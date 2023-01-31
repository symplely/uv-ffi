<?php

declare(strict_types=1);

use FFI\CData;
use ZE\Resource;
use ZE\PhpStream;

if (!\class_exists('UVHandler')) {
    abstract class UVHandler
    {
        protected ?CData $uv_struct = null;
        protected ?CData $uv_struct_ptr = null;
        protected ?CData $uv_struct_type = null;
        protected ?\UVSockAddr $uv_sock = null;

        public function __destruct()
        {
            $this->free();
        }

        protected function __construct(string $typedef, string $uv_type)
        {
            $this->uv_struct = \uv_ffi()->new($typedef);
            $this->uv_struct_ptr = \ffi_ptr($this->uv_struct);
            $this->uv_struct_type = \ffi_ptr($this->uv_struct_ptr->uv->{$uv_type});
        }

        public function __invoke(?bool $by_handle = false): CData
        {
            if ($by_handle)
                return \uv_handle($this->uv_struct_type);
            elseif (\is_null($by_handle))
                return $this->uv_struct_ptr;

            return $this->uv_struct_type;
        }

        public function get_sock()
        {
            return $this->uv_sock->__invoke();
        }

        /**
         * Manually removes `C data` structure pointer memory.
         *
         * @return void
         */
        public function free(): void
        {
            if (\is_uv_ffi() && !\is_null($this->uv_struct_type))
                \uv_ffi()->uv_unref($this->__invoke(true));

            \ffi_free_if($this->uv_struct_type, $this->uv_struct_ptr);
            $this->uv_struct_type = null;
            $this->uv_struct_ptr = null;
            $this->uv_struct = null;
            $this->uv_sock = null;
        }

        public static function close(object $handle, ?callable $callback = null)
        {
            if (!$handle instanceof \UV) {
                return \ze_ffi()->zend_error(
                    \E_WARNING,
                    "passed UV handle (%s) is not closeable",
                    \ffi_str_typeof(\uv_object($handle))
                );
            }

            if (!\uv_is_active($handle)) {
                \zval_add_ref($handle);
            }

            if (\uv_is_closing($handle)) {
                return \ze_ffi()->zend_error(
                    \E_WARNING,
                    "passed %s handle is already closed",
                    \reflect_object_name($handle)
                );
            }

            $handler = $handle(true);
            $fd = $handler->u->fd;
            if (Resource::is_valid($fd))
                Resource::remove_fd($fd);
            elseif (PhpStream::is_valid($fd))
                PhpStream::remove_fd($fd);

            \uv_ffi()->uv_close(
                $handler,
                (!\is_null($callback) ?
                    function (object $stream = null, int $status = null) use ($callback, $handle) {
                        $callback($handle, $status);
                    } : null)
            );

            \zval_skip_dtor($handle);
        }

        /** @return static */
        public static function init(?\UVLoop $loop, ...$arguments)
        {
            return new static(\array_shift($arguments), \reset($arguments));
        }
    }
}
