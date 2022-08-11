<?php

declare(strict_types=1);

use FFI\CData;
use FFI\CType;

if (!\function_exists('ffi_cdef')) {
  function ffi_cdef(string $code, string $lib = null): \FFI
  {
    if (!empty($lib)) {
      return \FFI::cdef($code, $lib);
    } else {
      return \FFI::cdef($code);
    }
  }

  /**
   * @return php_stream|null
   */
  function stream_stdout(): ?CData
  {
    return Core::get_stdio(1)();
  }

  /**
   * @return php_stream|null
   */
  function stream_stdin(): ?CData
  {
    return Core::get_stdio(0)();
  }

  /**
   * @return php_stream|null
   */
  function stream_stderr(): ?CData
  {
    return Core::get_stdio(2)();
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
    Core::init_libuv($compile, $library, $include);
  }

  function zend_init(): void
  {
    Core::init_zend();
  }

  /**
   * Returns **cast** a `uv_req_t` _base request_ pointer.
   *
   * @param object $ptr
   * @return CData uv_req_t
   */
  function uv_request(object $ptr): ?CData
  {
    return Core::cast('uv', 'uv_req_t*', \ffi_object($ptr));
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
    return Core::cast('uv', $typedef, \ffi_object($ptr));
  }

  /**
   * Returns **cast** a `zend` pointer as `typedef`.
   *
   * @param string $typedef
   * @param object $ptr
   * @return CData
   */
  function zend_cast(string $typedef, $ptr): CData
  {
    return Core::cast('ze', $typedef, \ffi_object($ptr));
  }

  /**
   * Returns **cast** a `uv_stream_t` _stream_ pointer.
   *
   * @param object $ptr
   * @return CData uv_stream_t
   */
  function uv_stream(object $ptr): CData
  {
    $stream = \ffi_object($ptr);
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

    $handle = \ffi_object($ptr);
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
    return Core::cast('uv', 'const struct sockaddr*', \ffi_object($ptr));
  }

  /**
   * Returns **cast** a `void*` pointer.
   *
   * @param CData $ptr
   * @return CData void_ptr
   */
  function ffi_void($ptr): CData
  {
    return \FFI::cast('void*', $ptr);
  }

  /**
   * Returns `C pointer` _addr_ of `C data` _type_.
   *
   * @param CData $ptr
   * @return FFI\CData
   */
  function ffi_ptr(CData $ptr): CData
  {
    return \FFI::addr($ptr);
  }

  /**
   * Convert `C string` to PHP `string`.
   *
   * @param CData $ptr
   * @return string
   */
  function ffi_string(CData $ptr): string
  {
    return \FFI::string($ptr);
  }

  /**
   * Convert PHP `string` to `C string`.
   *
   * @param string $string
   * @param bool $owned
   * @return CData char **pointer** of `string`
   */
  function ffi_char(string $string, bool $owned = false): CData
  {
    $size = \strlen($string);
    $ptr = \FFI::new('char[' . ($size + 1) . ']', $owned);
    \FFI::memcpy($ptr, $string, $size);

    return $ptr;
  }

  /**
   * Creates a `char` C data structure of size.
   *
   * @param int $size
   * @param bool $owned
   * @return CData `char` C structure
   */
  function ffi_characters(int $size, bool $owned = true): CData
  {
    $ptr = \FFI::new('char[' . ($size + 1) . ']', $owned);
    return $ptr;
  }

  /**
   * Checks `instance` and returns the `CData` object within.
   *
   * @param UVInterface|object $handle
   * @return CData
   */
  function ffi_object($handle): CData
  {
    $handler = $handle;
    if (
      $handle instanceof UVInterface
      || $handle instanceof UVLoop
      || $handle instanceof UVStream
      || $handle instanceof UVTypes
      || $handle instanceof ZE
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
  function ffi_free(object $ptr): void
  {
    Core::free($ptr);
  }

  /**
   * This function returns the **string** of the `FFI\CType object`,
   * representing the type of the given `FFI\CData object`.
   *
   * @param CData $ptr
   * @return string
   */
  function ffi_str_typeof(CData $ptr): string
  {
    return \trim(\str_replace(['FFI\CType:', ' Object'], '', \print_r(\FFI::typeof($ptr), true)));
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
   * @return \FFI **_global zend C structures_:**
   *
   * @property zend_internal_function $zend_pass_function
   * @property zend_object_handlers $std_object_handlers
   * @property HashTable $module_registry
   * @property size_t $compiler_globals_offset if ZTS
   * @property size_t $executor_globals_offset if ZTS
   * @property zend_execute_data $executor_globals if NTS
   * @property zend_compiler_globals $compiler_globals if NTS
   * @property php_stream_ops php_stream_stdio_ops;
   * @property php_stream_wrapper php_plain_files_wrapper;
   * @property sapi_module_struct sapi_module;
   * @property zend_fcall_info empty_fcall_info;
   * @property zend_fcall_info_cache empty_fcall_info_cache;
   */
  function ze_ffi(): \FFI
  {
    return Core::get('ze');
  }

  function win_ffi(): \FFI
  {
    return Core::get('win');
  }

  /**
   * Checks whether the given `FFI\CData` object __C type__, it's *typedef* equal.
   *
   * @param CData $ptr
   * @param string $ctype typedef
   * @return boolean
   */
  function is_typeof(CData $ptr, string $ctype): bool
  {
    return \ffi_str_typeof($ptr) === $ctype;
  }

  /**
   * Checks whether the given object is `FFI\CData`.
   *
   * @param mixed $ptr
   * @return boolean
   */
  function is_cdata($ptr): bool
  {
    return $ptr instanceof CData;
  }

  /**
   * Checks whether the given object is `UVStream` or `uv_stream_t`.
   *
   * @param mixed $ptr
   * @return boolean
   */
  function is_uv_stream(object $ptr): bool
  {
    return \in_array(\ffi_str_typeof(\ffi_object($ptr)), [
      'struct uv_tcp_s*', 'struct uv_pipe_s*',
      'struct uv_tty_s*', 'struct uv_stream_s*'
    ], true);
  }

  /**
   * Checks whether the `FFI\CData` is a null pointer.
   *
   * @param object $ptr
   * @return boolean
   */
  function is_null_ptr(object $ptr): bool
  {
    return Core::is_null($ptr);
  }

  /**
   * Check for _active_ `UV` **ffi** instance
   *
   * @return boolean
   */
  function is_uv_ffi(): bool
  {
    return Core::is_uv_ffi();
  }

  /**
   * Check for _active_ `PHP Engine` **ffi** instance
   *
   * @return boolean
   */
  function is_ze_ffi(): bool
  {
    return Core::is_ze_ffi();
  }

  /**
   * Check for _active_ `Windows` **ffi** instance
   *
   * @return boolean
   */
  function is_win_ffi(): bool
  {
    return Core::is_win_ffi();
  }

  /**
   * Temporary enable `cli` if needed to preform a the `routine` call.
   *
   * @param callable $routine
   * @param mixed ...$arguments
   * @return mixed
   */
  function cli_direct(callable $routine, ...$arguments)
  {
    $cdata = \ze_ffi()->sapi_module;
    $old = \ffi_string($cdata->name);
    $changed = false;
    if ($old !== 'cli') {
      $changed = true;
      $cdata->name = \ffi_char('cli');
    }

    $result = $routine(...$arguments);
    if ($changed)
      $cdata->name = \ffi_char($old);

    return $result;
  }

  /**
   * Gets class name
   *
   * @param object $handle
   * @return string
   */
  function reflect_object_name(object $handle): string
  {
    return (new \ReflectionObject($handle))->getName();
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

    if ('\\' === \DIRECTORY_SEPARATOR) {
      $code = '.\\headers\\windows.h';
      $lib = '.\\Windows\\uv.dll';
    } elseif (\PHP_OS === 'Darwin') {
      $code = './headers/macos.h';
      $lib = './macOS/libuv.1.0.0.dylib';
    } elseif (\php_uname('m') === 'aarch64') {
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
        $lib = './Linux/ubuntu' . ((float)$version < 20.04 ? '18.04' : '20.04') . '/libuv.so.1.0.0';
        $code = './headers/ubuntu' . ((float)$version < 20.04 ? '18.04' : '20.04') . '.h';
      } elseif ($id === 'redhat') {
        $lib = './Linux/centos' . ((float)$version < 8 ? '7' : '8+') . '/libuv.so.1.0.0';
        $code = './headers/centos' . ((float)$version < 8 ? '7' : '8+') . '.h';
      }
    }

    if ($compile) {
      $scope = \FFI::load($code);
      if (\opcache_is_script_cached("./ffi/UV.php") === false && \opcache_is_script_cached("./ffi/ZE.php") === false) {
        \opcache_compile_file("./ffi/Core.php");
        \opcache_compile_file("./ffi/ZE.php");
        \opcache_compile_file("./ffi/ZETrait.php");
        \opcache_compile_file("./ffi/ZETypes.php");
        \opcache_compile_file("./ffi/UV.php");
        \opcache_compile_file("./ffi/UVConstants.php");
        \opcache_compile_file("./ffi/UVFunctions.php");
        \opcache_compile_file("./ffi/UVHandler.php");
        \opcache_compile_file("./ffi/UVTypes.php");
        \opcache_compile_file("./ffi/UVHandles.php");
        \opcache_compile_file("./ffi/UVInterface.php");
        \opcache_compile_file("./preload.php");
        \opcache_compile_file("./ffi/ZEFunctions.php");
      }
    } else {
      $headers = empty($include) ? $code : $include;
      $scope = \FFI::cdef(\str_replace($remove, '', \file_get_contents($headers)), (empty($library) ? $lib : $library));
    }

    Core::set('uv', $scope);
  }

  function ze_ffi_loader()
  {
    if (\PHP_OS_FAMILY === 'Windows') {
      $code =  "zeWin" . \PHP_MAJOR_VERSION . (\PHP_ZTS ? "ts" : "") . ".h";
      $php = \FFI::load('./headers/' . $code);
    } else {
      $php = \FFI::load('./headers/ze' . (\PHP_ZTS ? "ts" : "") . '.h');
    }

    /*
    \define('ZEND_HANDLE_FILENAME', 1);
    \define('ZEND_HANDLE_FP', 2);
    \define('ZEND_HANDLE_STREAM', 3);
    \define('PHP_ZE_EXTENSION', '.ze');
    \define('OPEN_TAG', '<?php' . \PHP_EOL);

    // When a file will be opened by the PHP Engine, it checks if the
    // function zend_stream_open_function is defined (default is undefined)
    // if the function is defined, the engine calls it instead of using the
    // default function.
    $php->zend_stream_open_function = function ($filename, $handle) use ($php) {
      $handle->type = \ZEND_HANDLE_STREAM;
      // We are using isatty to be able to read each char of the file and
      // append the open tag if necessary
      $handle->handle->stream->isatty = 1;

      $file = \fopen($filename, 'r');
      $filenameLength = \strlen($filename);

      // is the file extension .plus?
      $extension = \substr(
        $filename,
        $filenameLength - \strlen(\PHP_ZE_EXTENSION),
        \strlen(\PHP_ZE_EXTENSION)
      );
      $isPhpZe = $extension === \PHP_ZE_EXTENSION;

      $currentChar = 0;

      $handle->handle->stream->reader = function ($handle, $buf, $sizeOfBuf) use (&$currentChar, $file, $isPhpZe) {
        // Appends the open tag at the beginning of the file
        if ($isPhpZe && $currentChar < \strlen(\OPEN_TAG)) {
          $char = \OPEN_TAG[$currentChar++];
          \FFI::memcpy($buf, $char, $sizeOfBuf);
          return true;
        }

        // Reads the file
        if ($char = \fread($file, $sizeOfBuf)) {
          \FFI::memcpy($buf, $char, $sizeOfBuf);
          return true;
        }

        // EOF
        return false;
      };
    };
    */

    Core::set('ze', $php);
  }

  function win_ffi_loader()
  {
    Core::set('win', \FFI::load('.\\headers\\msvcrt.h'));
  }

  \uv_ffi_loader();
  \ze_ffi_loader();
  // if ('\\' === \DIRECTORY_SEPARATOR)
  //   \win_ffi_loader();
}
