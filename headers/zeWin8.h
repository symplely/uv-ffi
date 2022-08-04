#define FFI_SCOPE "__zend__"
#define FFI_LIB "php8.dll"

typedef struct _IO_FILE __FILE;
typedef struct _IO_FILE FILE;
typedef long int __off_t;
typedef long int __off64_t;

typedef enum {
  SUCCESS = 0,
  FAILURE = -1,
} ZEND_RESULT_CODE;

typedef struct {
	void *ptr;
	uint32_t type_mask;

} zend_type;

typedef ZEND_RESULT_CODE zend_result;
typedef intptr_t zend_intptr_t;
typedef uintptr_t zend_uintptr_t;
typedef bool zend_bool;
typedef unsigned char zend_uchar;
typedef int64_t zend_long;
typedef uint64_t zend_ulong;
typedef int64_t zend_off_t;

typedef struct _zend_refcounted_h {
	uint32_t refcount;
	union {
		uint32_t type_info;
	} u;
} zend_refcounted_h;

struct _zend_string {
    zend_refcounted_h gc;
    zend_ulong        h;                /* hash value */
    size_t            len;
    char              val[1];
};

typedef struct _zend_string zend_string;
struct _IO_marker;
struct _IO_codecvt;
struct _IO_wide_data;
typedef void _IO_lock_t;
struct _IO_FILE
{
  int _flags;
  char *_IO_read_ptr;
  char *_IO_read_end;
  char *_IO_read_base;
  char *_IO_write_base;
  char *_IO_write_ptr;
  char *_IO_write_end;
  char *_IO_buf_base;
  char *_IO_buf_end;
  char *_IO_save_base;
  char *_IO_backup_base;
  char *_IO_save_end;
  struct _IO_marker *_markers;
  struct _IO_FILE *_chain;
  int _fileno;
  int _flags2;
  __off_t _old_offset;
  unsigned short _cur_column;
  signed char _vtable_offset;
  char _shortbuf[1];
  _IO_lock_t *_lock;
  __off64_t _offset;
  struct _IO_codecvt *_codecvt;
  struct _IO_wide_data *_wide_data;
  struct _IO_FILE *_freeres_list;
  void *_freeres_buf;
  size_t __pad5;
  int _mode;
  char _unused2[15 * sizeof (int) - 4 * sizeof (void *) - sizeof (size_t)];
};

typedef size_t (*zend_stream_fsizer_t)(void* handle);
typedef ssize_t (*zend_stream_reader_t)(void* handle, char *buf, size_t len);
typedef void   (*zend_stream_closer_t)(void* handle);

typedef struct _zend_stream {
    void        *handle;
    int         isatty;
    zend_stream_reader_t   reader;
    zend_stream_fsizer_t   fsizer;
    zend_stream_closer_t   closer;
} zend_stream;

typedef enum {
    ZEND_HANDLE_FILENAME,
    ZEND_HANDLE_FP,
    ZEND_HANDLE_STREAM
} zend_stream_type;

typedef struct _zend_file_handle {
    union {
        FILE          *fp;
        zend_stream   stream;
    } handle;
    const char      *filename;
    zend_string       *opened_path;
    zend_stream_type  type;
    /* free_filename is used by wincache */
    /* TODO: Clean up filename vs opened_path mess */
    zend_bool         free_filename;
    char              *buf;
    size_t            len;
} zend_file_handle;

typedef int (*zend_stream_open_function_func_t)(const char *filename, zend_file_handle *handle);
extern zend_stream_open_function_func_t zend_stream_open_function;

struct _zend_refcounted {
	zend_refcounted_h gc;
};

struct _zend_resource {
	zend_refcounted_h gc;
	int handle;
	int type;
	void *ptr;
};

typedef struct _zend_resource zend_resource;
typedef void (*rsrc_dtor_func_t)(zend_resource *res);

typedef struct _zend_rsrc_list_dtors_entry {
	rsrc_dtor_func_t list_dtor_ex;
	rsrc_dtor_func_t plist_dtor_ex;

	const char *type_name;

	int module_number;
	int resource_id;
} zend_rsrc_list_dtors_entry;

typedef struct _zend_refcounted zend_refcounted;
typedef struct _zend_object_handlers zend_object_handlers;
typedef struct _zend_array HashTable;
typedef struct _zend_array zend_array;
typedef struct _zend_object zend_object;
typedef struct _zend_resource zend_resource;
typedef struct _zend_reference zend_reference;
typedef struct _zend_ast_ref zend_ast_ref;
typedef struct _zval_struct zval;
typedef struct _zend_class_entry zend_class_entry;
typedef union _zend_function zend_function;
typedef struct _zend_op_array zend_op_array;
typedef struct _zend_op zend_op;
typedef struct _zend_execute_data zend_execute_data;
typedef void (*zif_handler)(zend_execute_data *execute_data, zval *return_value);

typedef union _zend_value {
	zend_long lval;
	double dval;
	zend_refcounted *counted;
	zend_string *str;
	zend_array *arr;
	zend_object *obj;
	zend_resource *res;
	zend_reference *ref;
	zend_ast_ref *ast;
	zval *zv;
	void *ptr;
	zend_class_entry *ce;
	zend_function *func;
	struct {
		uint32_t w1;
		uint32_t w2;
	} ww;
} zend_value;

struct _zval_struct {
	zend_value value;
	union {
		struct {
			zend_uchar type;
			zend_uchar type_flags;
			union {
				uint16_t extra;
			} u;
		} v;
		uint32_t type_info;
	} u1;
	union {
		uint32_t next;
		uint32_t cache_slot;
		uint32_t opline_num;
		uint32_t lineno;
		uint32_t num_args;
		uint32_t fe_pos;
		uint32_t fe_iter_idx;
		uint32_t access_flags;
		uint32_t property_guard;
		uint32_t constant_flags;
		uint32_t extra;
	} u2;
};

typedef struct _Bucket {
	zval              val;
	zend_ulong        h;                /* hash value (or numeric index)   */
	zend_string      *key;              /* string key or NULL for numerics */
} Bucket;

typedef void (*dtor_func_t)(zval *pDest);
struct _zend_array {
	zend_refcounted_h gc;
	union {
		struct {
			zend_uchar flags; zend_uchar _unused; zend_uchar nIteratorsCount; zend_uchar _unused2;
		} v;
		uint32_t flags;
	} u;
	uint32_t nTableMask;
	Bucket *arData;
	uint32_t nNumUsed;
	uint32_t nNumOfElements;
	uint32_t nTableSize;
	uint32_t nInternalPointer;
	zend_long nNextFreeElement;
	dtor_func_t pDestructor;
};

typedef struct _zend_property_info {
	uint32_t offset; /* property offset for object properties or
	                      property index for static properties */
	uint32_t flags;
	zend_string *name;
	zend_string *doc_comment;
	HashTable *attributes;
	zend_class_entry *ce;
	zend_type type;
} zend_property_info;

typedef struct {
	size_t num;
	size_t num_allocated;
	struct _zend_property_info *ptr[1];
} zend_property_info_list;

typedef union {
	struct _zend_property_info *ptr;
	uintptr_t list;
} zend_property_info_source_list;

struct _zend_reference {
	zend_refcounted_h gc;
	zval val;
	zend_property_info_source_list sources;
};

struct _zend_ast_ref {
	zend_refcounted_h gc;
};

struct _zend_object {
	zend_refcounted_h gc;
	uint32_t handle;
	zend_class_entry *ce;
	const zend_object_handlers *handlers;
	HashTable *properties;
	zval properties_table[1];
};

typedef zval *(*zend_object_read_property_t)(zend_object *object, zend_string *member, int type, void **cache_slot, zval *rv);
typedef zval *(*zend_object_read_dimension_t)(zend_object *object, zval *offset, int type, zval *rv);
typedef zval *(*zend_object_write_property_t)(zend_object *object, zend_string *member, zval *value, void **cache_slot);
typedef void (*zend_object_write_dimension_t)(zend_object *object, zval *offset, zval *value);
typedef zval *(*zend_object_get_property_ptr_ptr_t)(zend_object *object, zend_string *member, int type, void **cache_slot);
typedef int (*zend_object_has_property_t)(zend_object *object, zend_string *member, int has_set_exists, void **cache_slot);
typedef int (*zend_object_has_dimension_t)(zend_object *object, zval *member, int check_empty);
typedef void (*zend_object_unset_property_t)(zend_object *object, zend_string *member, void **cache_slot);
typedef void (*zend_object_unset_dimension_t)(zend_object *object, zval *offset);
typedef HashTable *(*zend_object_get_properties_t)(zend_object *object);
typedef HashTable *(*zend_object_get_debug_info_t)(zend_object *object, int *is_temp);

typedef enum _zend_prop_purpose {
 ZEND_PROP_PURPOSE_DEBUG,
 ZEND_PROP_PURPOSE_ARRAY_CAST,
 ZEND_PROP_PURPOSE_SERIALIZE,
 ZEND_PROP_PURPOSE_VAR_EXPORT,
 ZEND_PROP_PURPOSE_JSON,
 _ZEND_PROP_PURPOSE_NON_EXHAUSTIVE_ENUM
} zend_prop_purpose;

typedef zend_array *(*zend_object_get_properties_for_t)(zend_object *object, zend_prop_purpose purpose);
typedef zend_function *(*zend_object_get_method_t)(zend_object **object, zend_string *method, const zval *key);
typedef zend_function *(*zend_object_get_constructor_t)(zend_object *object);
typedef void (*zend_object_dtor_obj_t)(zend_object *object);
typedef void (*zend_object_free_obj_t)(zend_object *object);
typedef zend_object* (*zend_object_clone_obj_t)(zend_object *object);
typedef zend_string *(*zend_object_get_class_name_t)(const zend_object *object);
typedef int (*zend_object_compare_t)(zval *object1, zval *object2);
typedef int (*zend_object_cast_t)(zend_object *readobj, zval *retval, int type);
typedef int (*zend_object_count_elements_t)(zend_object *object, zend_long *count);
typedef int (*zend_object_get_closure_t)(zend_object *obj, zend_class_entry **ce_ptr, zend_function **fptr_ptr, zend_object **obj_ptr, zend_bool check_only);
typedef HashTable *(*zend_object_get_gc_t)(zend_object *object, zval **table, int *n);
typedef int (*zend_object_do_operation_t)(zend_uchar opcode, zval *result, zval *op1, zval *op2);

struct _zend_object_handlers {
 int offset;
 zend_object_free_obj_t free_obj;
 zend_object_dtor_obj_t dtor_obj;
 zend_object_clone_obj_t clone_obj;
 zend_object_read_property_t read_property;
 zend_object_write_property_t write_property;
 zend_object_read_dimension_t read_dimension;
 zend_object_write_dimension_t write_dimension;
 zend_object_get_property_ptr_ptr_t get_property_ptr_ptr;
 zend_object_has_property_t has_property;
 zend_object_unset_property_t unset_property;
 zend_object_has_dimension_t has_dimension;
 zend_object_unset_dimension_t unset_dimension;
 zend_object_get_properties_t get_properties;
 zend_object_get_method_t get_method;
 zend_object_get_constructor_t get_constructor;
 zend_object_get_class_name_t get_class_name;
 zend_object_cast_t cast_object;
 zend_object_count_elements_t count_elements;
 zend_object_get_debug_info_t get_debug_info;
 zend_object_get_closure_t get_closure;
 zend_object_get_gc_t get_gc;
 zend_object_do_operation_t do_operation;
 zend_object_compare_t compare;
 zend_object_get_properties_for_t get_properties_for;
};

/* arg_info for internal functions */
typedef struct _zend_internal_arg_info {
	const char *name;
	zend_type type;
	const char *default_value;
} zend_internal_arg_info;

typedef struct {
	uint32_t num_types;
	zend_type types[1];
} zend_type_list;

typedef struct _zend_arg_info {
 zend_string *name;
 zend_type type;
 zend_string *default_value;
} zend_arg_info;

typedef struct _zend_internal_function {
	/* Common elements */
	zend_uchar type;
	zend_uchar arg_flags[3]; /* bitset of arg_info.pass_by_reference */
	uint32_t fn_flags;
	zend_string* function_name;
	zend_class_entry *scope;
	zend_function *prototype;
	uint32_t num_args;
	uint32_t required_num_args;
	zend_internal_arg_info *arg_info;
	HashTable *attributes;
	/* END of common elements */

	zif_handler handler;
	struct _zend_module_entry *module;
	void *reserved[6];
} zend_internal_function;

typedef struct _zend_internal_function_info {
 zend_uintptr_t required_num_args;
 zend_type type;
 const char *default_value;
} zend_internal_function_info;

typedef struct _zend_live_range {
 uint32_t var;
 uint32_t start;
 uint32_t end;
} zend_live_range;

typedef struct _zend_try_catch_element {
 uint32_t try_op;
 uint32_t catch_op;
 uint32_t finally_op;
 uint32_t finally_end;
} zend_try_catch_element;

struct _zend_op_array {
 zend_uchar type;
 zend_uchar arg_flags[3];
 uint32_t fn_flags;
 zend_string *function_name;
 zend_class_entry *scope;
 zend_function *prototype;
 uint32_t num_args;
 uint32_t required_num_args;
 zend_arg_info *arg_info;
 HashTable *attributes;
 int cache_size;
 int last_var;
 uint32_t T;
 uint32_t last;
 zend_op *opcodes;
 void ** * run_time_cache__ptr;
 HashTable * * static_variables_ptr__ptr;
 HashTable *static_variables;
 zend_string **vars;
 uint32_t *refcount;
 int last_live_range;
 int last_try_catch;
 zend_live_range *live_range;
 zend_try_catch_element *try_catch_array;
 zend_string *filename;
 uint32_t line_start;
 uint32_t line_end;
 zend_string *doc_comment;
 int last_literal;
 zval *literals;
 void *reserved[6];
};

struct _zend_execute_data {
 const zend_op *opline;
 zend_execute_data *call;
 zval *return_value;
 zend_function *func;
 zval This;
 zend_execute_data *prev_execute_data;
 zend_array *symbol_table;
 void **run_time_cache;
 zend_array *extra_named_params;
};

typedef union _znode_op {
	uint32_t      constant;
	uint32_t      var;
	uint32_t      num;
	uint32_t      opline_num; /*  Needs to be signed */
	uint32_t      jmp_offset;
	zval          *zv;
} znode_op;

typedef struct _znode { /* used only during compilation */
	zend_uchar op_type;
	zend_uchar flag;
	union {
		znode_op op;
		zval constant; /* replaced by literal/zv */
	} u;
} znode;

struct _zend_op {
 const void *handler;
 znode_op op1;
 znode_op op2;
 znode_op result;
 uint32_t extended_value;
 uint32_t lineno;
 zend_uchar opcode;
 zend_uchar op1_type;
 zend_uchar op2_type;
 zend_uchar result_type;
};

union _zend_function {
	zend_uchar type;	/* MUST be the first element of this struct! */
	uint32_t   quick_arg_flags;

	struct {
		zend_uchar type;  /* never used */
		zend_uchar arg_flags[3]; /* bitset of arg_info.pass_by_reference */
		uint32_t fn_flags;
		zend_string *function_name;
		zend_class_entry *scope;
		zend_function *prototype;
		uint32_t num_args;
		uint32_t required_num_args;
		zend_arg_info *arg_info;  /* index -1 represents the return value info, if any */
		HashTable   *attributes;
	} common;

	zend_op_array op_array;
	zend_internal_function internal_function;
};

typedef struct _zend_class_name {
 zend_string *name;
 zend_string *lc_name;
} zend_class_name;

typedef struct _zend_object_iterator zend_object_iterator;
typedef struct _zend_object_iterator_funcs {
 void (*dtor)(zend_object_iterator *iter);
 int (*valid)(zend_object_iterator *iter);
 zval *(*get_current_data)(zend_object_iterator *iter);
 void (*get_current_key)(zend_object_iterator *iter, zval *key);
 void (*move_forward)(zend_object_iterator *iter);
 void (*rewind)(zend_object_iterator *iter);
 void (*invalidate_current)(zend_object_iterator *iter);
 HashTable *(*get_gc)(zend_object_iterator *iter, zval **table, int *n);
} zend_object_iterator_funcs;

struct _zend_object_iterator {
 zend_object std;
 zval data;
 const zend_object_iterator_funcs *funcs;
 zend_ulong index;
};

typedef struct _zend_class_iterator_funcs {
 zend_function *zf_new_iterator;
 zend_function *zf_valid;
 zend_function *zf_current;
 zend_function *zf_key;
 zend_function *zf_next;
 zend_function *zf_rewind;
} zend_class_iterator_funcs;

struct _zend_serialize_data;
struct _zend_unserialize_data;
typedef struct _zend_serialize_data zend_serialize_data;
typedef struct _zend_unserialize_data zend_unserialize_data;

typedef struct _zend_function_entry {
	const char *fname;
	zif_handler handler;
	const struct _zend_internal_arg_info *arg_info;
	uint32_t num_args;
	uint32_t flags;
} zend_function_entry;

typedef struct _zend_trait_method_reference {
 zend_string *method_name;
 zend_string *class_name;
} zend_trait_method_reference;

typedef struct _zend_trait_precedence {
 zend_trait_method_reference trait_method;
 uint32_t num_excludes;
 zend_string *exclude_class_names[1];
} zend_trait_precedence;

typedef struct _zend_trait_alias {
 zend_trait_method_reference trait_method;
 zend_string *alias;
 uint32_t modifiers;
} zend_trait_alias;

struct _zend_class_entry {
	char type;
	zend_string *name;
	/* class_entry or string depending on ZEND_ACC_LINKED */
	union {
		zend_class_entry *parent;
		zend_string *parent_name;
	};
	int refcount;
	uint32_t ce_flags;

	int default_properties_count;
	int default_static_members_count;
	zval *default_properties_table;
	zval *default_static_members_table;
  zval **static_members_table__ptr;
	HashTable function_table;
	HashTable properties_info;
	HashTable constants_table;

	struct _zend_property_info **properties_info_table;

	zend_function *constructor;
	zend_function *destructor;
	zend_function *clone;
	zend_function *__get;
	zend_function *__set;
	zend_function *__unset;
	zend_function *__isset;
	zend_function *__call;
	zend_function *__callstatic;
	zend_function *__tostring;
	zend_function *__debugInfo;
	zend_function *__serialize;
	zend_function *__unserialize;

	/* allocated only if class implements Iterator or IteratorAggregate interface */
	zend_class_iterator_funcs *iterator_funcs_ptr;

	/* handlers */
	union {
		zend_object* (*create_object)(zend_class_entry *class_type);
		int (*interface_gets_implemented)(zend_class_entry *iface, zend_class_entry *class_type); /* a class implements this interface */
	};
	zend_object_iterator *(*get_iterator)(zend_class_entry *ce, zval *object, int by_ref);
	zend_function *(*get_static_method)(zend_class_entry *ce, zend_string* method);

	/* serializer callbacks */
	int (*serialize)(zval *object, unsigned char **buffer, size_t *buf_len, zend_serialize_data *data);
	int (*unserialize)(zval *object, zend_class_entry *ce, const unsigned char *buf, size_t buf_len, zend_unserialize_data *data);

	uint32_t num_interfaces;
	uint32_t num_traits;

	/* class_entry or string(s) depending on ZEND_ACC_LINKED */
	union {
		zend_class_entry **interfaces;
		zend_class_name *interface_names;
	};

	zend_class_name *trait_names;
	zend_trait_alias **trait_aliases;
	zend_trait_precedence **trait_precedences;
	HashTable *attributes;

	union {
		struct {
			zend_string *filename;
			uint32_t line_start;
			uint32_t line_end;
			zend_string *doc_comment;
		} user;
		struct {
			const struct _zend_function_entry *builtin_functions;
			struct _zend_module_entry *module;
		} internal;
	} info;
};

struct _zend_ini_entry;
typedef struct _zend_ini_entry zend_ini_entry;
struct _zend_module_dep {
	const char *name;
	const char *rel;
	const char *version;
	unsigned char type;
};

typedef struct _zend_module_dep zend_module_dep;
typedef struct _zend_module_entry zend_module_entry;

struct _zend_module_entry {
	unsigned short size;
	unsigned int zend_api;
	unsigned char zend_debug;
	unsigned char zts;
	const struct _zend_ini_entry *ini_entry;
	const struct _zend_module_dep *deps;
	const char *name;
	const struct _zend_function_entry *functions;
	zend_result (*module_startup_func)(int type, int module_number);
	zend_result (*module_shutdown_func)(int type, int module_number);
	zend_result (*request_startup_func)(int type, int module_number);
	zend_result (*request_shutdown_func)(int type, int module_number);
	void (*info_func)(zend_module_entry *zend_module);
	const char *version;
	size_t globals_size;
	void* globals_ptr;
	void (*globals_ctor)(void *global);
	void (*globals_dtor)(void *global);
	zend_result (*post_deactivate_func)(void);
	int module_started;
	unsigned char type;
	void *handle;
	int module_number;
	const char *build_id;
};


typedef struct _zend_stack {
 int size, top, max;
 void *elements;
} zend_stack;

typedef struct _zend_llist_element {
	struct _zend_llist_element *next;
	struct _zend_llist_element *prev;
	char data[1]; /* Needs to always be last in the struct */
} zend_llist_element;

typedef void (*llist_dtor_func_t)(void *);
typedef int (*llist_compare_func_t)(const zend_llist_element **, const zend_llist_element **);
typedef void (*llist_apply_with_args_func_t)(void *data, int num_args, va_list args);
typedef void (*llist_apply_with_arg_func_t)(void *data, void *arg);
typedef void (*llist_apply_func_t)(void *);

typedef struct _zend_llist {
	zend_llist_element *head;
	zend_llist_element *tail;
	size_t count;
	size_t size;
	llist_dtor_func_t dtor;
	unsigned char persistent;
	zend_llist_element *traverse_ptr;
} zend_llist;

typedef zend_llist_element* zend_llist_position;

typedef void (*zend_ini_parser_cb_t)(zval *arg1, zval *arg2, zval *arg3, int callback_type, void *arg);
typedef struct _zend_ini_parser_param {
	zend_ini_parser_cb_t ini_parser_cb;
	void *arg;
} zend_ini_parser_param;

typedef struct _zend_brk_cont_element {
	int start;
	int cont;
	int brk;
	int parent;
	zend_bool is_switch;
} zend_brk_cont_element;

/* Compilation context that is different for each op array. */
typedef struct _zend_oparray_context {
	uint32_t   opcodes_size;
	int        vars_size;
	int        literals_size;
	uint32_t   fast_call_var;
	uint32_t   try_catch_offset;
	int        current_brk_cont;
	int        last_brk_cont;
	zend_brk_cont_element *brk_cont_array;
	HashTable *labels;
} zend_oparray_context;

typedef struct _zend_declarables {
	zend_long ticks;
} zend_declarables;

/* Compilation context that is different for each file, but shared between op arrays. */
typedef struct _zend_file_context {
	zend_declarables declarables;

	zend_string *current_namespace;
	zend_bool in_namespace;
	zend_bool has_bracketed_namespaces;

	HashTable *imports;
	HashTable *imports_function;
	HashTable *imports_const;

	HashTable seen_symbols;
} zend_file_context;

typedef struct _zend_arena zend_arena;

struct _zend_arena {
	char		*ptr;
	char		*end;
	zend_arena  *prev;
};

typedef struct _zend_encoding zend_encoding;
typedef uint16_t zend_ast_kind;
typedef uint16_t zend_ast_attr;
typedef struct _zend_ast zend_ast;
typedef struct _zend_compiler_globals zend_compiler_globals;

struct _zend_ast {
 zend_ast_kind kind;
 zend_ast_attr attr;
 uint32_t lineno;
 zend_ast *child[1];
};

struct _zend_compiler_globals {
	zend_stack loop_var_stack;

	zend_class_entry *active_class_entry;

	zend_string *compiled_filename;

	int zend_lineno;

	zend_op_array *active_op_array;

	HashTable *function_table;	/* function symbol table */
	HashTable *class_table;		/* class table */

	HashTable *auto_globals;

	/* Refer to zend_yytnamerr() in zend_language_parser.y for meaning of values */
	zend_uchar parse_error;
	zend_bool in_compilation;
	zend_bool short_tags;

	zend_bool unclean_shutdown;

	zend_bool ini_parser_unbuffered_errors;

	zend_llist open_files;

	struct _zend_ini_parser_param *ini_parser_param;

	zend_bool skip_shebang;
	zend_bool increment_lineno;

	zend_string *doc_comment;
	uint32_t extra_fn_flags;

	uint32_t compiler_options; /* set of ZEND_COMPILE_* constants */

	zend_oparray_context context;
	zend_file_context file_context;

	zend_arena *arena;

	HashTable interned_strings;

	const zend_encoding **script_encoding_list;
	size_t script_encoding_list_size;
	zend_bool multibyte;
	zend_bool detect_unicode;
	zend_bool encoding_declared;

	zend_ast *ast;
	zend_arena *ast_arena;

	zend_stack delayed_oplines_stack;
	HashTable *memoized_exprs;
	int memoize_mode;

	void   *map_ptr_base;
	size_t  map_ptr_size;
	size_t  map_ptr_last;

	HashTable *delayed_variance_obligations;
	HashTable *delayed_autoloads;

	uint32_t rtd_key_counter;

	zend_stack short_circuiting_opnums;
};

typedef struct __pthread_internal_list
{
  struct __pthread_internal_list *__prev;
  struct __pthread_internal_list *__next;
} __pthread_list_t;

struct __pthread_mutex_s
{
  int __lock;
  unsigned int __count;
  int __owner;
  unsigned int __nusers;
  int __kind;
  short __spins;
  short __elision;
  __pthread_list_t __list;
};

typedef union
{
  struct __pthread_mutex_s __data;
  char __size[40];
  long int __align;
} pthread_mutex_t;

typedef pthread_mutex_t   mutex_t;

typedef struct _zend_executor_globals zend_executor_globals;

typedef long int __jmp_buf[8];

typedef struct
{
  unsigned long int __val[(1024 / (8 * sizeof (unsigned long int)))];
} __sigset_t;
typedef __sigset_t sigset_t;

struct __jmp_buf_tag
  {
    __jmp_buf __jmpbuf;
    int __mask_was_saved;
    __sigset_t __saved_mask;
  };

typedef struct __jmp_buf_tag jmp_buf[1];
typedef struct __jmp_buf_tag sigjmp_buf[1];
typedef struct _zend_vm_stack *zend_vm_stack;
typedef uint32_t HashPosition;

struct _zend_vm_stack {
 zval *top;
 zval *end;
 zend_vm_stack prev;
};

typedef enum {
 EH_NORMAL = 0,
 EH_THROW
} zend_error_handling_t;

typedef struct {
 zend_error_handling_t handling;
 zend_class_entry *exception;
} zend_error_handling;

typedef struct _zend_objects_store {
 zend_object **object_buckets;
 uint32_t top;
 uint32_t size;
 int free_list_head;
} zend_objects_store;

typedef struct _HashTableIterator {
 HashTable *ht;
 HashPosition pos;
} HashTableIterator;

typedef struct {
 zval *cur;
 zval *end;
 zval *start;
} zend_get_gc_buffer;

typedef struct _OSVERSIONINFOEXA {
    uint32_t dwOSVersionInfoSize;
    uint32_t dwMajorVersion;
    uint32_t dwMinorVersion;
    uint32_t dwBuildNumber;
    uint32_t dwPlatformId;
    char  szCSDVersion[128];
    uint16_t  wServicePackMajor;
    uint16_t  wServicePackMinor;
    uint16_t  wSuiteMask;
    char  wProductType;
    char  wReserved;
} OSVERSIONINFOEX;

struct _zend_executor_globals {
 zval uninitialized_zval;
 zval error_zval;
 zend_array *symtable_cache[32];
 zend_array **symtable_cache_limit;
 zend_array **symtable_cache_ptr;
 zend_array symbol_table;
 HashTable included_files;
 jmp_buf *bailout;
 int error_reporting;
 int exit_status;
 HashTable *function_table;
 HashTable *class_table;
 HashTable *zend_constants;
 zval *vm_stack_top;
 zval *vm_stack_end;
 zend_vm_stack vm_stack;
 size_t vm_stack_page_size;
 struct _zend_execute_data *current_execute_data;
 zend_class_entry *fake_scope;
 uint32_t jit_trace_num;
 zend_long precision;
 int ticks_count;
 uint32_t persistent_constants_count;
 uint32_t persistent_functions_count;
 uint32_t persistent_classes_count;
 HashTable *in_autoload;
 zend_bool full_tables_cleanup;
 zend_bool no_extensions;
 zend_bool vm_interrupt;
 zend_bool timed_out;
 zend_long hard_timeout;
 OSVERSIONINFOEX windows_version_info;
 HashTable regular_list;
 HashTable persistent_list;
 int user_error_handler_error_reporting;
 zval user_error_handler;
 zval user_exception_handler;
 zend_stack user_error_handlers_error_reporting;
 zend_stack user_error_handlers;
 zend_stack user_exception_handlers;
 zend_error_handling_t error_handling;
 zend_class_entry *exception_class;
 zend_long timeout_seconds;
 int lambda_count;
 HashTable *ini_directives;
 HashTable *modified_ini_directives;
 zend_ini_entry *error_reporting_ini_entry;
 zend_objects_store objects_store;
 zend_object *exception, *prev_exception;
 const zend_op *opline_before_exception;
 zend_op exception_op[3];
 struct _zend_module_entry *current_module;
 zend_bool active;
 zend_uchar flags;
 zend_long assertions;
 uint32_t ht_iterators_count;
 uint32_t ht_iterators_used;
 HashTableIterator *ht_iterators;
 HashTableIterator ht_iterators_slots[16];
 void *saved_fpu_cw_ptr;
 zend_function trampoline;
 zend_op call_trampoline_op;
 HashTable weakrefs;
 zend_bool exception_ignore_args;
 zend_long exception_string_param_max_len;
 zend_get_gc_buffer get_gc_buffer;
 void *reserved[6];
};

extern const zend_object_handlers std_object_handlers;
const zend_internal_function zend_pass_function;
extern HashTable module_registry;
extern zend_executor_globals executor_globals;
struct _zend_compiler_globals compiler_globals; // function_table

typedef int (*user_opcode_handler_t) (zend_execute_data *execute_data);
typedef void (*opcode_handler_t) (void);

zend_result zend_parse_parameters(uint32_t num_args, const char *type_spec, ...);
void zend_set_function_arg_flags(zend_function *func);
zend_result zend_register_functions(zend_class_entry *scope, const zend_function_entry *functions, HashTable *function_table, int type);
void zend_unregister_functions(const zend_function_entry *functions, int count, HashTable *function_table);

int zend_register_list_destructors_ex(rsrc_dtor_func_t ld, rsrc_dtor_func_t pld, const char *type_name, int module_number);
zend_resource *zend_register_resource(void *rsrc_pointer, int rsrc_type);


void *zend_fetch_resource(zend_resource *res, const char *resource_type_name, int resource_type);
void *zend_fetch_resource2(zend_resource *res, const char *resource_type_name, int resource_type, int resource_type2);
void *zend_fetch_resource_ex(zval *res, const char *resource_type_name, int resource_type);
void *zend_fetch_resource2_ex(zval *res, const char *resource_type_name, int resource_type, int resource_type2);

int zend_set_user_opcode_handler(zend_uchar opcode, user_opcode_handler_t handler);
user_opcode_handler_t zend_get_user_opcode_handler(zend_uchar opcode);

void zval_ptr_dtor(zval *zval_ptr);
void zval_internal_ptr_dtor(zval *zvalue);
void zval_add_ref(zval *p);
zval *zend_get_zval_ptr(const zend_op *opline, int op_type, const znode_op *node, const zend_execute_data *execute_data);

zend_uchar zend_get_call_op(const zend_op *init_op, zend_function *fbc);
void object_init(zval *arg);
zend_result object_init_ex(zval *arg, zend_class_entry *ce);

typedef struct _php_stream php_stream;
php_stream *_php_stream_fopen_from_fd(int fd, const char *mode, const char *persistent_id, ...);

typedef struct _php_stream_wrapper php_stream_wrapper;
typedef struct _php_stream_context php_stream_context;
typedef struct stat zend_stat_t;
typedef unsigned long int __dev_t;
typedef unsigned int __uid_t;
typedef unsigned int __gid_t;
typedef unsigned long int __ino_t;
typedef unsigned long int __ino64_t;
typedef long int __time_t;
typedef unsigned int __mode_t;
typedef unsigned long int __nlink_t;
typedef long int __blksize_t;
typedef long int __blkcnt_t;
typedef long int __syscall_slong_t;
struct timespec
{
  __time_t tv_sec;
  __syscall_slong_t tv_nsec;
};
struct stat
  {
    __dev_t st_dev;
    __ino_t st_ino;
    __nlink_t st_nlink;
    __mode_t st_mode;
    __uid_t st_uid;
    __gid_t st_gid;
    int __pad0;
    __dev_t st_rdev;
    __off_t st_size;
    __blksize_t st_blksize;
    __blkcnt_t st_blocks;
    struct timespec st_atim;
    struct timespec st_mtim;
    struct timespec st_ctim;
    __syscall_slong_t __glibc_reserved[3];
  };

typedef struct _php_stream_notifier php_stream_notifier;
/* callback for status notifications */
typedef void (*php_stream_notification_func)(php_stream_context *context,
		int notifycode, int severity,
		char *xmsg, int xcode,
		size_t bytes_sofar, size_t bytes_max,
		void * ptr);

struct _php_stream_notifier {
	php_stream_notification_func func;
	void (*dtor)(php_stream_notifier *notifier);
	zval ptr;
	int mask;
	size_t progress, progress_max; /* position for progress notification */
};

struct _php_stream_context {
	php_stream_notifier *notifier;
	zval options;	/* hash keyed by wrapper family or specific wrapper */
	zend_resource *res;	/* used for auto-cleanup */
};

typedef struct _php_stream_statbuf {
	zend_stat_t sb; /* regular info */
	/* extended info to go here some day: content-type etc. etc. */
} php_stream_statbuf;

/* operations on streams that are file-handles */
typedef struct _php_stream_ops  {
	/* stdio like functions - these are mandatory! */
	ssize_t (*write)(php_stream *stream, const char *buf, size_t count);
	ssize_t (*read)(php_stream *stream, char *buf, size_t count);
	int    (*close)(php_stream *stream, int close_handle);
	int    (*flush)(php_stream *stream);

	const char *label; /* label for this ops structure */

	/* these are optional */
	int (*seek)(php_stream *stream, zend_off_t offset, int whence, zend_off_t *newoffset);
	int (*cast)(php_stream *stream, int castas, void **ret);
	int (*stat)(php_stream *stream, php_stream_statbuf *ssb);
	int (*set_option)(php_stream *stream, int option, int value, void *ptrparam);
} php_stream_ops;

typedef struct _php_stream_wrapper_ops {
	/* open/create a wrapped stream */
	php_stream *(*stream_opener)(php_stream_wrapper *wrapper, const char *filename, const char *mode,
			int options, zend_string **opened_path, php_stream_context *context , int __php_stream_call_depth,	const char *__zend_filename, const uint32_t __zend_lineno, const char *__zend_filename, const uint32_t __zend_lineno);
	/* close/destroy a wrapped stream */
	int (*stream_closer)(php_stream_wrapper *wrapper, php_stream *stream);
	/* stat a wrapped stream */
	int (*stream_stat)(php_stream_wrapper *wrapper, php_stream *stream, php_stream_statbuf *ssb);
	/* stat a URL */
	int (*url_stat)(php_stream_wrapper *wrapper, const char *url, int flags, php_stream_statbuf *ssb, php_stream_context *context);
	/* open a "directory" stream */
	php_stream *(*dir_opener)(php_stream_wrapper *wrapper, const char *filename, const char *mode,
			int options, zend_string **opened_path, php_stream_context *context , int __php_stream_call_depth,	const char *__zend_filename, const uint32_t __zend_lineno, const char *__zend_filename, const uint32_t __zend_lineno);

	const char *label;

	/* delete a file */
	int (*unlink)(php_stream_wrapper *wrapper, const char *url, int options, php_stream_context *context);

	/* rename a file */
	int (*rename)(php_stream_wrapper *wrapper, const char *url_from, const char *url_to, int options, php_stream_context *context);

	/* Create/Remove directory */
	int (*stream_mkdir)(php_stream_wrapper *wrapper, const char *url, int mode, int options, php_stream_context *context);
	int (*stream_rmdir)(php_stream_wrapper *wrapper, const char *url, int options, php_stream_context *context);
	/* Metadata handling */
	int (*stream_metadata)(php_stream_wrapper *wrapper, const char *url, int options, void *value, php_stream_context *context);
} php_stream_wrapper_ops;

struct _php_stream_wrapper	{
	const php_stream_wrapper_ops *wops;	/* operations the wrapper can perform */
	void *abstract;					/* context for the wrapper */
	int is_url;						/* so that PG(allow_url_fopen) can be respected */
};

typedef struct _php_stream_filter php_stream_filter;
typedef struct _php_stream_bucket php_stream_bucket;
typedef struct _php_stream_bucket_brigade	php_stream_bucket_brigade;

struct _php_stream_bucket {
	php_stream_bucket *next, *prev;
	php_stream_bucket_brigade *brigade;

	char *buf;
	size_t buflen;
	/* if non-zero, buf should be pefreed when the bucket is destroyed */
	uint8_t own_buf;
	uint8_t is_persistent;

	/* destroy this struct when refcount falls to zero */
	int refcount;
};

struct _php_stream_bucket_brigade {
	php_stream_bucket *head, *tail;
};

typedef enum {
	PSFS_ERR_FATAL,	/* error in data stream */
	PSFS_FEED_ME,	/* filter needs more data; stop processing chain until more is available */
	PSFS_PASS_ON	/* filter generated output buckets; pass them on to next in chain */
} php_stream_filter_status_t;

typedef struct _php_stream_filter_ops {

	php_stream_filter_status_t (*filter)(
			php_stream *stream,
			php_stream_filter *thisfilter,
			php_stream_bucket_brigade *buckets_in,
			php_stream_bucket_brigade *buckets_out,
			size_t *bytes_consumed,
			int flags
			);

	void (*dtor)(php_stream_filter *thisfilter);

	const char *label;

} php_stream_filter_ops;

typedef struct _php_stream_filter_chain {
	php_stream_filter *head, *tail;

	/* Owning stream */
	php_stream *stream;
} php_stream_filter_chain;

struct _php_stream_filter {
	const php_stream_filter_ops *fops;
	zval abstract; /* for use by filter implementation */
	php_stream_filter *next;
	php_stream_filter *prev;
	int is_persistent;

	/* link into stream and chain */
	php_stream_filter_chain *chain;

	/* buffered buckets */
	php_stream_bucket_brigade buffer;

	/* filters are auto_registered when they're applied */
	zend_resource *res;
};

struct _php_stream  {
	const php_stream_ops *ops;
	void *abstract;			/* convenience pointer for abstraction */

	php_stream_filter_chain readfilters, writefilters;

	php_stream_wrapper *wrapper; /* which wrapper was used to open the stream */
	void *wrapperthis;		/* convenience pointer for a instance of a wrapper */
	zval wrapperdata;		/* fgetwrapperdata retrieves this */

	uint8_t is_persistent:1;
	uint8_t in_free:2;			/* to prevent recursion during free */
	uint8_t eof:1;
	uint8_t __exposed:1;	/* non-zero if exposed as a zval somewhere */

	/* so we know how to clean it up correctly.  This should be set to
	 * PHP_STREAM_FCLOSE_XXX as appropriate */
	uint8_t fclose_stdiocast:2;

	uint8_t fgetss_state;		/* for fgetss to handle multiline tags */

	char mode[16];			/* "rwb" etc. ala stdio */

	uint32_t flags;	/* PHP_STREAM_FLAG_XXX */

	zend_resource *res;		/* used for auto-cleanup */
	FILE *stdiocast;    /* cache this, otherwise we might leak! */
	char *orig_path;

	zend_resource *ctx;

	/* buffer */
	zend_off_t position; /* of underlying stream */
	unsigned char *readbuf;
	size_t readbuflen;
	zend_off_t readpos;
	zend_off_t writepos;

	/* how much data to read when filling buffer */
	size_t chunk_size;

	struct _php_stream *enclosing_stream; /* this is a private stream owned by enclosing_stream */
}; /* php_stream */

int php_file_le_stream(void);
int php_file_le_pstream(void);
int php_file_le_stream_filter(void);
int _php_stream_cast(php_stream *stream, int castas, void **ret, int show_err);

__declspec(dllimport) HashTable* __vectorcall _zend_new_array(uint32_t size);
__declspec(dllimport) uint32_t zend_array_count(HashTable *ht);
__declspec(dllimport) HashTable* __vectorcall zend_new_pair(zval *val1, zval *val2);

__declspec(dllimport) int __vectorcall zend_hash_del(HashTable *ht, zend_string *key);
__declspec(dllimport) zval __vectorcall *zend_hash_find(const HashTable *ht, zend_string *key);
__declspec(dllimport) zval* __vectorcall zend_hash_str_find(const HashTable *ht, const char *key, size_t len);
__declspec(dllimport) zval __vectorcall *zend_hash_add_or_update(HashTable *ht, zend_string *key, zval *pData, uint32_t flag);

/* PHPAPI void php_error(int type, const char *format, ...); */
void php_error_docref(const char *docref, int type, const char *format, ...);
__declspec(dllimport) void zend_error(int type, const char *format, ...);

typedef unsigned int 	__uid_t;
typedef unsigned int 	__gid_t;
typedef __uid_t 		uid_t;
typedef __gid_t			gid_t;
typedef unsigned char   u_char;
typedef unsigned short  u_short;
typedef unsigned int    u_int;
typedef unsigned long   u_long;
typedef signed long int __int64;
typedef __int64	UINT_PTR;
typedef UINT_PTR SOCKET;
typedef SOCKET php_socket_t;
typedef php_socket_t uv_file;
typedef void	*PVOID;
typedef PVOID	HANDLE;
typedef HANDLE uv_os_fd_t;
typedef struct {
	php_socket_t	bsd_socket;
	int			type;
	int			error;
	int			blocking;
	zval		zstream;
	zend_object std;
} php_socket;

int _php_stream_free(php_stream *stream, int close_options);
php_stream *_php_stream_fopen_tmpfile(int dummy);
php_stream *_php_stream_fopen_from_pipe(FILE *file, const char *mode, ...);
php_stream *_php_stream_open_wrapper_ex(const char *path, const char *mode, int options, zend_string **opened_path, php_stream_context *context, ...);
ssize_t _php_stream_read(php_stream *stream, char *buf, size_t count);
ssize_t _php_stream_write(php_stream *stream, const char *buf, size_t count);
php_stream *_php_stream_fopen(const char *filename, const char *mode, zend_string **opened_path, int options, ...);
FILE * _php_stream_open_wrapper_as_file(char * path, char * mode, int options, zend_string **opened_path, ...);
ssize_t _php_stream_printf(php_stream *stream, const char *fmt, ...);

typedef struct fd_set {
        u_int   fd_count;               /* how many are SET? */
        SOCKET  fd_array[64];   /* an array of SOCKETs */
} fd_set;

int php_select(php_socket_t max_fd, fd_set *rfds, fd_set *wfds, fd_set *efds, struct timeval *tv);

extern php_stream_ops php_stream_stdio_ops;
extern php_stream_wrapper php_plain_files_wrapper;
/*
php_stream *_php_stream_fopen_tmpfile(int dummy);
php_stream *_php_stream_fopen_from_pipe(FILE *file, const char *mode, ...);
int _php_stream_seek(php_stream *stream, zend_off_t offset, int whence);
zend_off_t _php_stream_tell(php_stream *stream);
ssize_t _php_stream_read(php_stream *stream, char *buf, size_t count);
zend_string *php_stream_read_to_str(php_stream *stream, size_t len);
ssize_t _php_stream_write(php_stream *stream, const char *buf, size_t count);
int _php_stream_fill_read_buffer(php_stream *stream, size_t size);
ssize_t _php_stream_printf(php_stream *stream, const char *fmt, ...);
int _php_stream_eof(php_stream *stream);
int _php_stream_getc(php_stream *stream);

int _php_stream_putc(php_stream *stream, int c);

int _php_stream_flush(php_stream *stream, int closing);
char *_php_stream_get_line(php_stream *stream, char *buf, size_t maxlen, size_t *returned_len);
zend_string *php_stream_get_record(php_stream *stream, size_t maxlen, const char *delim, size_t delim_len);

// CAREFUL! this is equivalent to puts NOT fputs!
int _php_stream_puts(php_stream *stream, const char *buf);
int _php_stream_stat(php_stream *stream, php_stream_statbuf *ssb);

int _php_stream_stat_path(const char *path, int flags, php_stream_statbuf *ssb, php_stream_context *context);
int _php_stream_mkdir(const char *path, int mode, int options, php_stream_context *context);
int _php_stream_rmdir(const char *path, int options, php_stream_context *context);
php_stream *_php_stream_opendir(const char *path, int options, php_stream_context *context, ...); php_stream_dirent *_php_stream_readdir(php_stream *dirstream, php_stream_dirent *ent);

int php_stream_dirent_alphasort(const zend_string **a, const zend_string **b);
int php_stream_dirent_alphasortr(const zend_string **a, const zend_string **b);
int _php_stream_scandir(const char *dirname, zend_string **namelist[], int flags, php_stream_context *context, int (*compare) (const zend_string **a, const zend_string **b));
int _php_stream_set_option(php_stream *stream, int option, int value, void *ptrparam);
*/

typedef struct _sapi_module_struct sapi_module_struct;
extern sapi_module_struct sapi_module;  /* true global */

typedef struct {
	char *header;
	size_t header_len;
} sapi_header_struct;

typedef struct {
	zend_llist headers;
	int http_response_code;
	unsigned char send_default_content_type;
	char *mimetype;
	char *http_status_line;
} sapi_headers_struct;

typedef enum {					/* Parameter: 			*/
	SAPI_HEADER_REPLACE,		/* sapi_header_line* 	*/
	SAPI_HEADER_ADD,			/* sapi_header_line* 	*/
	SAPI_HEADER_DELETE,			/* sapi_header_line* 	*/
	SAPI_HEADER_DELETE_ALL,		/* void					*/
	SAPI_HEADER_SET_STATUS		/* int 					*/
} sapi_header_op_enum;

struct _sapi_module_struct {
	char *name;
	char *pretty_name;

	int (*startup)(struct _sapi_module_struct *sapi_module);
	int (*shutdown)(struct _sapi_module_struct *sapi_module);

	int (*activate)(void);
	int (*deactivate)(void);

	size_t (*ub_write)(const char *str, size_t str_length);
	void (*flush)(void *server_context);
	zend_stat_t *(*get_stat)(void);
	char *(*getenv)(const char *name, size_t name_len);

	void (*sapi_error)(int type, const char *error_msg, ...);

	int (*header_handler)(sapi_header_struct *sapi_header, sapi_header_op_enum op, sapi_headers_struct *sapi_headers);
	int (*send_headers)(sapi_headers_struct *sapi_headers);
	void (*send_header)(sapi_header_struct *sapi_header, void *server_context);

	size_t (*read_post)(char *buffer, size_t count_bytes);
	char *(*read_cookies)(void);

	void (*register_server_variables)(zval *track_vars_array);
	void (*log_message)(const char *message, int syslog_type_int);
	double (*get_request_time)(void);
	void (*terminate_process)(void);

	char *php_ini_path_override;

	void (*default_post_reader)(void);
	void (*treat_data)(int arg, char *str, zval *destArray);
	char *executable_location;

	int php_ini_ignore;
	int php_ini_ignore_cwd; /* don't look for php.ini in the current directory */

	int (*get_fd)(int *fd);

	int (*force_http_10)(void);

	int (*get_target_uid)(uid_t *);
	int (*get_target_gid)(gid_t *);

	unsigned int (*input_filter)(int arg, const char *var, char **val, size_t val_len, size_t *new_val_len);

	void (*ini_defaults)(HashTable *configuration_hash);
	int phpinfo_as_text;

	char *ini_entries;
	const zend_function_entry *additional_functions;
	unsigned int (*input_filter_init)(void);
};

typedef struct _zend_fcall_info {
	size_t size;
	zval function_name;
	zval *retval;
	zval *params;
	zend_object *object;
	uint32_t param_count;
	/* This hashtable can also contain positional arguments (with integer keys),
	 * which will be appended to the normal params[]. This makes it easier to
	 * integrate APIs like call_user_func_array(). The usual restriction that
	 * there may not be position arguments after named arguments applies. */
	HashTable *named_params;
} zend_fcall_info;

typedef struct _zend_fcall_info_cache {
	zend_function *function_handler;
	zend_class_entry *calling_scope;
	zend_class_entry *called_scope;
	zend_object *object;
} zend_fcall_info_cache;

extern const zend_fcall_info empty_fcall_info;
extern const zend_fcall_info_cache empty_fcall_info_cache;

/*
zend_result zend_startup_module(zend_module_entry *module_entry);
zend_module_entry* zend_register_internal_module(zend_module_entry *module_entry);
zend_module_entry* zend_register_module_ex(zend_module_entry *module);
zend_result zend_startup_module_ex(zend_module_entry *module);

size_t php_printf(const char *format, ...);
void php_info_print_table_start(void);
void php_info_print_table_header(int num_cols, ...);
void php_info_print_table_row(int num_cols, ...);
void php_info_print_table_end(void);
int php_request_startup(void);
int php_execute_script(zend_file_handle *primary_file);
void php_request_shutdown(void *dummy);
*/
