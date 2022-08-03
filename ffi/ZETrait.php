<?php

declare(strict_types=1);

use FFI\CData;

trait ZETrait
{
    /**
     * Creates **PHP** class `instance` from _zend engine_ `C` structure _representing_ a **value**.
     *
     * @param CData $ptr a value pointer
     */
    public static function init_value(CData $ptr): self
    {
        $reflection = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();

        return $reflection->update($ptr);
    }

    public static function executor_globals(): CData
    {
        if (\PHP_ZTS) {
            $value = \ze_ffi()->cast(
                'zend_executor_globals*',
                \ze_ffi()->cast(
                    'char*',
                    \ze_ffi()->tsrm_get_ls_cache()
                ) + \ze_ffi()->executor_globals_offset
            );
        } else {
            $value = \ze_ffi()->executor_globals;
        }

        return $value;
    }

    public static function compiler_globals(): CData
    {
        if (\PHP_ZTS) {
            $value = \ze_ffi()->cast(
                'zend_compiler_globals*',
                \ze_ffi()->cast(
                    'char*',
                    \ze_ffi()->tsrm_get_ls_cache()
                ) + \ze_ffi()->compiler_globals_offset
            );
        } else {
            $value = \ze_ffi()->compiler_globals;
        }

        return $value;
    }

    public static function module_registry(): CData
    {
        return \ffi_ptr(\ze_ffi()->module_registry);
    }

    /**
     * Returns an aligned size.
     * Represents `ZEND_MM_ALIGNED_SIZE()` _macro_.
     */
    public static function aligned_size(int $size): int
    {
        $mask = ~(ZendExecutor::MM_ALIGNMENT - 1);
        $size = (($size + ZendExecutor::MM_ALIGNMENT - 1) & $mask);

        return $size;
    }

    /**
     * This method should return an instance of zend_refcounted_h.
     */
    protected function gc(): ?CData
    {
        if ($this->isZval)
            return $this->ze_ptr->value->counted;

        return $this->ze_other_ptr;
    }

    /**
     * Checks if the current value is refcounted or not.
     * Represents `Z_TYPE_INFO_REFCOUNTED()` _macro_.
     *
     * @param int $typeInfo Value type information
     */
    protected function is_type_info_refcounted(int $typeInfo): bool
    {
        return ($typeInfo & ZE::Z_TYPE_FLAGS_MASK) != 0;
    }

    /**
     * Represents `GC_REFCOUNT()` the `zend_gc_refcount` _macro_.
     * Returns an internal reference counter value
     */
    public function gc_refcount()
    {
        return $this->gc()->gc->refcount;
    }

    /**
     * Represents `GC_SET_REFCOUNT()` the `zend_gc_set_refcount` _macro_.
     */
    public function gc_set_refcount(int $count)
    {
        $this->gc()->gc->refcount = $count;
    }

    /**
     * Represents `GC_ADDREF()` the `zend_gc_addref` _macro_.
     */
    public function gc_addRef()
    {
        return ++$this->gc()->gc->refcount;
    }

    /**
     * Represents `GC_DELREF()` the `zend_gc_delref` _macro_.
     */
    public function gc_delRef()
    {
        assert($this->gc()->gc->refcount > 0);

        return --$this->gc()->gc->refcount;
    }

    /**
     * Represents `GC_TYPE_INFO()` _macro_.
     */
    public function gc_type_info()
    {
        return $this->gc()->gc->u->type_info;
    }

    /**
     * Represents `GC_ADD_FLAGS()` _macro_.
     * @param int $flags
     */
    public function gc_add_flags($flags)
    {
        $this->gc()->gc->u->type_info |= ($flags) << ZE::GC_FLAGS_SHIFT;
    }

    /**
     * Represents `GC_TYPE()` the `zval_gc_type` _macro_.
     */
    public function gc_type()
    {
        return ($this->gc_type_info() & ZE::GC_TYPE_MASK);
    }

    /**
     * Represents `GC_FLAGS()` the `zval_gc_flags` _macro_.
     */
    public function gc_flags(CData $ptr = null)
    {
        $info = \is_null($ptr) ? $this->gc_type_info() : $ptr->gc->u->type_info;

        return ($info >> ZE::GC_FLAGS_SHIFT) & (ZE::GC_FLAGS_MASK >> ZE::GC_FLAGS_SHIFT);
    }

    /**
     * Represents `GC_INFO()` the `zval_gc_info` _macro_.
     */
    public function gc_info()
    {
        return ($this->gc_type_info() >> ZE::GC_INFO_SHIFT);
    }

    /**
     * Checks if this _variable_ is or not `immutable`, `persistent (allocated using malloc)`,
     * or `persistent for thread via thread-local-storage (TLS)`.
     *
     * @param int $constant - must be either: `ZE::GC_IMMUTABLE`, `ZE::GC_PERSISTENT`, or `ZE::GC_PERSISTENT_LOCAL`
     * @return bool
     */
    public function is_variable(int $constant): bool
    {
        return (bool) ($this->gc()->gc->u->type_info & $constant);
        /*switch ($constant) {
  case ZE::IMMUTABLE:
    return (bool) ($this->gc()->u->type_info & ZE::GC_IMMUTABLE);
  case ZE::PERSISTENT:
    return (bool) ($this->gc()->u->type_info & ZE::GC_PERSISTENT);
  case ZE::PERSISTENT_LOCAL:
    return (bool) ($this->gc()->u->type_info & ZE::GC_PERSISTENT_LOCAL);
}*/
    }
}
