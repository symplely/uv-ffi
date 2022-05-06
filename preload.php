<?php

declare(strict_types=1);

if (!\function_exists('ffi_loader')) {
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
    Core::init($compile, $library, $include);
  }

  /**
   * Returns `C pointer` of a **uv type** from a `C data` _uv structure_.
   *
   * @param \FFI\CData $ptr `Could represent` one of:
   * - uv__io_t
   * - uv_loop_t
   * - uv_handle_t
   * - uv_dir_t
   * - uv_stream_t
   * - uv_tcp_t
   * - uv_udp_t
   * - uv_pipe_t
   * - uv_tty_t
   * - uv_poll_t
   * - uv_timer_t
   * - uv_prepare_t
   * - uv_check_t
   * - uv_idle_t
   * - uv_async_t
   * - uv_process_t
   * - uv_fs_event_t
   * - uv_fs_poll_t
   * - uv_signal_t
   * - uv_req_t
   * - uv_getaddrinfo_t
   * - uv_getnameinfo_t
   * - uv_shutdown_t
   * - uv_write_t
   * - uv_connect_t
   * - uv_udp_send_t
   * - uv_fs_t
   * - uv_work_t
   * - uv_random_t
   * - uv_env_item_t
   * - uv_cpu_info_t
   * - uv_interface_address_t
   * - uv_dirent_t
   * - uv_passwd_t
   * - uv_utsname_t
   * - uv_statfs_t
   * - uv_thread_options_t
   * @return FFI\CData
   */
  function uv_ptr(\FFI\CData $ptr): \FFI\CData
  {
    return \FFI::addr($ptr);
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
  function uv_struct($typedef, bool $owned = true, bool $persistent = false): ?\FFI\CData
  {
    return Core::struct($typedef, $owned, $persistent);
  }

  /**
   * Manually removes an previously created _uv structure_.
   *
   * @param UVInterface|UVLoop|\FFI\CData $ptr
   * @return void
   */
  function uv_free($ptr): void
  {
    Core::free($ptr);
  }

  function uv_ffi(): \FFI
  {
    return Core::get();
  }

  function uv_typeof($ptr): \FFI\CType
  {
    return Core::typeof($ptr());
  }

  function uv_sizeof($ptr): int
  {
    return Core::sizeof($ptr);
  }

  function is_null_ptr($ptr): bool
  {
    return Core::is_null($ptr);
  }

  /**
   * Check for _active_ `UV` **ffi** instance
   *
   * @return boolean
   */
  function is_ffi(): bool
  {
    return Core::is_ffi();
  }

  function ffi_loader(bool $compile = true, string $library = null, string $include = null)
  {
    $headers = empty($include) ? '.' . \DIRECTORY_SEPARATOR . 'headers' . \DIRECTORY_SEPARATOR . 'uv.h' : $include;
    $code = ($compile) ? '' : \file_get_contents($headers);
    if ('\\' === \DIRECTORY_SEPARATOR) {
      $code .= ($compile) ? '.\\headers\\windows.h' : \file_get_contents('.\\extra_windows.h');
      $lib = '.\\Windows\\uv.dll';
    } elseif (\PHP_OS === 'Darwin') {
      if ($compile)
        $code = './headers/macos.h';
      $lib = './macOS/libuv.1.0.0.dylib';
    } elseif (\php_uname('m') === 'aarch64') {
      if ($compile)
        $code = './headers/pi.h';
      $lib = './Linux/raspberry/libuv.so.1.0.0';
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
          // split value from key
          $parts = \explode('=', $line);
          // makes sure that "useless" lines are ignored (together with array_filter)
          if (\count($parts) !== 2)
            return false;

          // remove quotes, if the value is quoted
          $parts[1] = \str_replace(['"', "'"], '', $parts[1]);
          return $parts;
        }, \file($file)));

        foreach ($lines as $line)
          $os[$line[0]] = $line[1];
      }

      $id = \trim((string) $os['ID_LIKE']);
      $version = \trim((string) $os['VERSION_ID']);
      if ($id === 'debian') {
        $lib = './Linux/ubuntu' . ((float)$version < 20.04 ? '18.04' : '20.04') . '/libuv.so.1.0.0';
        if ($compile)
          $code = './headers/ubuntu' . ((float)$version < 20.04 ? '18.04' : '20.04') . '.h';
      } elseif ($id === 'redhat') {
        $lib = './Linux/centos' . ((float)$version < 8 ? '7' : '8+') . '/libuv.so.1.0.0';
        if ($compile)
          $code = './headers/centos' . ((float)$version < 8 ? '7' : '8+') . '.h';
      }
    }

    if ($compile) {
      $scope = \FFI::load($code);
      if (
        \opcache_is_script_cached("./ffi/UV.php") === false
        && \opcache_is_script_cached("./ffi/UVFunctions.php") === false
        && \opcache_is_script_cached("./ffi/UVHandles.php") === false
      ) {
        \opcache_compile_file("./ffi/Core.php");
        \opcache_compile_file("./ffi/UV.php");
        \opcache_compile_file("./ffi/UVConstants.php");
        \opcache_compile_file("./ffi/UVFunctions.php");
        \opcache_compile_file("./ffi/UVHandler.php");
        \opcache_compile_file("./ffi/UVHandles.php");
        \opcache_compile_file("./ffi/UVInterface.php");
      }
    } else {
      $scope = \FFI::cdef($code, (empty($library) ? $lib : $library));
    }

    Core::set($scope);
  }

  \ffi_loader();
}
