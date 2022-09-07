<?php

declare(strict_types=1);

use FFI\CData;
use FFI\CType;
use ZE\Zval;
use ZE\Resource;
use ZE\PhpStream;

if (!\function_exists('uv_init')) {
    /**
     * Returns **cast** a `uv_req_t` _base request_ pointer.
     *
     * @param object $ptr
     * @return CData uv_req_t
     */
    function uv_request(object $ptr): ?CData
    {
        return Core::cast('uv', 'uv_req_t*', \uv_object($ptr));
    }

    /**
     * Returns **cast** a `uv` pointer as `typedef`.
     *
     * @param string $typedef
     * @param object $ptr
     * @return CData
     */
    function uv_cast(string $typedef, object $ptr): CData
    {
        return Core::cast('uv', $typedef, \uv_object($ptr));
    }

    /**
     * Returns **cast** a `uv_stream_t` _stream_ pointer.
     *
     * @param object $ptr
     * @return CData uv_stream_t
     */
    function uv_stream(object $ptr): CData
    {
        $stream = \uv_object($ptr);
        return \is_typeof($stream, 'struct uv_stream_s*')  ? $stream : Core::cast('uv', 'uv_stream_t*', $stream);
    }

    /**
     * Returns **cast** a `uv_handle_t` _base handle_ pointer.
     *
     * @param object $ptr
     * @return CData uv_handle_t
     */
    function uv_handle(object $ptr): CData
    {
        if ($ptr instanceof UVInterface)
            return $ptr(true);

        $handle = \uv_object($ptr);
        return \is_typeof($handle, 'struct uv_handle_s*')  ? $handle : Core::cast('uv', 'uv_handle_t*', $handle);
    }

    /**
     * Returns **cast** a `sockaddr` _address and port base structure_ pointer.
     *
     * @param UVSockAddr|sockaddr_in|sockaddr_in6 $ptr
     * @return CData sockaddr
     */
    function uv_sockaddr(object $ptr): CData
    {
        return Core::cast('uv', 'const struct sockaddr*', \uv_object($ptr));
    }

    /**
     * Checks `instance` and returns the `CData` object within.
     *
     * @param UVInterface|object $handle
     * @return CData
     */
    function uv_object($handle): CData
    {
        $handler = $handle;
        if (
            $handle instanceof UVInterface
            || $handle instanceof UVLoop
            || $handle instanceof UVStream
            || $handle instanceof UVTypes
        )
            $handler = $handle();

        return $handler;
    }

    /**
     * Manually removes an previously created `C` data memory pointer.
     *
     * @param UVInterface|UVLoop|CData $ptr
     * @return void
     */
    function uv_ffi_free(object $ptr): void
    {
        if ($ptr instanceof \UVInterface || $ptr instanceof \UVLoop || $ptr instanceof \UVTypes)
            $ptr->free();
        elseif (\is_cdata($ptr))
            \FFI::free($ptr);
    }

    /**
     * @param CData $fd_ptr
     * @param integer $fd
     * @param \UVFs $req
     * @return resource
     */
    function create_uv_fs_resource(CData $fd_ptr, int $fd, \UVFs $req)
    {
        $fd_res = \zend_register_resource(
            $fd_ptr,
            \zend_register_list_destructors_ex(
                function (CData $rsrc) {
                    \uv_ffi()->uv_fs_req_cleanup(\uv_cast('uv_fs_t*', $rsrc->ptr));
                },
                null,
                'stream',
                20220101
            )
        );

        $fd_zval = \zval_resource($fd_res);
        $resource = \zval_native($fd_zval);
        $file = \fd_type();
        $file->update($fd_ptr, true);
        $file->add_object($req);
        $file->add_pair($fd_zval, $fd, (int)$resource);

        return $resource;
    }

    /**
     * @param resource $stream
     * @return array<Zval|uv_file|int>
     */
    function zval_to_fd_pair($stream): array
    {
        $zval = Resource::get_fd((int)$stream, true);
        $fd = $zval instanceof Zval ? Resource::get_fd((int)$stream, false, false, true) : null;
        if (!\is_integer($fd)) {
            $zval = Zval::constructor($stream);
            $fd = PhpStream::zval_to_fd($zval, true);
        }

        return [$zval, $fd];
    }

    /**
     * Represents _ext-uv_ `php_uv_stat_to_zval` function.
     *
     * @param CData $stat
     * @return array
     */
    function uv_stat_to_zval(CData $stat): array
    {
        $result = \zval_array(\ze_ffi()->_zend_new_array(0));
        \ze_ffi()->add_assoc_long_ex($result(), "dev", \strlen("dev"), $stat->st_dev);
        \ze_ffi()->add_assoc_long_ex($result(), "ino", \strlen("ino"), $stat->st_ino);
        \ze_ffi()->add_assoc_long_ex($result(), "mode", \strlen("mode"), $stat->st_mode);
        \ze_ffi()->add_assoc_long_ex($result(), "nlink", \strlen("nlink"), $stat->st_nlink);
        \ze_ffi()->add_assoc_long_ex($result(), "uid", \strlen("uid"), $stat->st_uid);
        \ze_ffi()->add_assoc_long_ex($result(), "gid", \strlen("gid"), $stat->st_gid);
        \ze_ffi()->add_assoc_long_ex($result(), "rdev", \strlen("rdev"), $stat->st_rdev);
        \ze_ffi()->add_assoc_long_ex($result(), "size", \strlen("size"), $stat->st_size);

        if (\IS_LINUX) {
            \ze_ffi()->add_assoc_long_ex($result(), "blksize", \strlen("blksize"), $stat->st_blksize);
            \ze_ffi()->add_assoc_long_ex($result(), "blocks", \strlen("blocks"), $stat->st_blocks);
        }

        \ze_ffi()->add_assoc_long_ex($result(), "atime", \strlen("atime"), $stat->st_atim->tv_sec);
        \ze_ffi()->add_assoc_long_ex($result(), "mtime", \strlen("mtime"), $stat->st_mtim->tv_sec);
        \ze_ffi()->add_assoc_long_ex($result(), "ctime", \strlen("ctime"), $stat->st_ctim->tv_sec);

        return \zval_native($result);
    }

    /**
     * Creates a _uv structure_, can be of 40 types.
     * @param string $typedef
     * - typedef: `struct uv__io_s` for - **uv__io_t**
     * - typedef: `struct uv_buf_t`
     * - typedef: `struct uv_loop_s` for - **uv_loop_t**
     * - typedef: `struct uv_handle_s` for - **uv_handle_t**
     * - typedef: `struct uv_dir_s` for - **uv_dir_t**
     * - typedef: `struct uv_stream_s` for - **uv_stream_t**
     * - typedef: `struct uv_tcp_s` for - **uv_tcp_t**
     * - typedef: `struct uv_udp_s` for - **uv_udp_t**
     * - typedef: `struct uv_pipe_s` for - **uv_pipe_t**
     * - typedef: `struct uv_tty_s` for - **uv_tty_t**
     * - typedef: `struct uv_poll_s` for - **uv_poll_t**
     * - typedef: `struct uv_timer_s` for - **uv_timer_t**
     * - typedef: `struct uv_prepare_s` for - **uv_prepare_t**
     * - typedef: `struct uv_check_s` for - **uv_check_t**
     * - typedef: `struct uv_idle_s` for - **uv_idle_t**
     * - typedef: `struct uv_async_s` for - **uv_async_t**
     * - typedef: `struct uv_process_s` for - **uv_process_t**
     * - typedef: `struct uv_fs_event_s` for - **uv_fs_event_t**
     * - typedef: `struct uv_fs_poll_s` for - **uv_fs_poll_t**
     * - typedef: `struct uv_signal_s` for - **uv_signal_t**
     * - typedef: `struct uv_req_s` for - **uv_req_t**
     * - typedef: `struct uv_getaddrinfo_s` for - **uv_getaddrinfo_t**
     * - typedef: `struct uv_getnameinfo_s` for - **uv_getnameinfo_t**
     * - typedef: `struct uv_shutdown_s` for - **uv_shutdown_t**
     * - typedef: `struct uv_write_s` for - **uv_write_t**
     * - typedef: `struct uv_connect_s` for - **uv_connect_t**
     * - typedef: `struct uv_udp_send_s` for - **uv_udp_send_t**
     * - typedef: `struct uv_fs_s` for - **uv_fs_t**
     * - typedef: `struct uv_work_s` for - **uv_work_t**
     * - typedef: `struct uv_random_s` for - **uv_random_t**
     * - typedef: `struct uv_env_item_s` for - **uv_env_item_t**
     * - typedef: `struct uv_cpu_info_s` for - **uv_cpu_info_t**
     * - typedef: `struct uv_interface_address_s` for - **uv_interface_address_t**
     * - typedef: `struct uv_dirent_s` for - **uv_dirent_t**
     * - typedef: `struct uv_passwd_s` for - **uv_passwd_t**
     * - typedef: `struct uv_utsname_s` for - **uv_utsname_t**
     * - typedef: `struct uv_statfs_s` for - **uv_statfs_t**
     * - typedef: `struct uv_stdio_container_s`
     * - typedef: `struct uv_process_options_s`
     * - typedef: `struct uv_thread_options_s` for - **uv_thread_options_t**
     *
     * @param boolean $owned
     * @param boolean $persistent
     * @return FFI\CData - use `uv_ptr()` to pass a **uv type** structure to a `uv function`.
     */
    function uv_struct($typedef, bool $owned = true, bool $persistent = false): ?CData
    {
        return Core::struct('uv', $typedef, $owned, $persistent);
    }

    function uv_typedef(string $typedef): ?CType
    {
        return Core::typedef('uv', $typedef);
    }

    function uv_ffi(): \FFI
    {
        return Core::get('uv');
    }

    /**
     * Checks whether the given object is `UVStream` or `uv_stream_t`.
     *
     * @param mixed $ptr
     * @return boolean
     */
    function is_uv_stream(object $ptr): bool
    {
        return \in_array(\ffi_str_typeof(\uv_object($ptr)), [
            'struct uv_tcp_s*', 'struct uv_pipe_s*',
            'struct uv_tty_s*', 'struct uv_stream_s*'
        ], true);
    }

    /**
     * Check for _active_ `UV` **ffi** instance
     *
     * @return boolean
     */
    function is_uv_ffi(): bool
    {
        return Core::get('uv') instanceof \FFI;
    }

    /**
     * **Setup** - *creates* a new **UV FFI** object or *retrieve* current `scoped`_(preloaded)_ object.
     * - This function will try preloading first.
     *
     * @param boolean $compile Controls how FFI library is initialized when calling **FFI::scope()** fails.
     * - `true` calls **FFI:load()** and `opcache_compile_file()`.
     * - `false` calls **FFI::cdef()**.
     * @param string $library The name of a shared library file, to be loaded and linked with the header definitions.
     * - Only used when calling **FFI::cdef()**.
     * @param string $include Include headers, _file/string_ for *OS/platforms* not currently available.
     * @return void
     * @throws \RuntimeException
     */
    function uv_init(bool $compile = true, string $library = null, string $include = null): void
    {
        if (!\is_uv_ffi()) {
            // Try if preloaded
            try {
                Core::set('uv', \FFI::scope("UV"));
            } catch (Exception $e) {
                \uv_ffi_loader($compile, $library, $include);
            }

            if (!\is_uv_ffi()) {
                throw new \RuntimeException("FFI parse failed!");
            }
        }
    }

    function uv_ffi_loader(bool $compile = true, string $library = null, string $include = null)
    {
        $remove = [
            '#define', ' FFI_SCOPE ', '"__uv__"', ' FFI_LIB ',
            '"./lib/Linux/ubuntu20.04/libuv.so.1.0.0"', '"./lib/Linux/ubuntu18.04/libuv.so.1.0.0"',
            '"./lib/Linux/raspberry/libuv.so.1.0.0"', '"./lib/macOS/libuv.1.0.0.dylib"',
            '".\\lib\\Windows\\uv.dll"', '"./lib/Linux/centos8+/libuv.so.1.0.0"',
            '"./lib/Linux/centos7/libuv.so.1.0.0"'
        ];

        $directory = __DIR__ . \DS;
        if (\IS_WINDOWS) {
            $code = $directory . 'headers\\windows.h';
            $lib = $directory . 'Windows\\uv.dll';
        } elseif (\PHP_OS === 'Darwin') {
            $code = $directory . 'headers/macos.h';
            $lib = $directory . 'macOS/libuv.1.0.0.dylib';
        } elseif (\php_uname('m') === 'aarch64') {
            $code = $directory . 'headers/pi.h';
            $lib = $directory . 'Linux/raspberry/libuv.so.1.0.0';
        } else {
            /*
        * Get the `Linux` distribution info and version.
        * [DISTRIB_ID] => Ubuntu
        * [DISTRIB_RELEASE] => 13.04
        * [DISTRIB_CODENAME] => raring
        * [DISTRIB_DESCRIPTION] => Ubuntu 13.04
        * [NAME] => Ubuntu
        * [VERSION] => 13.04, Raring Ringtail
        * [ID] => ubuntu
        * [ID_LIKE] => debian
        * [PRETTY_NAME] => Ubuntu 13.04
        * [VERSION_ID] => 13.04
        * [HOME_URL] => http://www.ubuntu.com/
        * [SUPPORT_URL] => http://help.ubuntu.com/
        * [BUG_REPORT_URL] => http://bugs.launchpad.net/ubuntu/
      */
            $os = [];
            $files = \glob('/etc/*-release');
            foreach ($files as $file) {
                $lines = \array_filter(\array_map(function ($line) {
                    $parts = \explode('=', $line);
                    if (\count($parts) !== 2)
                        return false;

                    $parts[1] = \str_replace(['"', "'"], '', $parts[1]);
                    return $parts;
                }, \file($file)));

                foreach ($lines as $line)
                    $os[$line[0]] = $line[1];
            }

            $id = \trim((string) $os['ID_LIKE']);
            $version = \trim((string) $os['VERSION_ID']);
            if ($id === 'debian') {
                $lib = $directory . 'Linux/ubuntu' . ((float)$version < 20.04 ? '18.04' : '20.04') . '/libuv.so.1.0.0';
                $code = $directory . 'headers/ubuntu' . ((float)$version < 20.04 ? '18.04' : '20.04') . '.h';
            } elseif ($id === 'redhat') {
                $lib = $directory . 'Linux/centos' . ((float)$version < 8 ? '7' : '8+') . '/libuv.so.1.0.0';
                $code = $directory . 'headers/centos' . ((float)$version < 8 ? '7' : '8+') . '.h';
            }
        }

        if ($compile) {
            $scope = \FFI::load($code);
            if (\file_exists('.' . \DS . 'ffi_extension.json')) {
                $ext_list = \json_decode(\file_get_contents('.' . \DS . 'ffi_extension.json'), true);
                $isDir = false;
                $iterator = [];
                $is_opcache_cli = \ini_get('opcache.enable_cli') === '1';
                if (isset($ext_list['preload']['directory'])) {
                    $isDir = true;
                    $directory = \array_shift($ext_list['preload']['directory']);
                    $dir = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::KEY_AS_PATHNAME);
                    $iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
                } elseif (isset($ext_list['preload']['files'])) {
                    $iterator = $ext_list['preload']['files'];
                }

                foreach ($iterator as $fileInfo) {
                    if ($isDir && !$fileInfo->isFile()) {
                        continue;
                    }

                    $file = $isDir ? $fileInfo->getPathname() : $fileInfo;
                    if ($is_opcache_cli) {
                        if (!\opcache_is_script_cached($file))
                            \opcache_compile_file($file);
                    } else {
                        include_once $file;
                    }
                }
            }
        } else {
            $headers = empty($include) ? $code : $include;
            $scope = \FFI::cdef(\str_replace($remove, '', \file_get_contents($headers)), (empty($library) ? $lib : $library));
        }

        Core::set('uv', $scope);
    }

    \uv_ffi_loader();
}
