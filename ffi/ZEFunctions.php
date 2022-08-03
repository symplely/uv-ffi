<?php

declare(strict_types=1);

use FFI\CData;

if (!\function_exists('zval_stack')) {
    /**
     * Returns `Zval` of an argument by it's index.
     * - Represents `ZEND_CALL_ARG()` _macro_, the argument index is starting from 0.
     *
     * @param integer $argument
     * @return Zval
     */
    function zval_stack(int $argument): Zval
    {
        return ZendExecutor::init()->previous_state()->call_argument($argument);
    }

    /**
     * Zval `value` constructor, a copy of `argument`.
     *
     * @param mixed $argument
     * @return Zval
     */
    function zval_constructor($argument): Zval
    {
        return Zval::constructor($argument);
    }

    function zval_return()
    {
        return ZendExecutor::init()->previous_state()->return_value();
    }

    /**
     * Represents `php_stream_to_zval()` _macro_.
     *
     * Use this to assign the stream to a zval and tell the stream that is
     * has been exported to the engine; it will expect to be closed automatically
     * when the resources are auto-destructed.
     *
     * @param php_stream $ptr
     * @return Zval
     */
    function zval_stream(CData $ptr): Zval
    {
        return PhpStream::init_stream($ptr);
    }

    /**
     * Creates a new zval from it's type and value.
     *
     * @param int $type Value type
     * @param CData $value Value, should be zval-compatible
     * @param bool $isPersistent
     *
     * @return Zval
     */
    function zval_new(int $type, CData $value, bool $isPersistent = false): Zval
    {
        return Zval::new($type, $value, $isPersistent);
    }

    /**
     * Returns _native_ value for `userland`.
     *
     * @param Zval $zval
     */
    function zval_native(Zval $zval)
    {
        $zval->native_value($argument);
        return $argument;
    }

    /**
     * Creates a `Zval` instance base on various accessor macros.
     *
     * @param string|int $accessor One of:
     * -          Macro                 Return/Set type
     * - `ZE::TRUE`   for `ZVAL_TRUE()`
     * - `ZE::FALSE`   for `ZVAL_FALSE()`
     * - `ZE::NULL`   for `ZVAL_NULL()`
     * - `ZE::UNDEF`   for `ZVAL_UNDEF()`
     * - `ZE::BOOL`   for `ZVAL_BOOL()`   `unsigned char`
     * -
     * - `ZE::TYPE_P`   for `Z_TYPE_P()`  `unsigned char`
     * - `ZE::TYPE_INFO_P`  for `Z_TYPE_INFO_P()` `unsigned char`
     * - `ZE::TYPE_INFO_REFCOUNTED`  for `Z_TYPE_INFO_REFCOUNTED()` `boolean`
     * - `ZE::LVAL_P`   for `Z_LVAL_P()`  `zend_long`
     * - `ZE::DVAL_P`   for `Z_DVAL_P()`  `double`
     * - `ZE::STR_P`    for `Z_STR_P()`   `zend_string *`
     * - `ZE::STRVAL_P` for `Z_STRVAL_P()`  `char *`
     * - `ZE::STRLEN_P` for `Z_STRLEN_P()`  `size_t`
     * - `ZE::ARR_P`    for `Z_ARR_P()`   `HashTable *`
     * - `ZE::ARRVAL_P` for `Z_ARRVAL_P()`    `HashTable *`
     * - `ZE::OBJ_P`    for `Z_OBJ_P()`   `zend_object *`
     * - `ZE::OBJCE_P`  for `Z_OBJCE_P()` `zend_class_entry *`
     * - `ZE::RES_P`    for `Z_RES_P()`   `zend_resource *`
     * - `ZE::REF_P`    for `Z_REF_P()`   `zend_reference *`
     * - `ZE::REFVAL_P` for `Z_REFVAL_P()`  `zval *`
     * - `ZE::COUNTED_P`  for `Z_COUNTED_P()` `*`
     *
     * @param CData|int|bool|null $value a zend `C` struct/value to set to
     * @return Zval
     */
    function zval_macro($accessor, $value = null): Zval
    {
        return Zval::init()->macro($accessor, $value);
    }

    /**
     * @param object $ptr _struct_ with `->data` field
     * @param object $instance to be _cast_ to `void*`
     * @param string $name data field name other than `->data` the default
     * @return object $ptr
     */
    function zval_set_data(object $ptr, object $instance, string $name = 'data'): object
    {
        if (\is_cdata($ptr)) {
            $zval = Zval::constructor($instance);
            $ptr->{$name} = \ffi_void($zval()->value->obj);
            $zval->gc_addRef();
        }

        return $ptr;
    }

    function zval_get_data(object $ptr, string $name = 'data'): ?object
    {
        $zval = null;
        if (\is_cdata($ptr) && !\is_null($ptr->{$name})) {
            $zval = \zval_native(\zval_macro(
                ZE::OBJ_P,
                \zend_cast('zend_object*', $ptr->{$name})
            ));
        }

        return $zval;
    }

    /**
     * Represents `zend_gc_addref` macro.
     *
     * @param object $instance
     * @return object
     */
    function zval_add_ref(object $instance): object
    {
        $zval = \zval_stack(0);
        $zval->gc_addRef();
        return $instance;
    }

    /**
     * Represents `zend_gc_delref` macro.
     *
     * @param object $instance
     * @return object
     */
    function zval_del_ref(object $instance): object
    {
        $zval = \zval_stack(0);
        $zval->gc_delRef();
        return $instance;
    }

    /**
     * Check for `IS_OBJ_DESTRUCTOR_CALLED`, with `GC_ADD_FLAGS` macro.
     *
     * @param object $instance
     * @return void
     */
    function zval_skip_dtor(object $instance): void
    {
        \zval_stack(0)->gc_add_flags(ZE::IS_OBJ_DESTRUCTOR_CALLED);
    }

    /**
     * Returns an _instance_ that's a cross platform representation of a file handle.
     *
     * @param string $type - a handle `uv_file`, `uv_os_fd_t`, `php_socket_t` or _any_ platform type.
     * @return Resource
     */
    function fd_type(string $type = 'uv_file'): Resource
    {
        return Resource::init($type);
    }

    /**
     * Temporary enable `cli` if needed to preform a `php://fd/` **fopen()** call.
     *
     * @param integer $resource fd number
     * @return resource|false
     */
    function php_fd_direct(int $resource)
    {
        return \cli_direct(function (int $type) {
            return \fopen('php://fd/' . $type, '');
        }, $resource);
    }

    /**
     * Temporary enable `cli` if needed to preform a `php://fd/` **_php_stream_open_wrapper_ex()** call.
     * - Same as `php_fd_direct()` but returns a **Zval** _instance_ of `resource`.
     *
     * @param integer $resource fd number
     * @return Zval
     */
    function zval_fd_direct(int $resource): Zval
    {
        return \cli_direct(function (int $type) {
            $fd = Core::get_stdio($type);
            if ($fd === null)
                $fd = PhpStream::open_wrapper('php://fd/' . $type, '', 0)();

            return \zval_stream($fd);
        }, $resource);
    }

    /**
     * Temporary enable `cli` if needed to preform a `php://fd/` **_php_stream_open_wrapper_ex()** call.
     * - Same as `zval_fd_direct()` but returns underlying Zend **php_stream** _C structure_ of `resource`.
     *
     * @param integer $resource fd number
     * @return PhpStream
     */
    function php_stream_direct(int $resource): ?PhpStream
    {
        return \cli_direct(function (int $type) {
            $fd = Core::get_stdio($type);
            if ($fd === null) {
                return PhpStream::open_wrapper('php://fd/' . $type, '', 0);
            }

            return $fd;
        }, $resource);
    }

    /**
     * Returns an _instance_ representing `_php_stream` _C structure_.
     *
     * @return PhpStream
     */
    function stream_type(): PhpStream
    {
        return PhpStream::init();
    }

    /**
     * @param zend_resource|CData $res ZendResource
     * @return Zval
     */
    function zval_resource(CData $res): Zval
    {
        return Zval::init()->macro(ZE::RES_P, $res);
    }

    /**
     * @param _zend_array|CData $ht HashTable
     * @return Zval
     */
    function zval_array(CData $ht): Zval
    {
        return Zval::init()->macro(ZE::ARR_P, $ht);
    }

    /**
     * @param zval|CData $zval_value
     * @param zval|CData $zval_value2
     * @return _zend_array HashTable
     */
    function zend_new_pair(CData $zval_value, CData $zval_value2)
    {
        return \ze_ffi()->zend_new_pair($zval_value, $zval_value2);
    }

    /**
     * @param object|CData $ptr Will be **cast** to a `void` pointer
     * @param integer $type
     * @return zend_resource CData
     */
    function zend_register_resource($ptr, int $type): CData
    {
        return \ze_ffi()->zend_register_resource(\ze_ffi()->cast('void*', $ptr), $type);
    }

    /**
     * @param object|zend_resource|CData $ptr
     * @param string $type_name
     * @param integer|null $type_number if `null` uses `$ptr->type`
     * @param string $type_cast **void*** pointer to **typedef**, pass `null` for original **void***
     * @return CData **typedef**, or **void** pointer, if `null` in **$type_cast**
     */
    function zend_fetch_resource(object $ptr, string $type_name = '', int $type_number = null, ?string $type_cast = 'php_stream*'): CData
    {
        $void = \ze_ffi()->zend_fetch_resource($ptr, $type_name, \is_null($type_number)
            ? $ptr->type : $type_number);

        if (\is_null($type_cast))
            return $void;

        return \ze_ffi()->cast($type_cast, $void);
    }

    function zend_register_list_destructors_ex(callable $ld, ?callable $pld, string $type_name, int $module_number)
    {
        return \ze_ffi()->zend_register_list_destructors_ex($ld, $pld, $type_name, $module_number);
    }

    function zend_resource($argument): ZendResource
    {
        return ZendResource::init($argument);
    }

    function create_resource(CData $fd_ptr, string $type = 'stream', int $module = 20220101, callable $rsrc = null)
    {
        $fd_res = \zend_register_resource(
            $fd_ptr,
            \zend_register_list_destructors_ex((\is_null($rsrc)
                    ? function (CData $rsrc) {
                    } : $rsrc),
                null,
                $type,
                $module
            )
        );

        $fd_zval = \zval_resource($fd_res);

        return \zval_native($fd_zval);
    }

    function zend_reference(&$argument): ZendReference
    {
        return ZendReference::init($argument);
    }

    /**
     * Represents `ext-uv` _macro_ `PHP_UV_FD_TO_ZVAL()`.
     *
     * @param int $fd
     * @param string $mode
     * @return resource
     */
    function resource_from($fd, string $mode = 'wb+')
    {
        return PhpStream::fd_to_zval($fd, $mode);
    }

    /**
     * Represents `ext-uv` _function_ `php_uv_zval_to_fd()`.
     *
     * @param Zval $handle
     * @return int `fd`
     */
    function fd_from(Zval $handle)
    {
        return PhpStream::zval_to_fd($handle);
    }

    /**
     * Represents `ext-uv` _function_ `php_uv_zval_to_valid_poll_fd()`.
     *
     * @param Zval $handle
     * @return php_socket_t
     */
    function socket_from(Zval $handle)
    {
        return PhpStream::zval_to_poll_fd($handle);
    }

    Core::setup_stdio();
}
