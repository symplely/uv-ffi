<?php

declare(strict_types=1);

use FFI\CData;

if (!\class_exists('ext_uv')) {
    final class ext_uv extends \StandardModule
    {
        protected string $ffi_tag = 'uv';
        protected string $module_name = 'uv';
        protected string $module_version = '0.3.0';
        protected bool $m_startup = true;
        protected bool $m_shutdown = true;
        protected bool $r_shutdown = true;

        protected string $uv_version;
        protected bool $restart_sapi = false;
        protected bool $uv_exited = false;

        protected bool $shutdown_on_request = false;

        /** @var \UVLoop[]|null */
        protected $uv_default = null;

        protected ?CData $default_mutex = null;

        public function shutdown_set(): void
        {
            $this->shutdown_on_request = true;
        }

        public function is_shutdown(): bool
        {
            return $this->shutdown_on_request;
        }

        public function get_mutex(): ?CData
        {
            return (\PHP_ZTS) ? $this->default_mutex : null;
        }

        public function set_default(?\UVLoop $loop): void
        {
            if (\PHP_ZTS)
                $this->uv_default[\ze_ffi()->tsrm_thread_id()] = $loop;
            else
                $this->uv_default = $loop;
        }

        public function get_default(): ?\UVLoop
        {
            if (\PHP_ZTS)
                return $this->uv_default[\ze_ffi()->tsrm_thread_id()] ?? null;

            return $this->uv_default;
        }

        public function module_startup(int $type, int $module_number): int
        {
            \ffi_set_free(false);
            if (\PHP_ZTS)
                $this->default_mutex = \ze_ffi()->tsrm_mutex_alloc();

            $this->uv_version = \uv_ffi()->uv_version_string();
            \Core::setup_stdio();
            return \ZE::SUCCESS;
        }

        public function module_clear(): void
        {
            if (!$this->uv_exited) {
                $this->uv_exited = true;
                \Core::clear_stdio();

                if (\PHP_ZTS) {
                    \ze_ffi()->tsrm_mutex_free($this->default_mutex);
                    $this->default_mutex = null;
                }

                \ext_uv::clear_module();
                if (\Core::get('uv') instanceof \FFI) {
                    if (\IS_WINDOWS)
                        \uv_library_shutdown();

                    \Core::clear('uv');
                }
            };
        }

        public function module_shutdown(int $type, int $module_number): int
        {
            if (!$this->destructor_linked) {
                $this->module_clear();
            }

            return \ZE::SUCCESS;
        }

        public function request_shutdown(int $type, int $module_number): int
        {
            if (\is_ze_ffi()) {
                $uv_loop = $this->get_default();
                if ($uv_loop instanceof \UVLoop && \is_cdata($uv_loop())) {
                    $this->set_default(null);
                } elseif (!$this->uv_exited) {
                    $module = $this->__invoke();
                    $this->module_shutdown($module->type, $module->module_number);
                }
            }

            return \ZE::SUCCESS;
        }

        public function module_info(CData $entry): void
        {
            \ze_ffi()->php_printf('PHP lib' . $entry->name . "-ffi Extension\n");
            \ze_ffi()->php_info_print_table_start();
            \ze_ffi()->php_info_print_table_header(2, "libuv Support", "enabled");
            \ze_ffi()->php_info_print_table_row(2, "Version", $this->module_version);
            \ze_ffi()->php_info_print_table_row(2, "libuv Version", $this->uv_version);
            \ze_ffi()->php_info_print_table_end();
        }
    }
}
