<?php

declare(strict_types=1);

use FFI\CData;

if (!\function_exists('uv_loop_init')) {
    /**
     * Initializes a `UVLoop` instance structure.
     *
     * @return UVLoop|int
     * @link http://docs.libuv.org/en/v1.x/loop.html#c.uv_loop_init
     */
    function uv_loop_init(bool $compile = true, ?string $library = null, ?string $include = null)
    {
        return UVLoop::init($compile, $library, $include);
    }

    /**
     * create a `new` loop handle.
     *
     * @return UVLoop
     * @deprecated 1.0
     */
    function uv_loop_new(): \UVLoop
    {
        return \uv_loop_init();
    }

    /**
     * Returns the initialized default loop.
     * This function is just a convenient way for having a global loop
     * throughout an application, the default loop is in no way
     * different than the ones initialized with `uv_loop_init()`.
     * - Warning: This function is not thread safe.
     * @return UVLoop
     * @link http://docs.libuv.org/en/v1.x/loop.html#c.uv_default_loop
     */
    function uv_default_loop(bool $compile = true, ?string $library = null, ?string $include = null): \UVLoop
    {
        return \UVLoop::default($compile, $library, $include);
    }

    /**
     * This function runs the event loop. It will act differently depending on the
     * specified `$mode`.
     * - Note: *uv_run()* is not reentrant. It must not be called from a callback.
     * @param UVLoop $loop
     * @param int $mode
     *  - `UV::RUN_DEFAULT`: Runs the event loop until the reference count drops to
     *    zero. Always returns zero.
     *  - `UV::RUN_ONCE`: Poll for new events once. Note that this function blocks if
     *    there are no pending events. Returns zero when done (no active handles
     *    or requests left), or non-zero if more events are expected (meaning you
     *    should run the event loop again sometime in the future).
     *  - `UV::RUN_NOWAIT`: Poll for new events once but don't block if there are no
     *    pending events.
     * @link http://docs.libuv.org/en/v1.x/loop.html#c.uv_run
     */
    function uv_run(\UVLoop $loop = null, int $mode = \UV::RUN_DEFAULT): void
    {
        if (\is_null($loop))
            $loop = \uv_default_loop();

        \uv_ffi()->uv_run($loop(), $mode);
        if ($mode === \UV::RUN_DEFAULT)
            \zval_del_ref($loop);
    }

    /**
     * Delete specified loop handle.
     *
     * @param UVLoop $loop uv_loop handle.
     *
     * @return void
     * @deprecated 1.0
     */
    function uv_loop_delete(\UVLoop &$loop): void
    {
        \uv_ffi()->uv_loop_delete($loop());
    }

    /**
     * Release any global state that libuv is holding onto. Libuv will normally do so automatically when it is unloaded but it can be instructed to perform cleanup manually.
     *
     * - Warning: Only call uv_library_shutdown() once.
     * - Warning: Don’t call uv_library_shutdown() when there are still event loops or I/O requests active.
     * - Warning: Don’t call libuv functions after calling uv_library_shutdown().
     *
     * @return void
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=file%20to%20fd#c.uv_library_shutdown
     */
    function uv_library_shutdown(): void
    {
        \uv_ffi()->uv_library_shutdown();
    }

    /**
     * Releases all internal loop resources.
     * Call this function only when the loop has finished executing and all open handles and requests have been closed, or
     * it will return UV_EBUSY. After this function returns, the user can free the memory allocated for the loop.
     *
     * @param UVLoop $loop
     * @return void
     * @link http://docs.libuv.org/en/v1.x/loop.html#c.uv_loop_close
     */
    function uv_loop_close(\UVLoop &$loop): void
    {
        \uv_ffi()->uv_loop_close($loop());
        \zval_del_ref($loop);
    }

    /**
     * close uv handle.
     * Request handle to be closed. `$callback` will be called asynchronously after
     * this call. This MUST be called on each handle before memory is released.
     *
     * Note that handles that wrap file descriptors are closed immediately but
     * `$callback` will still be deferred to the next iteration of the event loop.
     * It gives you a chance to free up any resources associated with the handle.
     *
     * In-progress requests, like uv_connect or uv_write, are cancelled and
     * have their callbacks called asynchronously with status=UV_ECANCELED.
     *
     * @param UV|uv_handle_t $handle
     * @param callable $callback - expect (\UV $handle, int $status)
     * @link http://docs.libuv.org/en/v1.x/handle.html?highlight=uv_close#c.uv_close
     */
    function uv_close(\UV $handle, ?callable $callback = null)
    {
        return UV::close($handle, $callback);
    }

    /**
     * Stop the event loop, causing uv_run() to end as soon as possible.
     * This will happen not sooner than the next loop iteration.
     * If this function was called before blocking for i/o,
     * the loop won’t block for i/o on this iteration.
     *
     * @param UVLoop $loop
     * @return void
     * @link http://docs.libuv.org/en/v1.x/loop.html#c.uv_stop
     */
    function uv_stop(\UVLoop &$loop = null): void
    {
        if (\is_null($loop))
            $loop = \uv_default_loop();

        \uv_ffi()->uv_stop($loop());
        \zval_del_ref($loop);
    }

    /**
     * Return the current timestamp in milliseconds.
     *
     * The timestamp is cached at the start of the event loop tick,
     * see `uv_update_time()` for details and rationale.
     *
     * The timestamp increases monotonically from some arbitrary point in time.
     * Don’t make assumptions about the starting point, you will only get disappointed.
     *
     * `Note:` Use `uv_hrtime()` if you need sub-millisecond granularity.
     *
     * @return int
     */
    function uv_now(\UVLoop $loop = null): int
    {
        if (\is_null($loop))
            $loop = \uv_default_loop();

        return \uv_ffi()->uv_now($loop());
    }

    /**
     * Initialize the async handle. A NULL callback is allowed.
     * Note: Unlike other handle initialization functions, it immediately starts the handle.
     *
     * @param UVLoop $loop
     * @param callable $callback expect (\UVAsync $handle)
     *
     * @return UVAsync|int
     * @link http://docs.libuv.org/en/v1.x/async.html?highlight=uv_async_send#c.uv_async_init
     */
    function uv_async_init(\UVLoop $loop, callable $callback)
    {
        return UVAsync::init($loop, $callback);
    }

    /**
     * Wake up the event loop and call the async handle’s callback.
     *
     * `Note:` It’s safe to call this function from any thread.
     * The callback will be called on the loop thread.
     *
     * `Note:` uv_async_send() is async-signal-safe.
     * It’s safe to call this function from a signal handler.
     *
     * `Warning:` libuv will coalesce calls to `uv_async_send()`, that is, not every call to it
     * will yield an execution of the callback. For example: if `uv_async_send()` is called
     * 5 times in a row before the callback is called, the callback will only be called once.
     * If `uv_async_send()` is called again after the callback was called, it will be called again.
     *
     * @param UVAsync|uv_async_t $handle uv async handle.
     * @return int
     * @link http://docs.libuv.org/en/v1.x/async.html?highlight=uv_async_send#c.uv_async_send
     */
    function uv_async_send(\UVAsync $handle)
    {
        return \uv_ffi()->uv_async_send($handle());
    }

    /**
     * Create a pair of connected `resource` pipe handles in **array**.
     * - First is for _writing_.
     * - Second is for _reading_ .
     *
     * The resulting handles can be passed to `uv_pipe_open`, used with `uv_spawn`, or for any other purpose.
     *
     * Valid values for flags are:
     *- `UV::NONBLOCK_PIPE` Opens the specified socket handle for OVERLAPPED or FIONBIO/O_NONBLOCK I/O usage.
     * - This is recommended for handles that will be used by libuv, and not usually recommended otherwise.
     *
     * *Equivalent to pipe(2) with the UV::O_CLOEXEC flag set.*
     *
     * @param integer $read_flags
     * @param integer $write_flags
     * @return array<resource,resource>|int
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_bind#c.uv_pipe
     */
    function uv_pipe(int $read_flags = \UV::NONBLOCK_PIPE, int $write_flags = \UV::NONBLOCK_PIPE)
    {
        return UVPipe::pair($read_flags, $write_flags);
    }

    /**
     * Initialize a pipe handle.
     * The ipc argument is a boolean to indicate if this pipe will be used for
     * handle passing between processes (which may change the bytes on the wire).
     *
     * @param UVLoop $loop
     * @param int $ipc when use for ipc, set `true` otherwise `false`.
     * - Note: needs to be `false` on Windows for proper operations.
     *
     * @return UVPipe
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_open#c.uv_pipe_init
     */
    function uv_pipe_init(\UVLoop $loop, int $ipc): ?UVPipe
    {
        return UVPipe::init($loop, $ipc);
    }

    /**
     * Open an existing file descriptor or HANDLE as a pipe.
     *
     * The file descriptor is set to non-blocking mode.
     *
     * `Note:` The passed file descriptor or HANDLE is not checked for its type,
     * but it’s required that it represents a valid pipe.
     *
     * @param UVPipe $handle
     * @param int|resource $fd
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/search.html?q=uv_pipe_open
     */
    function uv_pipe_open(\UVPipe $handle, $fd, bool $emulated = true)
    {
        return $handle->open($fd, $emulated);
    }

    /**
     * Constructor for `UVBuffer`, filled with `data`.
     *
     * @param string $data
     * @return UVBuffer
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_buf_init#c.uv_buf_init
     */
    function uv_buf_init(?string $data)
    {
        return UVBuffer::init($data);
    }

    /**
     * Constructor for `uv_buf_t`.
     *
     * @param integer $size
     * @return uv_buf_t
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_buf_init#c.uv_buf_init
     */
    function uv_buf_init_alloc(int $size)
    {
        return \ffi_ptr(\uv_ffi()->uv_buf_init(
            \FFI::new('char[' . ($size + 1) . ']'),
            $size
        ));
    }

    /**
     * Write `data` to `stream` handle.
     * - Note The memory pointed to by the `$data` must remain valid until the callback gets called.
     * This also holds for uv_write2().
     *
     * @param UVStream|uv_stream_t $handle
     * @param string $data
     * @param callable|uv_write_cb $callback expect (\UV $handle, int $status)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_write#c.uv_write
     */
    function uv_write(\UVStream $handle, string $data, callable $callback = null): int
    {
        $buffer = \uv_buf_init($data);
        $req = \UVWriter::init('struct uv_write_s');
        $writer = $req();
        $writer->data = \ffi_void($handle());
        $r = \uv_ffi()->uv_write($writer, \uv_stream($handle), $buffer(), 1, \is_null($callback)
            ? function () {
            }
            :  function (object $writer, int $status) use ($callback, $handle) {
                $callback($handle, $status);
                $handle = $writer->data;
                \FFI::free($writer->data);
                \FFI::free($writer);
                \FFI::free($handle);
                \zval_del_ref($callback);
            });

        if ($r) {
            \ze_ffi()->zend_error(\E_WARNING, "write failed");
            \zval_del_ref($req);
            \zval_del_ref($buffer);
        } else {
            \zval_add_ref($req);
        }

        return $r;
    }

    /**
     * Read data from an incoming stream.
     *
     * The `uv_read` callback will be made several times until there is no more data to read
     * or `uv_read_stop()` is called.
     *
     * @param UVTcp|UVPipe|UVTty|uv_stream_t $handle
     * @param callable|uv_read_cb $callback expect (\UVStream $handle, $nRead, $data)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_read_alloc#c.uv_read_start
     */
    function uv_read_start(\UVStream $handle, callable $callback): int
    {
        $r = \UVStream::read($handle, $callback);
        if ($r) {
            \ze_ffi()->zend_error(\E_NOTICE, "read failed");
        }

        return $r;
    }

    /**
     * Stop reading data from the stream. The `uv_read` callback will no longer be called.
     *
     * This function is idempotent and may be safely called on a stopped stream.
     *
     * - This function will always succeed; hence, checking its return value is unnecessary.
     * A non-zero return indicates that finishing releasing resources may be pending on the next
     * input event on that TTY on Windows, and does not indicate failure.
     *
     * @param UVTcp|UVPipe|UVTty|uv_stream_t $handle UV handle which started uv_read.
     * @return int
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_read_alloc#c.uv_read_stop
     */
    function uv_read_stop(\UVStream $handle): int
    {
        return \uv_ffi()->uv_read_stop(\uv_stream($handle));
    }

    /**
     * Initialize a new TTY stream with the given file descriptor.
     *
     * Usually the file descriptor will be:
     *  - 0 = stdin
     *  - 1 = stdout
     *  - 2 = stderr
     *
     * On Unix this function will determine the path of the fd of the terminal using ttyname_r(3),
     * open it, and use it if the passed file descriptor refers to a TTY.
     *
     * This lets libuv put the tty in non-blocking mode without affecting other processes that share the tty.
     *
     * This function is not thread safe on systems that don’t support ioctl TIOCGPTN or TIOCPTYGNAME,
     * for instance OpenBSD and Solaris.
     *
     * `Note:` If reopening the TTY fails, `libuv` falls back to blocking writes.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource|int $fd
     * @param int $readable unused
     *
     * @return UVTty|int
     * @link http://docs.libuv.org/en/v1.x/tty.html?highlight=uv_tty_set_mode#c.uv_tty_init
     */
    function uv_tty_init(\UVLoop $loop, $fd, int $readable)
    {
        return \UVTty::init($loop, \fd_from(\zval_stack(1)), $readable);
    }

    /**
     * Set the TTY using the specified terminal mode.
     *
     * @param UVTty $tty
     * @param int $mode
     * - `UV::TTY_MODE_NORMAL` - Initial/normal terminal mode
     * - `UV::TTY_MODE_RAW` - Raw input mode (On Windows, ENABLE_WINDOW_INPUT is also enabled)
     * - `UV::TTY_MODE_IO` - Binary-safe I/O mode for IPC (Unix-only)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/tty.html?highlight=uv_tty_set_mode#c.uv_tty_set_mode
     */
    function uv_tty_set_mode(\UVTty $tty, int $mode)
    {
        return \uv_ffi()->uv_tty_set_mode($tty(), $mode);
    }

    /**
     * To be called when the program exits.
     *
     * Resets TTY settings to default values for the next process to take over.
     *
     * This function is async signal-safe on Unix platforms but can fail with error code
     * UV_EBUSY if you call it when execution is inside uv_tty_set_mode().
     *
     * @return void
     * @link http://docs.libuv.org/en/v1.x/tty.html?highlight=uv_tty_set_mode#c.uv_tty_reset_mode
     */
    function uv_tty_reset_mode()
    {
        return \uv_ffi()->uv_tty_reset_mode();
    }

    /**
     * Initialize the handle. No socket is created as of yet.
     *
     * @param UVLoop|null $loop uv_loop handle.
     *
     * @return UVTcp|int
     * @link http://docs.libuv.org/en/v1.x/tcp.html#c.uv_tcp_init
     */
    function uv_tcp_init(\UVLoop $loop = null)
    {
        if (\is_null($loop))
            $loop = \uv_default_loop();

        return UVTcp::init($loop);
    }

    /**
     * Bind the handle to an address and port.
     *
     * @param UVTcp $handle uv_tcp handle
     * @param UVSockAddr $addr uv sockaddr4 handle
     * @param int $flags
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/tcp.html#c.uv_tcp_bind
     */
    function uv_tcp_bind(\UVTcp $handle, \UVSockAddr $addr, int $flags = 0): int
    {
        return \uv_ffi()->uv_tcp_bind($handle(), \uv_sockaddr($addr), $flags);
    }

    /**
     * Convert a string containing an IPv4 addresses to a binary structure.
     *
     * @param string $ipv4_addr ipv4 address
     * @param int $port port number.
     *
     * @return UVSockAddrIPv4|int handle
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_ip4_addr#c.uv_ip4_addr
     */
    function uv_ip4_addr(string $ipv4_addr, int $port = 0)
    {
        $ip4 = \UVSockAddrIPv4::init('struct sockaddr_in');
        $status = \uv_ffi()->uv_ip4_addr($ipv4_addr, $port, $ip4());

        return $status === 0 ? $ip4 : $status;
    }

    /**
     * Convert a binary structure containing an IPv4 address to a string.
     *
     * @param UVSockAddr $address
     *
     * @return string|int
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_ip4_name#c.uv_ip4_name
     */
    function uv_ip4_name(\UVSockAddr $address)
    {
        $ptr = \ffi_characters(\UV::INET6_ADDRSTRLEN);
        $status = \uv_ffi()->uv_ip4_name($address(), $ptr, \UV::INET6_ADDRSTRLEN);

        return ($status === 0) ? \ffi_string($ptr) : $status;
    }

    /**
     * Convert a string containing an IPv6 addresses to a binary structure.
     *
     * @param string $ipv6_addr ipv6 address.
     * @param int $port port number.
     *
     * @return UVSockAddrIPv6 handle
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_ip4_addr#c.uv_ip6_addr
     */
    function uv_ip6_addr(string $ipv6_addr, int $port)
    {
        $ip6 = \UVSockAddrIPv6::init('struct sockaddr_in6');
        $status = \uv_ffi()->uv_ip6_addr($ipv6_addr, $port, $ip6());

        return $status === 0 ? $ip6 : $status;
    }

    /**
     * Convert a binary structure containing an IPv6 address to a string.
     *
     * @param UVSockAddr $address
     *
     * @return string
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_ip4_addr#c.uv_ip6_name
     */
    function uv_ip6_name(\UVSockAddr $address)
    {
        $ptr = \ffi_characters(\UV::INET6_ADDRSTRLEN);
        $status = \uv_ffi()->uv_ip6_name($address(), $ptr, \UV::INET6_ADDRSTRLEN);

        return ($status === 0) ? \ffi_string($ptr) : $status;
    }

    /**
     * This call is used in conjunction with `uv_listen()` to accept incoming connections.
     *
     * Call this function after receiving a `uv_connection` to accept the connection.
     * Before calling this function the `client` handle must be initialized.
     *
     * When the `uv_connection_cb` callback is called it is guaranteed that this function
     * will complete successfully the first time. If you attempt to use it more than once,
     * it may fail. It is suggested to only call this function once per `uv_connection_cb` call.
     *
     * `Note:` _server_ and _client_ must be handles running on the same _loop_.
     *
     * @param UVTcp|UVPipe $server uv_tcp or uv_pipe server handle.
     * @param UVTcp|UVPipe $client uv_tcp or uv_pipe client handle.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_accept#c.uv_accept
     */
    function uv_accept(object $server, object $client): int
    {
        $uv_server = \ffi_object($server);
        $uv_client = \ffi_object($client);
        $client_type = \ffi_str_typeof($uv_client);
        if (\is_typeof($uv_server, $client_type)) {
            $r = \uv_ffi()->uv_accept(\uv_stream($uv_server), \uv_stream($uv_client));
            if ($r)
                \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($r));

            return $r;
        }

        return \ze_ffi()->zend_error(
            \E_WARNING,
            'Client: \'%s\', expects server and client parameters to be either both of type UVTcp or both of type UVPipe',
            \reflect_object_name($client)
        );
    }

    /**
     * Start listening for incoming connections.
     *
     * backlog indicates the number of connections the kernel might queue, same as listen(2).
     * When a new incoming connection is received the `uv_connection_cb` callback is called.
     *
     * @param UVTcp|UVUdp|UVPipe|uv_stream_t $handle UV handle (tcp, udp and pipe).
     * @param int $backlog backlog.
     * @param callable|uv_connection_cb $callback expect (\UVStream $handle, int $status).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/stream.html#c.uv_listen
     */
    function uv_listen(\UVStream $handle, int $backlog, callable $callback): int
    {
        $r = \uv_ffi()->uv_listen(
            \uv_stream($handle),
            $backlog,
            function (object $stream, int $status) use ($callback, $handle) {
                \zval_add_ref($handle);
                $callback($handle, $status);
            }
        );

        if ($r) {
            \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($r));
            \zval_del_ref($handle);
        }

        return $r;
    }

    /**
     * Establish an IPv4 TCP connection.
     *
     * Provide an initialized TCP handle and an uninitialized uv_connect. addr
     * should point to an initialized struct sockaddr_in.
     *
     * On Windows if the addr is initialized to point to an unspecified address (0.0.0.0 or ::)
     * it will be changed to point to localhost. This is done to match the behavior of Linux systems.
     *
     * The callback is made when the connection has been established
     * or when a connection error happened.
     *
     * @param UVTcp $handle requires uv_tcp_init() handle.
     * @param UVSockAddr $addr requires uv_sockaddr handle.
     * @param callable|uv_connect_cb $callback callable expect (\UVTcp $handle, int $status).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/tcp.html?highlight=uv_tcp_connect#c.uv_tcp_connect
     */
    function uv_tcp_connect(\UVTcp $handle, \UVSockAddr $addr, callable $callback): int
    {
        $req = \UVConnect::init('struct uv_connect_s');
        \zval_add_ref($req);
        return \uv_ffi()->uv_tcp_connect(
            $req(),
            $handle(),
            \uv_sockaddr($addr),
            function (object $connect, int $status) use ($callback, $handle, $req) {
                $callback($handle, $status);
                \zval_del_ref($req);
            }
        );
    }

    /**
     * Opens a shared library. The filename is in utf-8. Returns 0 on success and -1 on error. Call `uv_dlerror()` to get the error message.
     *
     * @param string $filename
     * @return UVLib|int
     * @link http://docs.libuv.org/en/v1.x/dll.html?highlight=uv_lib_t#c.uv_dlopen
     */
    function uv_dlopen(string $filename)
    {
        return UVLib::init($filename);
    }

    /**
     * Close the shared library.
     *
     * @param UVLib $lib
     * @return void
     * @link http://docs.libuv.org/en/v1.x/dll.html?highlight=uv_lib_t#c.uv_dlclose
     */
    function uv_dlclose(\UVLib $lib)
    {
        $lib->loadClose();
    }

    /**
     * Retrieves a data pointer from a dynamic library. It is legal for a symbol to map to NULL.
     * Returns `symbol` **pointer** on success or -1 if the symbol was not found.
     *- Note: The returned object will need to be cast to be executable.
     *
     * @param \UVLib $lib
     * @param string $symbol
     * @return object|int definition
     * @link http://docs.libuv.org/en/v1.x/dll.html?highlight=uv_lib_t#c.uv_dlsym
     */
    function uv_dlsym(\UVLib $lib, string $symbol)
    {
        return $lib->loadSymbol($symbol);
    }

    /**
     * Returns the last `uv_dlopen()` or `uv_dlsym()` error message.
     *
     * @param \UVLib $lib
     * @return string
     * @link http://docs.libuv.org/en/v1.x/dll.html?highlight=uv_lib_t#c.uv_dlerror
     */
    function uv_dlerror(\UVLib $lib)
    {
        return $lib->loadError();
    }

    /**
     * Shutdown the outgoing (write) side of a duplex stream.
     *
     * It waits for pending write requests to complete. The handle should refer to a initialized
     * stream. req should be an uninitialized shutdown request struct. The cb is called after
     * shutdown is complete.
     *
     * @param UVTcp|UVPipe|UVTty|uv_stream_t $handle
     * @param callable|uv_shutdown_cb $callback - expect (\UVStream $handle, int $status)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_shutdown#c.uv_shutdown
     */
    function uv_shutdown(\UVStream $handle, callable $callback = null): int
    {
        $req = \UVShutdown::init('struct uv_shutdown_s');
        \zval_add_ref($req);
        $r = \uv_ffi()->uv_shutdown($req(), \uv_stream($handle), !\is_null($callback)
            ? function (object $shutdown, int $status) use ($callback, $handle) {
                $callback($handle, $status);
                \ffi_free($shutdown);
            } : null);

        if ($r) {
            \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($r));
            \zval_del_ref($req);
        }

        return $r;
    }

    /**
     * Returns an estimate of the default amount of parallelism a program should use.
     * Always returns a non-zero value.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/misc.html#c.uv_available_parallelism
     */
    function uv_available_parallelism()
    {
        return \uv_ffi()->uv_available_parallelism();
    }

    /**
     * For a file descriptor in the `C runtime`, get the OS-dependent handle. On UNIX, returns the `fd intact`,
     * on Windows, this calls `_get_osfhandle`.
     * - Note that the return value is still owned by the `C runtime`, any attempts to close it or to use it after closing the fd may lead to malfunction.
     *
     * @param integer $fd
     * @return uv_os_fd_t
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_get_osfhandle#c.uv_get_osfhandle
     */
    function uv_get_osfhandle($fd)
    {
        return \uv_ffi()->uv_get_osfhandle($fd);
    }

    /**
     * For a OS-dependent handle, get the file descriptor in the `C runtime`. On UNIX, returns the `os_fd intact`. On Windows, this calls `_open_osfhandle`.
     * - Note that this consumes the argument, any attempts to close it or to use it after closing the return value may lead to malfunction.
     *
     * @param uv_os_fd_t $fd
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_open_osfhandle#c.uv_open_osfhandle
     */
    function uv_open_osfhandle($fd)
    {
        return \uv_ffi()->uv_open_osfhandle($fd);
    }

    /**
     * Gets the platform dependent file descriptor equivalent.
     * The following handles are supported: `TCP`, `PIPE`, `TTY`, `UDP` and `POLL`. Passing any other handle type will fail with UV_EINVAL.
     * If a handle doesn’t have an attached file descriptor yet or the handle itself has been closed, this function will return UV_EBADF.
     *
     * - Warning Be very careful when using this function. libuv assumes it’s in control of the file descriptor so any change to it may lead to malfunction.
     *
     * @param UV|uv_handle_t $handle
     * @return Resource<uv_os_fd_t>|int
     * @link http://docs.libuv.org/en/v1.x/handle.html#c.uv_fileno
     */
    function uv_fileno(\UV $handle)
    {
        $fd = \fd_type('uv_os_fd_t');
        $status = \uv_ffi()->uv_fileno($handle(true), $fd());
        return $status === 0 ? $fd : $status;
    }

    /**
     * Used to detect what type of stream should be used with a given `$fd` file descriptor.
     *
     * Usually this will be used during initialization to guess the type of the `stdio` streams.
     *
     * @param uv_file|int $fd
     * @return int
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_guess_handle#c.uv_guess_handle
     */
    function uv_guess_handle($fd)
    {
        return \uv_ffi()->uv_guess_handle($fd);
    }

    /**
     * Get error code name.
     * - Leaks a few bytes of memory when you call it with an unknown error code.
     *
     * @param int $error_code libuv error code.
     * @return string
     * @link http://docs.libuv.org/en/v1.x/errors.html?highlight=uv_strerror#c.uv_err_name_r
     */
    function uv_err_name(int $error_code): string
    {
        return \uv_ffi()->uv_err_name($error_code);
    }

    /**
     * Get error message.
     * - Leaks a few bytes of memory when you call it with an unknown error code.
     *
     * @param int $error_code libuv error code
     * @return string
     * @link http://docs.libuv.org/en/v1.x/errors.html?highlight=uv_strerror#c.uv_strerror
     */
    function uv_strerror(int $error_code): string
    {
        return \uv_ffi()->uv_strerror($error_code);
    }

    /**
     * Returns non-zero if the handle is active, zero if it's inactive.
     *
     * What "active" means depends on the type of handle:
     *
     * - A uv_async handle is always active and cannot be deactivated, except
     *  by closing it with uv_close().
     *
     * - A UVPipe, UVTcp, UVUdp, etc. handle - basically any handle that
     *  deals with I/O - is active when it is doing something that involves I/O,
     *  like reading, writing, connecting, accepting new connections, etc.
     *
     * - A uv_check, uv_idle, uv_timer, etc. handle is active when it has
     *  been started with a call to uv_check_start(), uv_idle_start(), etc.
     *
     * Rule of thumb: if a handle of type uv_foo has a uv_foo_start()
     * function, then it's active from the moment that function is called.
     * Likewise, uv_foo_stop() deactivates the handle again.
     *
     * @param UV|uv_handle_t $handle
     *
     * @return bool
     * @link http://docs.libuv.org/en/v1.x/handle.html?highlight=uv_is_active#c.uv_is_active
     */
    function uv_is_active(\UV $handle): bool
    {
        return (bool) \uv_ffi()->uv_is_active($handle(true));
    }

    /**
     * Returns non-zero if the handle is closing or closed, zero otherwise.
     *
     * `Note:` This function should only be used between the initialization of
     * the handle and the arrival of the close callback.
     *
     * @param UV|uv_handle_t $handle
     *
     * @return bool
     * @link http://docs.libuv.org/en/v1.x/handle.html?highlight=uv_is_active#c.uv_is_closing
     */
    function uv_is_closing(\UV $handle): bool
    {
        return (bool) \uv_ffi()->uv_is_closing($handle(true));
    }

    /**
     * Returns 1 if the stream is readable, 0 otherwise.
     *
     * @param UVTcp|UVPipe|UVTty|uv_stream_t $handle
     *
     * @return bool
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_is_readable#c.uv_is_readable
     */
    function uv_is_readable(\UVStream $handle): bool
    {
        return (bool) \uv_ffi()->uv_is_readable(\uv_stream($handle));
    }

    /**
     * Returns 1 if the stream is writable, 0 otherwise.
     *
     * @param UVTcp|UVPipe|UVTty|uv_stream_t $handle
     *
     * @return bool
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_is_readable#c.uv_is_writable
     */
    function uv_is_writable(\UVStream $handle): bool
    {
        return (bool) \uv_ffi()->uv_is_writable(\uv_stream($handle));
    }

    /**
     * open specified file.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param string $path file path
     * @param string $flag
     * - `UV::O_RDONLY ` | `UV::O_WRONLY` | `UV::O_CREAT` | `UV::O_APPEND `
     * @param int $mode mode flag
     * - `UV::S_IRWXU` | `UV::S_IRUSR`
     * @param callable|uv_fs_cb $callback expect (resource $stream)
     * @return int|UVFs
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_open#c.uv_fs_open
     */
    function uv_fs_open(\UVLoop $loop, string $path, int $flag, int $mode = \UV::S_IRWXU, callable $callback = null)
    {
        return UVFs::init($loop, \UV::FS_OPEN, $path, $flag, $mode, $callback);
    }

    /**
     * Cleanup request. Must be called after a request is finished to deallocate any memory
     * libuv might have allocated.
     *
     * @param UVFs $req
     * @return void
     * @link http://docs.libuv.org/en/v1.x/fs.html#c.uv_fs_req_cleanup
     */
    function uv_fs_req_cleanup(\UVFs $req)
    {
        \uv_ffi()->uv_fs_req_cleanup($req());
    }

    /**
     * close specified file descriptor.
     *
     * @param UVLoop $loop
     * @param resource $fd PHP `stream`, or `socket`
     * @param callable $callback expect (bool $success)
     */
    function uv_fs_close(\UVLoop $loop, $fd, callable $callback)
    {
    }

    /**
     * async read.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param resource $fd PHP `stream`, or `socket`
     * @param int $offset
     * @param int $length
     * @param callable $callback - `$callable` expect (resource $fd, $data).
     *
     * `$data` is > 0 if there is data available, 0 if libuv is done reading for
     * now, or < 0 on error.
     *
     * The callee is responsible for closing the `$stream` when an error happens.
     * Trying to read from the `$stream` again is undefined.
     */
    function uv_fs_read(\UVLoop $loop, $fd, int $offset, int $length, callable $callback)
    {
    }

    /**
     * async write.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param resource $fd PHP `stream`, or `socket`
     * @param string $buffer data
     * @param int $offset
     * @param callable $callback expect (resource $fd, int $result)
     */
    function uv_fs_write(\UVLoop $loop, $fd, string $buffer, int $offset = -1, callable $callback)
    {
    }

    /**
     * async fdatasync.
     * synchronize a file's in-core state with storage device
     *
     * @param UVLoop $loop
     * @param resource $fd
     * @param callable $callback expect (resource $stream, int $result)
     */
    function uv_fs_fdatasync(\UVLoop $loop, $fd, callable $callback)
    {
    }

    /**
     * async scandir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param string $path
     * @param int $flags
     * @param callable $callback expect (int|array $result_or_dir_contents)
     */
    function uv_fs_scandir(\UVLoop $loop, string $path, int $flags = 0, callable $callback)
    {
    }

    /**
     * async stat.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param string $path
     * @param callable $callback expect ($result_or_stat)
     */
    function uv_fs_stat(\UVLoop $loop, string $path, callable $callback)
    {
    }

    /**
     * async lstat.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param string $path
     * @param callable $callback expect ($result_or_stat)
     */
    function uv_fs_lstat(\UVLoop $loop, string $path, callable $callback)
    {
    }

    /**
     * async fstat,
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param resource $fd
     * @param callable $callback expect (resource $stream, int $stat)
     */
    function uv_fs_fstat(\UVLoop $loop, $fd, callable $callback)
    {
    }

    /**
     * async sendfile.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param resource $out_fd
     * @param resource $in_fd
     * @param int $offset
     * @param int $length
     * @param callable $callback expect (resource $out_fd, int $result)
     */
    function uv_fs_sendfile(\UVLoop $loop, $out_fd, $in_fd, int $offset, int $length, callable $callback)
    {
    }

    /**
     * Start checking the file at `path` for changes every `interval` milliseconds.
     *
     * Your callback is invoked with `status < 0` if `path` does not exist
     * or is inaccessible. The watcher is *not* stopped but your callback is
     * not called again until something changes (e.g. when the file is created
     * or the error reason changes).
     *
     * When `status == 0`, your callback receives pointers to the old and new
     * `uv_stat` structs. They are valid for the duration of the callback
     * only!
     *
     * For maximum portability, use multi-second intervals. Sub-second intervals
     * will not detect all changes on many file systems.
     *
     * @param UVPoll $poll
     * @param callable $callback expect (\UVPoll $poll, $status, $old, $new)
     * @param string $path
     */
    function uv_fs_poll_start(\UVPoll $poll, $callback, string $path, int $interval)
    {
    }

    /**
     * Stop file system polling for changes.
     *
     * @param UVPoll $poll
     */
    function uv_fs_poll_stop(\UVPoll $poll)
    {
    }

    /**
     * initialize file system poll handle.
     *
     * @param UVLoop $loop
     *
     * @return UVPoll
     */
    function uv_fs_poll_init(\UVLoop $loop)
    {
    }

    /**
     * Update the event loop’s concept of “now”.
     *
     * `Libuv` caches the current time at the start of the event loop tick in order
     * to reduce the number of time-related system calls.
     *
     * You won’t normally need to call this function unless you have callbacks that
     * block the event loop for longer periods of time, where “longer” is somewhat
     * subjective but probably on the order of a millisecond or more.
     *
     * @param UVLoop $loop uv_loop handle.
     *
     * @return void
     */
    function uv_update_time(\UVLoop $loop)
    {
    }

    /**
     * start polling.
     *
     * If you want to use a socket. please use `uv_poll_init_socket` instead of this.
     * Windows can't handle socket with this function.
     *
     * @param UVPoll $poll
     * @param int $events UV::READABLE and UV::WRITABLE flags.
     * @param uv_poll_cb $callback expect (\UVPoll $poll, int $status, int $events, resource $fd)
     * - the callback `$fd` parameter is the same from `uv_poll_init`.
     */
    function uv_poll_start(\UVPoll $poll, $events, ?callable $callback = null)
    {
    }

    /**
     * Initialize the poll watcher using a socket descriptor. On unix this is
     * identical to `uv_poll_init`. On windows it takes a `SOCKET` handle.
     *
     * @param UVLoop $loop
     * @param resource $socket
     *
     * @return UVPoll
     */
    function uv_poll_init_socket(\UVLoop $loop, $socket)
    {
    }

    /**
     * Initialize poll
     *
     * @param UVLoop $loop
     * @param resource $fd PHP `stream`, or `socket`
     *
     * @return UVPoll
     */
    function uv_poll_init(\UVLoop $loop, $fd)
    {
    }

    /**
     * Stops polling the file descriptor.
     *
     * @param UVPoll $poll
     */
    function uv_poll_stop(\UVPoll $poll)
    {
    }

    /**
     * initialize timer handle.
     *
     * @param UVLoop $loop
     *
     * @return UVTimer|int
     * @link http://docs.libuv.org/en/v1.x/timer.html?highlight=uv_timer_init#c.uv_timer_init
     */
    function uv_timer_init(\UVLoop $loop = null)
    {
        return \UVTimer::init($loop);
    }

    /**
     * Start the timer. `$timeout` and `$repeat` are in milliseconds.
     *
     * If timeout is zero, the callback fires on the next tick of the event loop.
     *
     * If repeat is non-zero, the callback fires first after timeout milliseconds
     * and then repeatedly after repeat milliseconds.
     *
     * @param UVTimer $timer
     * @param int $timeout
     * @param int $repeat
     * @param callable|uv_timer_cb $callback expect (\UVTimer $timer)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/timer.html?highlight=uv_timer_init#c.uv_timer_start
     */
    function uv_timer_start(\UVTimer $timer, int $timeout, int $repeat, callable $callback = null): int
    {
        if ($timeout < 0)
            return \ze_ffi()->zend_error(\E_WARNING, "timeout value have to be larger than 0. given %lld", $timeout);

        if ($repeat < 0)
            return \ze_ffi()->zend_error(\E_WARNING, "repeat value have to be larger than 0. given %lld", $repeat);

        if (\uv_is_active($timer))
            return \ze_ffi()->zend_error(\E_NOTICE, "Passed uv timer resource has been started. You don't have to call this method");

        \zval_add_ref($timer);
        return \uv_ffi()->uv_timer_start(
            $timer(),
            \is_null($callback) ? function () {
            } :  function (object $handle) use ($callback, $timer) {
                $callback($timer);
            },
            $timeout,
            $repeat
        );
    }

    /**
     * stop specified timer.
     *
     * @param UVTimer $timer
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/timer.html?highlight=uv_timer_init#c.uv_timer_stop
     */
    function uv_timer_stop(\UVTimer $timer): int
    {
        if (!\uv_is_active($timer))
            return \ze_ffi()->zend_error(\E_NOTICE, "Passed uv timer resource has been stopped. You don't have to call this method");

        $r = \uv_ffi()->uv_timer_stop($timer());
        \zval_del_ref($timer);

        return $r;
    }

    /**
     * returns current exepath. basically this will returns current php path.
     *
     * @return string
     */
    function uv_exepath()
    {
    }

    /**
     * returns current working directory.
     *
     * @return string
     */
    function uv_cwd()
    {
    }

    /**
     * returns current cpu informations
     *
     * @return array
     */
    function uv_cpu_info()
    {
    }

    /**
     * Initialize signal handle.
     *
     * @param UVLoop $loop
     *
     * @return UVSignal
     */
    function uv_signal_init(\UVLoop $loop = null)
    {
    }

    /**
     * Start the signal handle with the given callback, watching for the given signal.
     *
     * @param UVSignal $handle
     * @param callable $callback expect (\UVSignal handle, int signal)
     * @param int $signal
     */
    function uv_signal_start(\UVSignal $handle, callable $callback, int $signal)
    {
    }

    /**
     * Stop the signal handle, the callback will no longer be called.
     *
     * @param UVSignal $handle
     *
     * @return int
     */
    function uv_signal_stop(\UVSignal $handle)
    {
    }

    /**
     * Initializes the process handle and starts the process.
     * If the process is successfully spawned, this function will return `UVProcess`
     * handle. Otherwise, the negative error code corresponding to the reason it couldn’t
     * spawn is returned.
     *
     * Possible reasons for failing to spawn would include (but not be limited to) the
     * file to execute not existing, not having permissions to use the setuid or setgid
     * specified, or not having enough memory to allocate for the new process.
     *
     * @param UVLoop $loop
     * @param string $command Program to be executed.
     * @param null|array $args Command line arguments.
     * - On Windows this uses CreateProcess which concatenates the arguments into a string this can
     * cause some strange errors. See the UV_PROCESS_WINDOWS_VERBATIM_ARGUMENTS flag on uv_process_flags.
     * @param null|array $stdio the file descriptors that will be made available to the child process.
     * - The convention is that stdio[0] points to stdin, fd 1 is used for stdout, and fd 2 is stderr.
     * - Note: On Windows file descriptors greater than 2 are available to the child process only if
     * the child processes uses the MSVCRT runtime.
     * @param null|string $cwd Current working directory for the subprocess.
     * @param array $env Environment for the new process. If NULL the parents environment is used.
     * @param null|callable $callback Callback called after the process exits.
     * - Expects (\UVProcess $process, $stat, $signal)
     * @param null|int $flags stdio flags
     * - Flags specifying how the stdio container should be passed to the child.
     * @param null|array $options
     *
     * @return UVProcess
     */
    function uv_spawn(
        UVLoop $loop,
        string $command,
        array $args,
        array $stdio,
        string $cwd,
        array $env = array(),
        callable $callback,
        int $flags = 0,
        array $options = []
    ) {
    }

    /**
     * send signal to specified uv process.
     *
     * @param UVProcess $process
     * @param int $signal
     */
    function uv_process_kill(\UVProcess $process, int $signal)
    {
    }

    /**
     * Returns process id.
     *
     * @param UVProcess $process
     * @return int
     */
    function uv_process_get_pid(\UVProcess $process)
    {
    }

    /**
     * send signal to specified pid.
     *
     * @param int $pid process id
     * @param int $signal
     */
    function uv_kill(int $pid, int $signal)
    {
    }

    /**
     * Initializes a work request which will run the given `$callback` in a thread from the threadpool.
     * Once `$callback` is completed, `$after_callback` will be called on the loop thread.
     * Executes callbacks in another thread (requires Thread Safe enabled PHP).
     *
     * @param UVLoop $loop
     * @param callable $callback
     * @param callable $after_callback
     */
    function uv_queue_work(\UVLoop $loop, callable $callback, callable $after_callback)
    {
    }

    /**
     * Bind the pipe to a file path (Unix) or a name (Windows).
     *
     * @param UVPipe $handle uv pipe handle.
     * @param string $name dunnno. maybe file descriptor.
     *
     * @return int
     */
    function uv_pipe_bind(\UVPipe $handle, string $name)
    {
    }

    /**
     * Connect to the Unix domain socket or the named pipe.
     *
     * @param UVPipe $handle uv pipe handle.
     * @param string $path named pipe path.
     * @param callable $callback this callback parameter expect (\UVPipe $pipe, int $status).
     */
    function uv_pipe_connect(\UVPipe $handle, string $path, callable $callback)
    {
    }

    /**
     * Set the number of pending pipe instance handles when the pipe server is waiting for connections.
     * Note: This setting applies to Windows only.
     *
     * @param UVPipe $handle
     * @param void $count
     */
    function uv_pipe_pending_instances(\UVPipe $handle, $count)
    {
    }

    /**
     * @param UV|resource $fd
     * @param integer $flags
     *
     * @return UVStdio
     */
    function uv_stdio_new($fd, int $flags)
    {
    }

    /**
     * Initialize the `UVIdle` handle watcher.
     * Idle watchers get invoked every loop iteration.
     * This function always succeeds.
     *
     * @param UVLoop $loop uv_loop handle.
     *
     * @return UVIdle|int
     * @link http://docs.libuv.org/en/v1.x/idle.html?highlight=uv_idle_init#c.uv_idle_init
     */
    function uv_idle_init(\UVLoop $loop = null)
    {
        return \UVIdle::init($loop);
    }

    /**
     * Start the Idle handle with the given callback.
     * This function always succeeds, except when `callback` is `NULL`.
     *
     * The callbacks of idle handles are invoked once per event loop.
     *
     * The idle callback can be used to perform some very low priority activity.
     * For example, you could dispatch a summary of the daily application performance to the
     * developers for analysis during periods of idleness, or use the application’s CPU time
     * to perform SETI calculations :)
     *
     * An idle watcher is also useful in a GUI application.
     *
     * Say you are using an event loop for a file download. If the TCP socket is still being established
     * and no other events are present your event loop will pause (block), which means your progress bar
     * will freeze and the user will face an unresponsive application. In such a case queue up and idle
     * watcher to keep the UI operational.
     *
     * @param UVIdle $idle uv_idle handle.
     * @param callable|uv_idle_cb $callback expect (\UVIdle $handle)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/idle.html?highlight=uv_idle_init#c.uv_idle_start
     */
    function uv_idle_start(\UVIdle $idle, callable $callback): int
    {
        if (\uv_is_active($idle)) {
            return \ze_ffi()->zend_error(\E_WARNING, "passed uv_idle resource has already started.");
        }

        \zval_add_ref($idle);
        return \uv_ffi()->uv_idle_start($idle(), function (object $handle) use ($callback, $idle) {
            $callback($idle);
        });
    }

    /**
     * Stop the Idle handle, the callback will no longer be called.
     * This function always succeeds.
     *
     * @param UVIdle $idle uv_idle handle.
     * @return int
     * @link http://docs.libuv.org/en/v1.x/idle.html?highlight=uv_idle_init#c.uv_idle_stop
     */
    function uv_idle_stop(\UVIdle $idle): int
    {
        if (!\uv_is_active($idle)) {
            return \ze_ffi()->zend_error(\E_NOTICE, "passed uv_idle resource does not start yet.");
        }

        $status = \uv_ffi()->uv_idle_stop($idle());
        \zval_del_ref($idle);

        return $status;
    }

    /**
     * Initialize the `UVPrepare` handle watcher.
     * This function always succeeds.
     * Prepare watchers get invoked before polling for I/O events.
     *
     * Their main purpose is to integrate other event mechanisms into `libuv` and their
     * use is somewhat advanced. They could be used, for example, to track variable changes,
     * implement your own watchers.
     *
     * @param UVLoop $loop uv_loop handle.
     *
     * @return UVPrepare|int
     * @link http://docs.libuv.org/en/v1.x/prepare.html?highlight=uv_prepare_init#c.uv_prepare_init
     */
    function uv_prepare_init(\UVLoop $loop = null)
    {
        return \UVPrepare::init($loop);
    }

    /**
     * Start the Prepare handle with the given callback.
     * This function always succeeds, except when `callback` is `NULL`.
     *
     * @param UVPrepare $handle UV handle (prepare)
     * @param callable|uv_prepare_cb $callback expect (\UVPrepare $prepare).
     * @return int
     * @link http://docs.libuv.org/en/v1.x/prepare.html?highlight=uv_prepare_init#c.uv_prepare_init
     */
    function uv_prepare_start(\UVPrepare $handle, callable $callback): int
    {
        if (\uv_is_active($handle)) {
            return \ze_ffi()->zend_error(\E_WARNING, "passed uv_prepare resource has been started.");
        }

        \zval_add_ref($handle);
        return \uv_ffi()->uv_prepare_start($handle(), function (object $prepare) use ($callback, $handle) {
            $callback($handle);
        });
    }

    /**
     * Stop the Prepare handle, the callback will no longer be called.
     * This function always succeeds.
     *
     * @param UVPrepare $handle UV handle (prepare).
     * @return int
     * @link http://docs.libuv.org/en/v1.x/prepare.html?highlight=uv_prepare_init#c.uv_prepare_stop
     */
    function uv_prepare_stop(\UVPrepare $handle): int
    {
        if (!\uv_is_active($handle)) {
            return \ze_ffi()->zend_error(\E_NOTICE, "passed uv_prepare resource has been stopped.");
        }

        $status = \uv_ffi()->uv_prepare_stop($handle());
        \zval_del_ref($handle);

        return $status;
    }

    /**
     * Initialize the `UVCheck` handle watcher.
     * This function always succeeds.
     * Check watchers get invoked after polling for I/O events.
     *
     * Their main purpose is to integrate other event mechanisms into `libuv` and their
     * use is somewhat advanced. They could be used, for example, to track variable changes,
     * implement your own watchers.
     *
     * @param UVLoop $loop uv_loop handle
     *
     * @return UVCheck|int
     * @link http://docs.libuv.org/en/v1.x/check.html?highlight=uv_check_init#c.uv_check_init
     */
    function uv_check_init(\UVLoop $loop = null)
    {
        return \UVCheck::init($loop);
    }

    /**
     * Start the Check handle with the given callback.
     * This function always succeeds, except when `callback` is `NULL`.
     *
     * @param UVCheck $handle UV handle (check).
     * @param callable|uv_check_cb $callback expect (\UVCheck $check).
     * @return int
     * @link http://docs.libuv.org/en/v1.x/check.html?highlight=uv_check_init#c.uv_check_start
     */
    function uv_check_start(\UVCheck $handle, callable $callback): int
    {
        if (\uv_is_active($handle)) {
            return \ze_ffi()->zend_error(\E_WARNING, "passed uv_check resource has already started");
        }

        \zval_add_ref($handle);
        return \uv_ffi()->uv_check_start($handle(), function (object $check) use ($callback, $handle) {
            $callback($handle);
        });
    }

    /**
     * Stop the Check handle, the callback will no longer be called.
     * This function always succeeds.
     *
     * @param UVCheck $handle UV handle (check).
     * @return int
     * @link http://docs.libuv.org/en/v1.x/check.html?highlight=uv_check_init#c.uv_check_stop
     */
    function uv_check_stop(\UVCheck $handle): int
    {
        if (!\uv_is_active($handle)) {
            return \ze_ffi()->zend_error(\E_NOTICE, "passed uv_check resource hasn't start yet.");
        }

        $status = \uv_ffi()->uv_check_stop($handle());
        \zval_del_ref($handle);

        return $status;
    }

    /**
     * Reference the given handle.
     *
     * References are idempotent, that is, if a handle is already referenced calling
     * this function again will have no effect.
     *
     * `Notes: Reference counting`
     * The libuv event loop (if run in the default mode) will run until there are no active
     *  and referenced handles left. The user can force the loop to exit early by unreferencing
     * handles which are active, for example by calling `uv_unref()` after calling `uv_timer_start()`.
     *
     * A handle can be referenced or unreferenced, the refcounting scheme doesn’t use a counter,
     * so both operations are idempotent.
     *
     * All handles are referenced when active by default, see `uv_is_active()` for a more detailed
     * explanation on what being active involves.
     *
     * @param UV $uv_handle UV.
     *
     * @return void
     */
    function uv_ref(\UV $uv_handle)
    {
    }

    /**
     * Un-reference the given handle.
     *
     * References are idempotent, that is, if a handle is not referenced calling
     * this function again will have no effect.
     *
     * `Notes: Reference counting`
     * The libuv event loop (if run in the default mode) will run until there are no active
     *  and referenced handles left. The user can force the loop to exit early by unreferencing
     * handles which are active, for example by calling `uv_unref()` after calling `uv_timer_start()`.
     *
     * A handle can be referenced or unreferenced, the refcounting scheme doesn’t use a counter,
     * so both operations are idempotent.
     *
     * All handles are referenced when active by default, see `uv_is_active()` for a more detailed
     * explanation on what being active involves.
     *
     * @param UV $handle UV handle.
     *
     * @return void
     * @link http://docs.libuv.org/en/v1.x/handle.html?highlight=uv_unref#c.uv_unref
     */
    function uv_unref(\UV $handle): void
    {
        \uv_ffi()->uv_unref($handle(true));
    }

    /**
     * Bind the handle to an address and port.
     *
     * @param UVTcp $uv_tcp uv_tcp handle
     * @param UVSockAddr|resource|int $uv_sockaddr uv sockaddr6 handle.
     *
     * @return void
     * @deprecated 1.0
     */
    function uv_tcp_bind6(\UVTcp $uv_tcp, UVSockAddr $uv_sockaddr)
    {
    }

    /**
     * Extended write function for sending handles over a pipe.
     *
     * The pipe must be initialized with ipc == 1.
     *
     * `Note:` $send must be a TCP socket or pipe, which is a server or a connection
     * (listening or connected state). Bound sockets or pipes will be assumed to be servers.
     *
     * @param UVTcp|UVPipe|UVTty $handle
     * @param string $data
     * @param UVTcp|UVPipe $send
     * @param callable $callback expect (\UVStream $handle, int $status).
     *
     * @return void
     */
    function uv_write2(\UVStream $handle, string $data, $send, callable $callback)
    {
    }

    /**
     * Enable TCP_NODELAY, which disables Nagle’s algorithm.
     *
     * @param UVTcp $handle libuv tcp handle.
     * @param bool $enable true means enabled. false means disabled.
     */
    function uv_tcp_nodelay(\UVTcp $handle, bool $enable)
    {
    }

    /**
     * Establish an IPv6 TCP connection.
     *
     * Provide an initialized TCP handle and an uninitialized uv_connect. addr
     * should point to an initialized struct sockaddr_in6.
     *
     * On Windows if the addr is initialized to point to an unspecified address (0.0.0.0 or ::)
     * it will be changed to point to localhost. This is done to match the behavior of Linux systems.
     *
     * The callback is made when the connection has been established
     * or when a connection error happened.
     *
     * @param UVTcp $handle requires uv_tcp_init() handle.
     * @param UVSockAddrIPv6 $ipv6_addr requires uv_sockaddr handle.
     * @param callable $callback callable expect (\UVTcp $tcp_handle, int $status).
     *
     * @return void
     */
    function uv_tcp_connect6(\UVTcp $handle, UVSockAddrIPv6 $ipv6_addr, callable $callback)
    {
    }

    /**
     * Stop the timer, and if it is repeating restart it using the repeat value as the timeout.
     *
     * @param UVTimer $timer uv_timer handle.
     *
     * @return void
     */
    function uv_timer_again(\UVTimer $timer)
    {
    }

    /**
     * Set the repeat interval value in milliseconds.
     *
     * The timer will be scheduled to run on the given interval, regardless of
     * the callback execution duration, and will follow normal timer semantics
     * in the case of a time-slice overrun.
     *
     * For example, if a 50ms repeating timer first runs for 17ms, it will be scheduled
     * to run again 33ms later. If other tasks consume more than the 33ms following the
     * first timer callback, then the callback will run as soon as possible.
     *
     * `Note:` If the repeat value is set from a timer callback it does not immediately
     * take effect. If the timer was non-repeating before, it will have been stopped.
     * If it was repeating, then the old repeat value will have been used to schedule
     * the next timeout.

     *
     * @param UVTimer $timer uv_timer handle.
     * @param int $repeat repeat count.
     *
     * @return void
     */
    function uv_timer_set_repeat(\UVTimer $timer, int $repeat)
    {
    }

    /**
     * Get the timer repeat value.
     *
     * @param UVTimer $timer uv_timer handle.
     *
     * @return int
     */
    function uv_timer_get_repeat(\UVTimer $timer)
    {
    }

    /**
     * Asynchronous `getaddrinfo(3)`
     *
     * That returns one or more addrinfo structures, each of which contains an Internet address that
     * can be specified in a call to bind(2) or connect(2).
     *
     * The getaddrinfo() function combines the functionality provided by the gethostbyname(3)
     * and getservbyname(3) functions into a single interface.
     *
     * Either $node or $service may be NULL but not both.
     *
     * $hints is a pointer to a struct addrinfo with additional address type constraints, or NULL.
     *
     * Returns 0 on success or an error code < 0 on failure. If successful, the callback will get
     * called sometime in the future with the lookup result, which is either:
     *
     * @param UVLoop $loop
     * @param callable|uv_getaddrinfo_cb $callback callable expect (array|int $addresses_or_error).
     * @param string $node
     * @param string $service
     * @param array $hints
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/dns.html?highlight=uv_getaddrinfo_t#c.uv_getaddrinfo
     */
    function uv_getaddrinfo(\UVLoop $loop, callable $callback, string $node, ?string $service, array $hints = [])
    {
        return \UVGetAddrinfo::getaddrinfo($loop, $callback, $node, $service, $hints);
    }

    /**
     * Free the struct addrinfo. Passing NULL is allowed and is a no-op
     *
     * @param addrinfo|CData $ai
     * @return void
     * @link http://docs.libuv.org/en/v1.x/dns.html?highlight=uv_freeaddrinfo#c.uv_freeaddrinfo
     */
    function uv_freeaddrinfo(CData $ai = null)
    {
        \uv_ffi()->uv_freeaddrinfo($ai);
    }

    /**
     * Cross-platform IPv6-capable implementation of `inet_ntop(3)`. On success return `string`.
     * - convert IPv4 and IPv6 addresses from binary to text form.
     *
     * @param integer $af
     * @param CData|object $src a `struct in_addr` (in network byte order) will be casted to `void*`
     * @return string|int
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_inet_ntop#c.uv_inet_ntop
     */
    function uv_inet_ntop(int $af, object $src)
    {
        $dst = \ffi_characters(\UV::INET6_ADDRSTRLEN);
        $status = \uv_ffi()->uv_inet_ntop($af, \ffi_void($src), $dst, \UV::INET6_ADDRSTRLEN);
        return $status === 0 ? \ffi_string($dst) : $status;
    }

    /**
     * Cross-platform IPv6-capable implementation of `inet_pton(3)`. On success return `C data` pointer.
     * - convert IPv4 and IPv6 addresses from text to binary form.
     *
     * @param integer $af
     * @param string $src
     * @return CData|int `binary` pointer
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_inet_ntop#c.uv_inet_pton
     */
    function uv_inet_pton(int $af, string $src)
    {
        $dst = \uv_ffi()->new('void_t');
        $status = \uv_ffi()->uv_inet_pton($af, $src, $dst);
        return $status === 0 ? $dst : $status;
    }

    /**
     * Initialize a new UDP handle. The actual socket is created lazily. Returns 0 on success.
     *
     * @param UVLoop|null $loop loop handle or null.
     * - if not specified loop handle then use uv_default_loop handle.
     *
     * @return UVUdp UV which initialized for udp.
     */
    function uv_udp_init(\UVLoop $loop = null)
    {
    }

    /**
     * Bind the UDP handle to an IP address and port.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - address – struct sockaddr_in or struct sockaddr_in6 with the address and port to bind to.
     * - flags – Indicate how the socket will be bound, UV_UDP_IPV6ONLY and UV_UDP_REUSEADDR are supported.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param UVSockAddr $address uv sockaddr(ipv4) handle.
     * @param int $flags unused.
     *
     * @return void
     */
    function uv_udp_bind(\UVUdp $handle, UVSockAddr $address, int $flags = 0)
    {
    }

    /**
     * Bind the UDP handle to an IP6 address and port.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - address – struct sockaddr_in6 with the address and port to bind to.
     * - flags – Indicate how the socket will be bound, UV_UDP_IPV6ONLY and UV_UDP_REUSEADDR are supported.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param UVSockAddr $address uv sockaddr(ipv6) handle.
     * @param int $flags Should be 0 or UV::UDP_IPV6ONLY.
     *
     * @return void
     */
    function uv_udp_bind6(\UVUdp $handle, UVSockAddr $address, int $flags = 0)
    {
    }

    /**
     * Prepare for receiving data.
     *
     * If the socket has not previously been bound with uv_udp_bind() it is bound to 0.0.0.0
     * (the “all interfaces” IPv4 address) and a random port number.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - callback – Callback to invoke with received data.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param callable $callback callback expect (\UVUdp $handle, $data, int $flags).
     *
     * @return void
     */
    function uv_udp_recv_start(\UVUdp $handle, callable $callback)
    {
    }

    /**
     * Stop listening for incoming datagrams.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     *
     * @param UVUdp $handle
     *
     * @return void
     */
    function uv_udp_recv_stop(\UVUdp $handle)
    {
    }

    /**
     * Set membership for a multicast address
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - multicast_addr – Multicast address to set membership for.
     * - interface_addr – Interface address.
     * - membership – Should be UV_JOIN_GROUP or UV_LEAVE_GROUP.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param string $multicast_addr multicast address.
     * @param string $interface_addr interface address.
     * @param int $membership UV::JOIN_GROUP or UV::LEAVE_GROUP
     *
     * @return int 0 on success, or an error code < 0 on failure.
     */
    function uv_udp_set_membership(\UVUdp $handle, string $multicast_addr, string $interface_addr, int $membership)
    {
    }

    /**
     * Set IP multicast loop flag.
     *
     * Makes multicast packets loop back to local sockets.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - on – `true` for on, `false` for off.

     * @param UVUdp $handle UV handle (udp).
     * @param bool $enabled
     *
     * @return void
     */
    function uv_udp_set_multicast_loop(\UVUdp $handle, bool $enabled)
    {
    }

    /**
     * Set the multicast ttl.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - ttl – 1 through 255.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param int $ttl multicast ttl.
     *
     * @return void
     */
    function uv_udp_set_multicast_ttl(\UVUdp $handle, int $ttl)
    {
    }

    /**
     * Set broadcast on or off.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - on – 1 for on, 0 for off.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param bool $enabled
     *
     * @return void
     */
    function uv_udp_set_broadcast(\UVUdp $handle, bool $enabled)
    {
    }

    /**
     * Send data over the UDP socket.
     *
     * If the socket has not previously been bound with uv_udp_bind() it will be bound to 0.0.0.0
     * (the “all interfaces” IPv4 address) and a random port number.
     *
     * On Windows if the addr is initialized to point to an unspecified address (0.0.0.0 or ::)
     * it will be changed to point to localhost. This is done to match the behavior of Linux systems.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - data – to send.
     * - uv_addr – struct sockaddr_in or struct sockaddr_in6 with the address and port of the remote peer.
     * - callback – Callback to invoke when the data has been sent out.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param string $data data.
     * @param UVSockAddr $uv_addr uv_ip4_addr.
     * @param callable $callback callback expect (\UVUdp $handle, int $status).
     *
     * @return void
     */
    function uv_udp_send(\UVUdp $handle, string $data, UVSockAddr $uv_addr, callable $callback)
    {
    }

    /**
     * Send data over the UDP socket.
     *
     * If the socket has not previously been bound with uv_udp_bind() it will be bound to 0.0.0.0
     * (the “all interfaces” IPv6 address) and a random port number.
     *
     * On Windows if the addr is initialized to point to an unspecified address (0.0.0.0 or ::)
     * it will be changed to point to localhost. This is done to match the behavior of Linux systems.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - data – to send.
     * - uv_addr – struct sockaddr_in6 with the address and port of the remote peer.
     * - callback – Callback to invoke when the data has been sent out.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param string $data data.
     * @param UVSockAddrIPv6 $uv_addr6 uv_ip6_addr.
     * @param callable $callback callback expect (\UVUdp $handle, int $status).
     *
     * @return void
     */
    function uv_udp_send6(\UVUdp $handle, string $data, UVSockAddrIPv6 $uv_addr6, callable $callback)
    {
    }

    /**
     * Walk the list of handles: callable will be executed with the given arg.
     *
     * @param UVLoop $loop
     * @param callable $closure
     * @param array|null $opaque
     *
     * @return bool
     */
    function uv_walk(\UVLoop $loop, callable $closure, array $opaque = null)
    {
    }

    /**
     * Gets the load average. @see: https://en.wikipedia.org/wiki/Load_(computing)
     *
     * Note: returns [0,0,0] on Windows (i.e., it’s not implemented).
     *
     * @return array
     */
    function uv_loadavg()
    {
    }

    /**
     * Initialize rwlock handle.
     *
     * @return UVLock returns uv rwlock handle.
     */
    function uv_rwlock_init()
    {
    }

    /**
     * Set read lock.
     *
     * @param UVLock $handle UV handle (\UV rwlock).
     */
    function uv_rwlock_rdlock(\UVLock $handle)
    {
    }

    /**
     * @param UVLock $handle
     *
     * @return bool
     */
    function uv_rwlock_tryrdlock(\UVLock $handle)
    {
    }

    /**
     * Unlock read lock.
     *
     * @param UVLock $handle UV handle (\UV rwlock)
     *
     * @return void
     */
    function uv_rwlock_rdunlock(\UVLock $handle)
    {
    }

    /**
     * Set write lock.
     *
     * @param UVLock $handle UV handle (\UV rwlock).
     *
     * @return void
     */
    function uv_rwlock_wrlock(\UVLock $handle)
    {
    }

    /**
     * @param UVLock $handle
     */
    function uv_rwlock_trywrlock(\UVLock $handle)
    {
    }

    /**
     * Unlock write lock.
     *
     * @param UVLock $handle UV handle (\UV rwlock).
     */
    function uv_rwlock_wrunlock(\UVLock $handle)
    {
    }

    /**
     * Initialize mutex handle.
     *
     * @return UVLock uv mutex handle
     */
    function uv_mutex_init()
    {
    }

    /**
     * Lock mutex.
     *
     * @param UVLock $lock UV handle (\UV mutex).
     *
     * @return void
     */
    function uv_mutex_lock(\UVLock $lock)
    {
    }

    /**
     * Unlock mutex.
     *
     * @param UVLock $lock UV handle (\UV mutex).
     *
     * @return void
     */
    function uv_mutex_unlock(\UVLock $lock)
    {
    }

    /**
     * @param UVLock $lock
     *
     * @return bool
     */
    function uv_mutex_trylock(\UVLock $lock)
    {
    }

    /**
     * Initialize semaphore handle.
     *
     * @param int $value
     * @return UVLock
     */
    function uv_sem_init(int $value)
    {
    }

    /**
     * Post semaphore.
     *
     * @param UVLock $sem UV handle (\UV sem).
     *
     * @return void
     */
    function uv_sem_post(\UVLock $sem)
    {
    }

    /**
     * @param UVLock $sem
     *
     * @return void
     */
    function uv_sem_wait(\UVLock $sem)
    {
    }

    /**
     * @param UVLock $sem
     *
     * @return void
     */
    function uv_sem_trywait(\UVLock $sem)
    {
    }

    /**
     * Returns the current high-resolution real time.
     *
     * This is expressed in nanoseconds. It is relative to an arbitrary time in the past.
     * It is not related to the time of day and therefore not subject to clock drift.
     * The primary use is for measuring performance between intervals.
     *
     * `Note:` Not every platform can support nanosecond resolution; however,
     * this value will always be in nanoseconds.
     *
     * @return int
     */
    function uv_hrtime()
    {
    }

    /**
     * Async fsync.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param callable $callback callback expect (resource $fd, int $result).
     *
     * @return void
     */
    function uv_fs_fsync(\UVLoop $loop, $fd, callable $callback)
    {
    }

    /**
     * Async ftruncate.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param int $offset
     * @param callable $callback callback expect (resource $fd, int $result).
     *
     * @return void
     */
    function uv_fs_ftruncate(\UVLoop $loop, $fd, int $offset, callable $callback)
    {
    }

    /**
     * Async mkdir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param int $mode
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_mkdir(\UVLoop $loop, string $path, int $mode, callable $callback)
    {
    }

    /**
     * Async rmdir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_rmdir(\UVLoop $loop, string $path, callable $callback)
    {
    }

    /**
     * Async unlink.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_unlink(\UVLoop $loop, string $path, callable $callback)
    {
    }

    /**
     * Async rename.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $from
     * @param string $to
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_rename(\UVLoop $loop, string $from, string $to, callable $callback)
    {
    }

    /**
     * Async utime.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $path
     * @param int $utime
     * @param int $atime
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_utime(\UVLoop $loop, string $path, int $utime, int $atime, callable $callback)
    {
    }

    /**
     * Async futime.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param int $utime
     * @param int $atime
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_futime(\UVLoop $loop, $fd, int $utime, int $atime, callable $callback)
    {
    }

    /**
     * Async chmod.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $path
     * @param int $mode
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_chmod(\UVLoop $loop, string $path, int $mode, callable $callback)
    {
    }

    /**
     * Async fchmod.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param int $mode
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_fchmod(\UVLoop $loop, $fd, int $mode, callable $callback)
    {
    }

    /**
     * Async chown.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $path
     * @param int $uid
     * @param int $gid
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_chown(\UVLoop $loop, string $path, int $uid, int $gid, callable $callback)
    {
    }

    /**
     * Async fchown.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param int $uid
     * @param int $gid
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_fchown(\UVLoop $loop, $fd, int $uid, int $gid, callable $callback)
    {
    }

    /**
     * Async link.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $from
     * @param string $to
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_link(\UVLoop $loop, string $from, string $to, callable $callback)
    {
    }

    /**
     * Async symlink.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * `Note:` On Windows the flags parameter can be specified to control how the symlink will be created:
     * - UV_FS_SYMLINK_DIR: indicates that path points to a directory.
     * - UV_FS_SYMLINK_JUNCTION: request that the symlink is created using junction points.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $from
     * @param string $to
     * @param int $flags
     * @param callable $callback callback expect (int $result).
     *
     * @return void
     */
    function uv_fs_symlink(\UVLoop $loop, string $from, string $to, int $flags, callable $callback)
    {
    }

    /**
     * Async readlink.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable $callback callback expect ($result_or_link_contents).
     *
     * @return void
     */
    function uv_fs_readlink(\UVLoop $loop, string $path, callable $callback)
    {
    }

    /**
     * Async readdir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param int $flags
     * @param callable $callback callback expect ($result_or_dir_contents).
     *
     * @return void
     */
    function uv_fs_readdir(\UVLoop $loop, string $path, int $flags, callable $callback)
    {
    }

    /**
     * Initialize file change event `UVFsEvent` handle, and start the given callback.
     * This will watch the specified path for changes. `$flags` can be an ORed mask of `uv_fs_event_flags`.
     *
     * The callback will receive the following arguments:
     *
     * `handle` - `UVFsEvent` handle. The path field of the handle is the file on which the watch was set.
     * `filename` - If a directory is being monitored, this is the file which was changed. Only non-null on
     * Linux and Windows. May be null even on those platforms.
     * `events` - one of `UV::RENAME` or `UV::CHANGE`, or a bitwise OR of both.
     * `status` - Currently 0, or `error` if < 0.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable $callback callback expect (\UVFsEvent $handle, ?string $filename, int $events, int $status).
     *
     * @param int $flags - `uv_fs_event_flags` that can be passed to control its behavior.
     *
     * By default, if the fs event watcher is given a directory name, we will
     * watch for all events in that directory. This flags overrides this behavior
     * and makes fs_event report only changes to the directory entry itself. This
     * flag does not affect individual files watched.
     * This flag is currently not implemented yet on any backend.
     *
     * `UV_FS_EVENT_WATCH_ENTRY = 1`
     *
     * By default uv_fs_event will try to use a kernel interface such as inotify
     * or kqueue to detect events. This may not work on remote file systems such
     * as NFS mounts. This flag makes fs_event fall back to calling stat() on a
     * regular interval.
     * This flag is currently not implemented yet on any backend.
     *
     * `UV_FS_EVENT_STAT = 2`
     *
     * By default, event watcher, when watching directory, is not registering
     * (is ignoring) changes in its subdirectories.
     * This flag will override this behaviour on platforms that support it.
     *
     *  `UV_FS_EVENT_RECURSIVE = 4`
     *
     * @return UVFsEvent
     */
    function uv_fs_event_init(\UVLoop $loop, string $path, callable $callback, int $flags = 0)
    {
    }

    /**
     * Gets the current Window size. On success it returns 0.
     *
     * @param UVTty $tty
     * @param int $width
     * @param int $height
     *
     * @return int
     */
    function uv_tty_get_winsize(\UVTty $tty, int &$width, int &$height)
    {
    }

    /**
     * Gets the current system uptime.
     *
     * @return float
     */
    function uv_uptime()
    {
    }

    /**
     * Returns current free memory size.
     *
     * @return int
     */
    function uv_get_free_memory()
    {
    }

    /**
     * Gets memory information (in bytes).
     *
     * @return int
     */
    function uv_get_total_memory()
    {
    }

    /**
     * Gets address information about the network interfaces on the system.
     *
     * An array of count elements is allocated and returned in addresses.
     * It must be freed by the user, calling uv_free_interface_addresses().
     *
     * @return array
     */
    function uv_interface_addresses()
    {
    }

    /**
     * Change working directory.
     *
     * @param string $directory
     * @return bool
     */
    function uv_chdir(string $directory)
    {
    }

    /**
     * Get the current address to which the handle is bound.
     *
     * @param UVTcp $uv_sock
     *
     * @return array ['address'], ['port'], ['family']
     */
    function uv_tcp_getsockname(\UVTcp $uv_sock)
    {
    }

    /**
     * Get the address of the peer connected to the handle.
     *
     * @param UVTcp $uv_sock
     *
     * @return array ['address'], ['port'], ['family']
     */
    function uv_tcp_getpeername(\UVTcp $uv_sock)
    {
    }

    /**
     * Get the local IP and port of the UDP handle.
     *
     * @param UVUdp $uv_sockaddr
     *
     * @return array ['address'], ['port'], ['family']
     */
    function uv_udp_getsockname(\UVUdp $uv_sock)
    {
    }

    /**
     * Gets the resident set size (RSS) for the current process.
     *
     * @return int
     */
    function uv_resident_set_memory()
    {
    }

    /**
     * Returns UV handle type.
     *
     * @param UV|uv_handle_t $handle.
     *
     * @return int
     * The kind of the `libuv` handle.
     * - UV_UNKNOWN_HANDLE = 0;
     * - UV_ASYNC = 1;
     * - UV_CHECK = 2;
     * - UV_FS_EVENT = 3;
     * - UV_FS_POLL = 4;
     * - UV_HANDLE = 5;
     * - UV_IDLE = 6;
     * - UV_NAMED_PIPE = 7;
     * - UV_POLL = 8;
     * - UV_PREPARE = 9;
     * - UV_PROCESS = 10;
     * - UV_STREAM = 11;
     * - UV_TCP = 12;
     * - UV_TIMER = 13;
     * - UV_TTY = 14;
     * - UV_UDP = 15;
     * - UV_SIGNAL = 16;
     * - UV_FILE = 17;
     * - UV_HANDLE_TYPE_MAX = 18;
     */
    function uv_handle_get_type(\UV $handle)
    {
        return \uv_ffi()->uv_handle_get_type($handle(true));
    }

    /**
     * Open an existing file descriptor or SOCKET as a TCP handle.
     *
     * The file descriptor is set to non-blocking mode.
     *
     * `Note:` The passed file descriptor or SOCKET is not checked for its type,
     * but it’s required that it represents a valid stream socket.
     *
     * @param UVTcp $handle
     * @param int|resource $tcpfd
     *
     * @return int|false
     */
    function uv_tcp_open(\UVTcp $handle, int $tcpfd)
    {
    }

    /**
     * Opens an existing file descriptor or Windows SOCKET as a UDP handle.
     *
     * The file descriptor is set to non-blocking mode.
     *
     * `Unix only:` The only requirement of the sock argument is that it follows
     * the datagram contract (works in unconnected mode, supports sendmsg()/recvmsg(), etc).
     * In other words, other datagram-type sockets like raw sockets or netlink sockets
     * can also be passed to this function.
     *
     * `Note:` The passed file descriptor or SOCKET is not checked for its type,
     * but it’s required that it represents a valid datagram socket..
     *
     * @param UVUdp $handle
     * @param int|resource $udpfd
     *
     * @return int|false
     */
    function uv_udp_open(\UVUdp $handle, int $udpfd)
    {
    }

    ////////////////////////
    // Not part of `libuv`
    ////////////////////////

    /**
     * @param UVLoop|null $uv_loop
     *
     * @return void
     */
    function uv_run_once(\UVLoop $uv_loop = null)
    {
    }
}
