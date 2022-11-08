
#if defined(PTHREADS)
/* Thread local storage */
static pthread_key_t tls_key;
# define tsrm_tls_set(what)		pthread_setspecific(tls_key, (void*)(what))
# define tsrm_tls_get()			pthread_getspecific(tls_key)

#elif defined(TSRM_WIN32)
static DWORD tls_key;
# define tsrm_tls_set(what)		TlsSetValue(tls_key, (void*)(what))
# define tsrm_tls_get()			TlsGetValue(tls_key)

#else
# define tsrm_tls_set(what)
# define tsrm_tls_get()			NULL
# warning tsrm_set_interpreter_context is probably broken on this platform
#endif

typedef struct _tsrm_tls_entry tsrm_tls_entry;

struct _tsrm_tls_entry {
	void **storage;
	int count;
	THREAD_T thread_id;
	tsrm_tls_entry *next;
};

typedef struct {
	size_t size;
	ts_allocate_ctor ctor;
	ts_allocate_dtor dtor;
	int done;
} tsrm_resource_type;

/* The memory manager table */
static tsrm_tls_entry	**tsrm_tls_table=NULL;
static int				tsrm_tls_table_size;
static ts_rsrc_id		id_count;

/* The resource sizes table */
static tsrm_resource_type	*resource_types_table=NULL;
static int					resource_types_table_size;

static MUTEX_T tsmm_mutex;	/* thread-safe memory manager mutex */

/* New thread handlers */
static tsrm_thread_begin_func_t tsrm_new_thread_begin_handler;
static tsrm_thread_end_func_t tsrm_new_thread_end_handler;

static void allocate_new_resource(tsrm_tls_entry **thread_resources_ptr, THREAD_T thread_id)
{
	int i;

	TSRM_ERROR((TSRM_ERROR_LEVEL_CORE, "Creating data structures for thread %x", thread_id));
	(*thread_resources_ptr) = (tsrm_tls_entry *) malloc(sizeof(tsrm_tls_entry));
	(*thread_resources_ptr)->storage = (void **) malloc(sizeof(void *)*id_count);
	(*thread_resources_ptr)->count = id_count;
	(*thread_resources_ptr)->thread_id = thread_id;
	(*thread_resources_ptr)->next = NULL;

	/* Set thread local storage to this new thread resources structure */
	tsrm_tls_set(*thread_resources_ptr);

	if (tsrm_new_thread_begin_handler) {
		tsrm_new_thread_begin_handler(thread_id, &((*thread_resources_ptr)->storage));
	}
	for (i=0; i<id_count; i++) {
		if (resource_types_table[i].done) {
			(*thread_resources_ptr)->storage[i] = NULL;
		} else
		{
			(*thread_resources_ptr)->storage[i] = (void *) malloc(resource_types_table[i].size);
			if (resource_types_table[i].ctor) {
				resource_types_table[i].ctor((*thread_resources_ptr)->storage[i], &(*thread_resources_ptr)->storage);
			}
		}
	}

	if (tsrm_new_thread_end_handler) {
		tsrm_new_thread_end_handler(thread_id, &((*thread_resources_ptr)->storage));
	}

	tsrm_mutex_unlock(tsmm_mutex);
}

void *tsrm_set_interpreter_context(void *new_ctx)
{
	tsrm_tls_entry *current;

	current = tsrm_tls_get();

	/* TODO: unlink current from the global linked list, and replace it
	 * it with the new context, protected by mutex where/if appropriate */

	/* Set thread local storage to this new thread resources structure */
	tsrm_tls_set(new_ctx);

	/* return old context, so caller can restore it when they're done */
	return current;
}

/* allocates a new interpreter context */
void *tsrm_new_interpreter_context(void)
{
	tsrm_tls_entry *new_ctx, *current;
	THREAD_T thread_id;

	thread_id = tsrm_thread_id();
	tsrm_mutex_lock(tsmm_mutex);

	current = tsrm_tls_get();

	allocate_new_resource(&new_ctx, thread_id);

	/* switch back to the context that was in use prior to our creation
	 * of the new one */
	return tsrm_set_interpreter_context(current);
}

/* frees an interpreter context.  You are responsible for making sure that
 * it is not linked into the TSRM hash, and not marked as the current interpreter */
void tsrm_free_interpreter_context(void *context)
{
	tsrm_tls_entry *next, *thread_resources = (tsrm_tls_entry*)context;
	int i;

	while (thread_resources) {
		next = thread_resources->next;

		for (i=0; i<thread_resources->count; i++) {
			if (resource_types_table[i].dtor) {
				resource_types_table[i].dtor(thread_resources->storage[i], &thread_resources->storage);
			}
		}
		for (i=0; i<thread_resources->count; i++) {
			free(thread_resources->storage[i]);
		}
		free(thread_resources->storage);
		free(thread_resources);
		thread_resources = next;
	}
}

static int php_uv_do_callback3(zval **retval_ptr, php_uv_t *uv, zval ***params, int param_count, enum php_uv_callback_type type)
{
	int error = 0;
	zend_executor_globals *ZEG = NULL;
	void ***tsrm_ls, ***old;

	if (ZEND_FCI_INITIALIZED(uv->callback[type]->fci)) {
		tsrm_ls = tsrm_new_interpreter_context();
		old = tsrm_set_interpreter_context(tsrm_ls);

		PG(expose_php) = 0;
		PG(auto_globals_jit) = 0;

		php_request_startup(TSRMLS_C);
		ZEG = UV_EG_ALL(TSRMLS_C);
		ZEG->in_execution = 1;
		ZEG->current_execute_data=NULL;
		ZEG->current_module=phpext_uv_ptr;
		ZEG->This = NULL;

		uv->callback[type]->fci.params         = params;
		uv->callback[type]->fci.retval_ptr_ptr = retval_ptr;
		uv->callback[type]->fci.param_count    = param_count;
		uv->callback[type]->fci.no_separation  = 1;
		uv->callback[type]->fci.object_ptr = ZEG->This;
		uv->callback[type]->fcc.initialized = 1;

		uv->callback[type]->fcc.calling_scope = NULL;
		uv->callback[type]->fcc.called_scope = NULL;
		uv->callback[type]->fcc.object_ptr = ZEG->This;

		zend_try {
			if (zend_call_function(&uv->callback[type]->fci, &uv->callback[type]->fcc TSRMLS_CC) != SUCCESS) {
				error = -1;
			}

			if (retval_ptr != NULL) {
				zval_ptr_dtor(retval_ptr);
			}
		} zend_catch {
			error = -1;
		} zend_end_try();

		{
			zend_op_array *ops = &uv->callback[type]->fcc.function_handler->op_array;
			if (ops) {
					if (ops->run_time_cache) {
							efree(ops->run_time_cache);
							ops->run_time_cache = NULL;
					}
			}
		}

		php_request_shutdown(TSRMLS_C);
		tsrm_set_interpreter_context(old);
		tsrm_free_interpreter_context(tsrm_ls);
	}
