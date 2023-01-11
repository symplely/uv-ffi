<?php

declare(strict_types=1);

use FFI\CData;
use FFI\CType;

if (!\class_exists('UVThreader')) {
    abstract class UVThreader extends \CStruct
    {
        protected string $type;
        protected int $locked = 0x00;
        protected ?CData $struct_base;

        const IS_UV_RWLOCK      = 1;
        const IS_UV_RWLOCK_RD   = 2;
        const IS_UV_RWLOCK_WR   = 3;
        const IS_UV_MUTEX       = 4;
        const IS_UV_SEMAPHORE   = 5;
        const UV_LOCK_TYPE      = [
            'rwlock'    => self::IS_UV_RWLOCK,
            'mutex'     => self::IS_UV_MUTEX,
            'semaphore' => self::IS_UV_SEMAPHORE,
        ];

        public function __destruct()
        {
            //  try {
            if ($this->type === 'rwlock') {
                if ($this->locked == 0x01) {
                    \ze_ffi()->zend_error(\E_NOTICE, "uv_rwlock: still locked resource detected; forcing wrunlock");
                    \uv_ffi()->uv_rwlock_wrunlock($this->struct_ptr);
                } elseif ($this->locked) {
                    \ze_ffi()->zend_error(\E_NOTICE, "uv_rwlock: still locked resource detected; forcing rdunlock");
                    while (--$this->locked > 0) {
                        \uv_ffi()->uv_rwlock_rdunlock($this->struct_ptr);
                    }
                }

                \uv_ffi()->uv_rwlock_destroy($this->struct_ptr);
            } elseif ($this->type === 'mutex') {
                if ($this->locked == 0x01) {
                    \ze_ffi()->zend_error(\E_NOTICE, "uv_mutex: still locked resource detected; forcing unlock");
                    \uv_ffi()->uv_mutex_unlock($this->struct_ptr);
                }

                \uv_ffi()->uv_mutex_destroy($this->struct_ptr);
            } elseif ($this->type === 'semaphore') {
                if ($this->locked == 0x01) {
                    \ze_ffi()->zend_error(\E_NOTICE, "uv_sem: still locked resource detected; forcing unlock");
                    \uv_ffi()->uv_sem_post($this->struct_ptr);
                }

                \uv_ffi()->uv_sem_destroy($this->struct_ptr);
            }
            // } catch (\Throwable $e) {
            // }

            $this->free();
        }

        protected function __construct(
            $typedef,
            string $type = '',
            array $initializer = null,
            bool $isSelf = false
        ) {
            $this->tag = 'uv';
            $this->type = $type;
            if (!$isSelf || \is_string($typedef)) {
                $this->struct = \Core::get($this->tag)->new($typedef);
                $this->struct_base = \FFI::addr($this->struct);
                $this->struct_ptr = \FFI::addr($this->struct->lock->{$type});
                $this->struct_base->type = self::UV_LOCK_TYPE[$type] ?? null;
            } else {
                echo 'xxxx';
                $this->struct = \Core::get($this->tag)->new($typedef);
            }
        }

        public function __invoke(bool $byBase = false): CData
        {
            if ($byBase) {
                if (!\is_cdata($this->struct_base)) {
                    $this->struct_base = \FFI::addr($this->struct);
                }

                return $this->struct_base;
            }

            if (!\is_cdata($this->struct_ptr)) {
                $this->struct_ptr = \FFI::addr($this->struct->lock->{$this->type});
            }

            return $this->struct_ptr;
        }

        public function free(): void
        {
            \ffi_free_if($this->struct_ptr, $this->struct_base);
            $this->struct_ptr = null;
            $this->struct_base = null;
            $this->struct = null;
            $this->type = '';
            $this->tag = '';
        }
    }
}
