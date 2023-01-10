<?php

declare(strict_types=1);

use FFI\CData;

if (!\class_exists('ext_uv')) {
    final class ext_uv extends \StandardModule
    {
        protected string $ffi_tag = 'uv';
        protected string $module_name = 'uv';
        protected string $module_version = '0.3.0';
        protected ?string $global_type = 'uv_globals';
        protected bool $m_startup = true;
        protected bool $m_shutdown = true;
        protected bool $r_shutdown = true;

        protected string $uv_version;

        /** @var \UVLoop[]|null */
        protected $uv_default;

        protected ?CData $default_mutex = null;

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
            if (\PHP_ZTS)
                $this->default_mutex = \ze_ffi()->tsrm_mutex_alloc();

            if (\IS_WINDOWS)
                $this->destruct_set();

            $this->uv_version = \uv_ffi()->uv_version_string();
            \Core::setup_stdio();
            return \ZE::SUCCESS;
        }

        public function module_shutdown(int $type, int $module_number): int
        {
            \Core::clear_stdio();
            \Core::clear('uv');

            if (\PHP_ZTS) {
                \ze_ffi()->tsrm_mutex_free($this->default_mutex);
                $this->default_mutex = null;
            }

            return \ZE::SUCCESS;
        }

        public function request_shutdown(...$args): int
        {
            if (\is_ze_ffi()) {
                $uv_loop = $this->get_default();
                if ($uv_loop instanceof \UVLoop && \is_cdata($uv_loop()))
                    $uv_loop->__destruct();
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
