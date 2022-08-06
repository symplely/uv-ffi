<?php

/** @var callable (zend_resource *res) */
interface rsrc_dtor_func_t extends closure
{
}
interface user_opcode_handler_t extends closure
{
}

abstract class _zval_struct extends FFI\CData
{
}
abstract class zend_object extends FFI\CData
{
}
abstract class zend_string extends FFI\CData
{
}
abstract class Resource extends FFI\CData
{
}

/**
 * This class wraps PHP's `zend_string` structure, `string` instance, and provide an API for working with it
 *```c++
 * struct _zend_string {
 *   zend_refcounted_h gc;
 *   zend_ulong        h;                // hash value
 *   size_t            len;
 *   char              val[1];
 * };
 *```
 */
abstract class ZendString extends zend_string
{
}
abstract class zend_op extends FFI\CData
{
}
abstract class znode_op extends FFI\CData
{
}
abstract class zend_resource extends FFI\CData
{
}
abstract class _zend_array extends FFI\CData
{
}

/**
 * Class `HashTable` provides general access to the internal array objects, aka hash-table
 *```c++
 * struct _zend_array {
 *     zend_refcounted_h gc;
 *     union {
 *         struct {
 *             zend_uchar    flags;
 *             zend_uchar    _unused;
 *             zend_uchar    nIteratorsCount;
 *             zend_uchar    _unused2;
 *         } v;
 *         uint32_t flags;
 *     } u;
 *     uint32_t          nTableMask;
 *     Bucket           *arData;
 *     uint32_t          nNumUsed;
 *     uint32_t          nNumOfElements;
 *     uint32_t          nTableSize;
 *     uint32_t          nInternalPointer;
 *     zend_long         nNextFreeElement;
 *     dtor_func_t       pDestructor;
 * };
 *```
 */
abstract class HashTable extends _zend_array
{
}

/**
 * Class `ZendResource` represents a resource instance in PHP
 *```c++
 * struct _zend_resource {
 *     zend_refcounted_h gc;
 *     int               handle;
 *     int               type;
 *     void             *ptr;
 * };
 *```
 * @link https://github.com/php/php-src/blob/master/Zend/zend_types.h
 */
abstract class ZendResource extends zend_resource
{
}

/**
 * Class `TsHashTable` provides `Thread` general access to the internal array objects, aka hash-table
 *```c++
 * typedef struct _zend_ts_hashtable {
 *	HashTable hash;
 *	uint32_t reader;
 *	pthread_mutex_t *mx_reader;
 *	pthread_mutex_t *mx_writer;
 * } TsHashTable;
 *```
 */
abstract class TsHashTable extends FFI\CData
{
}
abstract class zend_reference extends FFI\CData
{
}
abstract class zend_function extends FFI\CData
{
}

abstract class zend_execute_data extends FFI\CData
{
}

/**
 * `ZendExecutor` provides information about current stack frame
 *```c++
 * typedef struct _zend_execute_data {
 *   const zend_op       *opline;           // executed opline
 *   zend_execute_data   *call;             // current call
 *   zval                *return_value;
 *   zend_function       *func;             // executed function
 *   zval                 This;             // this + call_info + num_args
 *   zend_execute_data   *prev_execute_data;
 *   zend_array          *symbol_table;
 *   void               **run_time_cache;   // cache op_array->run_time_cache
 * };
 *```
 */
abstract class ZendExecutor extends zend_execute_data
{
}

abstract class _zend_module_entry extends FFI\CData
{
}

/**
 * `ZendModule` provides information about hooking into and creating extensions
 *```c++
 * struct _zend_module_entry {
 *   unsigned short size;
 *   unsigned int zend_api;
 *   unsigned char zend_debug;
 *   unsigned char zts;
 *   const struct _zend_ini_entry *ini_entry;
 *   const struct _zend_module_dep *deps;
 *   const char *name;
 *   const struct _zend_function_entry *functions;
 *   int (*module_startup_func)(int type, int module_number);
 *   int (*module_shutdown_func)(int type, int module_number);
 *   int (*request_startup_func)(int type, int module_number);
 *   int (*request_shutdown_func)(int type, int module_number);
 *   void (*info_func)(zend_module_entry *zend_module);
 *   const char *version;
 *   size_t globals_size;
 * #ifdef ZTS
 *   ts_rsrc_id* globals_id_ptr;
 * #else
 *   void* globals_ptr;
 * #endif
 *   void (*globals_ctor)(void *global);
 *   void (*globals_dtor)(void *global);
 *   int (*post_deactivate_func)(void);
 *   int module_started;
 *   unsigned char type;
 *   void *handle;
 *   int module_number;
 *   const char *build_id;
 * };
 *```
 */
abstract class ZendModule extends zend_module_entry
{
}

abstract class zend_module_entry extends _zend_module_entry
{
}

abstract class php_stream_context extends FFI\CData
{
}

abstract class _zend_closure extends FFI\CData
{
}

/**
 * `ZendClosure` represents an closure instance in PHP
 *```c++
 * typedef struct _zend_closure {
 *   zend_object       std;
 *   zend_function     func;
 *   zval              this_ptr;
 *   zend_class_entry *called_scope;
 *   zif_handler       orig_internal_handler;
 * } zend_closure;
 *```
 */
abstract class ZendClosure extends zend_closure
{
}

abstract class zend_closure extends _zend_closure
{
}

/**
 *```c++
 *typedef struct _zend_objects_store {
 * zend_object **object_buckets;
 * uint32_t top;
 * uint32_t size;
 * int free_list_head;
 *} zend_objects_store;
 *```
 */
abstract class ZendObjectsStore extends zend_objects_store
{
}
abstract class zend_objects_store extends _zend_objects_store
{
}
abstract class _zend_objects_store extends FFI\CData
{
}

abstract class _zend_object extends FFI\CData
{
}
/**
 * `ZendObject` represents an object instance in PHP
 *```c++
 * struct _zend_object {
 *   zend_refcounted_h gc;
 *   uint32_t          handle;
 *   zend_class_entry *ce;
 *   const zend_object_handlers *handlers;
 *   HashTable        *properties;
 *   zval              properties_table[1];
 * };
 *```
 */
abstract class ZendObject extends _zend_object
{
}

abstract class _php_stream extends FFI\CData
{
}

abstract class php_stream extends _php_stream
{
}

/**
 * Class `ZendReference` represents a reference instance in PHP
 *```c++
 * struct _zend_reference {
 *     zend_refcounted_h              gc;
 *     zval                           val;
 *     zend_property_info_source_list sources;
 * };
 *```
 */
abstract class ZendReference extends zend_reference
{
}

abstract class ZendFunction extends zend_function
{
}
abstract class FILE extends FFI\CData
{
}

/**
 * Class `Zval` represents a value in PHP
 *```c++
 * struct _zval_struct {
 *   zend_value        value;            // value
 *   union {
 *     struct {
 *       zend_uchar    type;            // active type
 *       zend_uchar    type_flags;
 *       union {
 *         uint16_t  extra;        // not further specified
 *       } u;
 *     } v;
 *     uint32_t type_info;
 *   } u1;
 *   union {
 *     uint32_t     next;                 // hash collision chain
 *     uint32_t     cache_slot;           // cache slot (for RECV_INIT)
 *     uint32_t     opline_num;           // opline number (for FAST_CALL)
 *     uint32_t     lineno;               // line number (for ast nodes)
 *     uint32_t     num_args;             // arguments number for EX(This)
 *     uint32_t     fe_pos;               // foreach position
 *     uint32_t     fe_iter_idx;          // foreach iterator index
 *     uint32_t     access_flags;         // class constant access flags
 *     uint32_t     property_guard;       // single property guard
 *     uint32_t     constant_flags;       // constant flags
 *     uint32_t     extra;                // not further specified
 *   } u2;
 * } zval;
 *```
 *
 *```c++
 * typedef union _zend_value {
 *   zend_long         lval;                // long value
 *   double            dval;                // double value
 *   zend_refcounted  *counted;
 *   zend_string      *str;
 *   zend_array       *arr;
 *   zend_object      *obj;
 *   zend_resource    *res;
 *   zend_reference   *ref;
 *   zend_ast_ref     *ast;
 *   zval             *zv;
 *   void             *ptr;
 *   zend_class_entry *ce;
 *   zend_function    *func;
 *   struct {
 *     uint32_t w1;
 *     uint32_t w2;
 *   } ww;
 * } zend_value;
 *```
 */
abstract class Zval extends _zval_struct
{
}
abstract class zend_uchar extends string
{
}
abstract class uint32_t extends int
{
}
abstract class long extends int
{
}
abstract class zend_long extends long
{
}
abstract class double extends float
{
}
abstract class intptr_t extends long
{
}
abstract class size_t extends uint32_t
{
}
abstract class errno_t extends uint32_t
{
}


interface FFI
{
    /** @return void_ptr */
    public function tsrm_get_ls_cache();

    /** @return int */
    public function zend_register_list_destructors_ex(?rsrc_dtor_func_t $ld, ?rsrc_dtor_func_t $pld, const_char $type_name, int $module_number);

    /** @return zend_resource */
    public function zend_register_resource(void_ptr &$rsrc_pointer, int $rsrc_type);

    /** @return void_ptr */
    public function zend_fetch_resource(zend_resource &$res, const_char &$resource_type_name, int $resource_type);

    /** @return void_ptr */
    public function zend_fetch_resource_ex(zval &$res, ?const_char &$resource_type_name, int $resource_type);

    /** @return void_ptr */
    public function zend_fetch_resource2(zend_resource &$res, const_char &$resource_type_name, int &$resource_type, int $resource_type2);

    /** @return void_ptr */
    public function zend_fetch_resource2_ex(zval &$res, const_char &$resource_type_name, int $resource_type, int $resource_type2);

    /** @return zend_result */
    public function zend_parse_parameters(uint32_t $num_args, const_char &$type_spec, ...$arguments);

    /** @return zval */
    public function zend_hash_find(HashTable &$ht, zend_string &$key);

    /** @return zval */
    public function zend_hash_str_find(HashTable &$ht, const_char &$key, size_t $len);

    /** @return int */
    public function zend_hash_del(HashTable &$ht, zend_string &$key);

    /** @return zval */
    public function zend_hash_add_or_update(HashTable &$ht, zend_string &$key, zval &$pData, uint32_t $flag);

    /** @return zval */
    public function zend_ts_hash_str_find(TsHashTable &$ht, const_char &$key, size_t $len);

    /** @return zval */
    public function zend_ts_hash_str_update(TsHashTable &$ht, const_char &$key, size_t $len, zval &$pData);

    /** @return zval */
    public function zend_ts_hash_str_add(TsHashTable &$ht, const_char &$key, size_t $len, zval &$pData);

    /** @return void */
    public function zend_ts_hash_destroy(TsHashTable &$ht);

    /** @return void */
    public function zend_ts_hash_clean(TsHashTable &$ht);

    /** @return zval */
    public function zend_ts_hash_find(TsHashTable &$ht, zend_string &$key);

    /** @return zend_result */
    public function zend_ts_hash_del(TsHashTable &$ht, zend_string &$key);

    /** @return zval */
    public function zend_ts_hash_update(TsHashTable &$ht, zend_string &$key, zval &$pData);

    /** @return zval */
    public function zend_ts_hash_add(TsHashTable &$ht, zend_string &$key, zval &$pData);

    /** @return zend_function */
    public function zend_fetch_function(zend_string &$name);

    /** @return int */
    public function zend_set_user_opcode_handler(zend_uchar $opcode, ?user_opcode_handler_t $handler);

    /** @return user_opcode_handler_t */
    public function zend_get_user_opcode_handler(zend_uchar $opcode);

    /** @return zval */
    public function zend_get_zval_ptr(zend_op &$opline, int $op_type, znode_op &$node, zend_execute_data &$execute_data);

    public function zval_ptr_dtor(zval &$zval_ptr);

    public function zval_add_ref(zval &$p);

    public function zval_internal_ptr_dtor(zval &$zvalue);

    /** @return php_stream */
    public function _php_stream_fopen_from_fd(int $fd, const_char $mode, ...$arguments);

    /** @return int */
    public function _php_stream_free(php_stream &$stream, int $close_options);

    /** @return void */
    public function php_error_docref(?const_char &$docRef, int $type, const_char &$format, ...$arguments);

    /** @return void */
    public function zend_error(int $type, const_char &$format, ...$arguments);

    /** @return int */
    public function php_file_le_stream();

    /** @return int */
    public function php_file_le_pstream();

    /** @return int */
    public function _php_stream_cast(php_stream &$stream, int $castas, void_ptr &$ret, int $show_err);

    /** @return php_stream */
    public function _php_stream_fopen_tmpfile(int $dummy);

    /** @return php_stream */
    public function _php_stream_open_wrapper_ex(const_char &$path, const_char $mode, int $options, zend_string &$opened_path, ?php_stream_context &$context, ...$arguments);

    /** @return ssize_t */
    public function _php_stream_printf(php_stream &$stream, const_char &$fmt, ...$arguments);

    /** @return HashTable */
    public function _zend_new_array(uint32_t $size);

    /** @return uint32_t */
    public function zend_array_count(HashTable &$ht);

    /** @return HashTable */
    public function zend_new_pair(zval &$val1, zval &$val2);

    /** @return void */
    public function add_assoc_long_ex(zval &$arg, const_char $key, size_t $key_len, zend_long $n);

    /** @return void */
    public function add_assoc_null_ex(zval &$arg, const_char $key, size_t $key_len);

    /** @return void */
    public function add_assoc_bool_ex(zval &$arg, const_char $key, size_t $key_len, bool $b);

    /** @return void */
    public function add_assoc_resource_ex(zval &$arg, const_char $key, size_t $key_len, zend_resource &$r);

    /** @return void */
    public function add_assoc_double_ex(zval &$arg, const_char $key, size_t $key_len, double $d);

    /** @return void */
    public function add_assoc_str_ex(zval &$arg, const_char $key, size_t $key_len, zend_string &$str);

    /** @return void */
    public function add_assoc_string_ex(zval &$arg, const_char $key, size_t $key_len, const_char $str);

    /** @return void */
    public function add_assoc_stringl_ex(zval &$arg, const_char $key, size_t $key_len, const_char $str, size_t $length);

    /** @return void */
    public function add_assoc_zval_ex(zval &$arg, const_char $key, size_t $key_len, zval &$value);

    /** @return zend_result */
    public function add_next_index_string(zval &$arg, const_char &$str);

    /** @return zend_module_entry */
    public function zend_register_module_ex(zend_module_entry &$module);

    /** @return zend_result */
    public function zend_startup_module_ex(zend_module_entry &$module);
}
