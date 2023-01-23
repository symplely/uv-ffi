<?php

declare(strict_types=1);

use FFI\CData;

if (!\function_exists('uv_loop_init')) {
    /**
     * Instructs the `UVloop` instance to **destruct** when _out of scope_, perform a _request shutdown_.
     *
     * @return void
     */
    function uv_destruct_set(): void
    {
        \ext_uv::get_module()->destruct_set();
    }

    /**
     * Initializes a `UVLoop` instance structure.
     *
     * @return UVLoop|int
     * @link http://docs.libuv.org/en/v1.x/loop.html#c.uv_loop_init
     */
    function uv_loop_init()
    {
        return UVLoop::init();
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
     * Returns non-zero if there are referenced active handles,
     * active requests or closing handles in the loop.
     *
     * @param \UVLoop $loop
     * @return boolean
     *@link http://docs.libuv.org/en/v1.x/loop.html#c.uv_loop_alive
     */
    function uv_loop_alive(\UVLoop $loop): bool
    {
        return (bool)\uv_ffi()->uv_loop_alive($loop());
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
    function uv_default_loop(): \UVLoop
    {
        return \UVLoop::default();
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
        $loop->uv_run_set();
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
     * Constructor for `UVBuffer`, **filled** with `data` or **set** to specific `size`.
     *
     * @param string|int $dataOrSize
     * @return UVBuffer
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_buf_init#c.uv_buf_init
     */
    function uv_buf_init($dataOrSize): \UVBuffer
    {
        if (\is_string($dataOrSize))
            return \UVBuffer::init($dataOrSize);
        elseif (\is_integer($dataOrSize))
            return \UVBuffer::init(null, $dataOrSize);
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
        return \UVWriter::init('struct uv_write_s')->write($handle, $data, $callback);
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
     * @param callable|uv_write_cb $callback expect (\UVStream $handle, int $status).
     *
     * @return void
     * @link http://docs.libuv.org/en/v1.x/stream.html?highlight=uv_write2#c.uv_write2
     */
    function uv_write2(\UVStream $handle, string $data, \UVStream $send, callable $callback)
    {
        return \UVWriter::init('struct uv_write_s')->write2($handle, $data, $send, $callback);
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
     * @param resource $fd
     * @param int $readable unused
     *
     * @return UVTty|int
     * @link http://docs.libuv.org/en/v1.x/tty.html?highlight=uv_tty_set_mode#c.uv_tty_init
     */
    function uv_tty_init(\UVLoop $loop, $fd, int $readable)
    {
        return \UVTty::init($loop, \get_fd_resource($fd), $readable);
    }

    /**
     * Set the TTY using the specified terminal mode.
     *
     * @param UVTty $tty
     * @param int|uv_tty_mode_t $mode
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
     * Gets the current Window size. On success it returns 0.
     *
     * @param UVTty $tty
     * @param int $width
     * @param int $height
     *
     * @return int
     */
    function uv_tty_get_winsize(\UVTty $tty, &$width, &$height)
    {
        $w = \zval_stack(1);
        $h = \zval_stack(2);

        $_width = \c_int_type('int');
        $_height = \c_int_type('int');

        $error = \uv_ffi()->uv_tty_get_winsize($tty(), $_width(), $_height());

        $w->change_value($_width()[0]);
        $h->change_value($_height()[0]);

        return $error;
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

        return \UVTcp::init($loop);
    }

    /**
     * Bind the handle to an address and port.
     *
     * @param UVTcp $handle uv_tcp handle
     * @param UVSockAddr $addr uv sockaddr handle
     * @param int $flags
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/tcp.html#c.uv_tcp_bind
     */
    function uv_tcp_bind(\UVTcp $handle, \UVSockAddr $addr, int $flags = 0): int
    {
        return $handle->bind($addr, $flags);
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
    function uv_tcp_bind6(\UVTcp $uv_tcp, \UVSockAddr $uv_sockaddr, int $flags = 0)
    {
        return \uv_tcp_bind($uv_tcp, $uv_sockaddr, $flags);
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
        $ip4 = \UVSockAddrIPv4::init();
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
    function uv_ip4_name(\UVSockAddrIPv4 $address)
    {
        $ptr = \ffi_characters(\INET6_ADDRSTRLEN);
        $status = \uv_ffi()->uv_ip4_name($address(), $ptr, \INET6_ADDRSTRLEN);

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
        $ip6 = \UVSockAddrIPv6::init();
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
    function uv_ip6_name(\UVSockAddrIPv6 $address)
    {
        $ptr = \ffi_characters(\INET6_ADDRSTRLEN);
        $status = \uv_ffi()->uv_ip6_name($address(), $ptr, \INET6_ADDRSTRLEN);

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
        $uv_server = \uv_object($server);
        $uv_client = \uv_object($client);
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
            function (CData $stream, int $status) use ($callback, $handle) {
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
        return $handle->connect($addr, $callback);
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
     * @return int
     * @deprecated 1.0
     */
    function uv_tcp_connect6(\UVTcp $handle, UVSockAddrIPv6 $ipv6_addr, callable $callback): int
    {
        return \uv_tcp_connect($handle, $ipv6_addr, $callback);
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
        return \UVShutdown::init('struct uv_shutdown_s')->shutdown($handle, $callback);
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
     * For a file descriptor in the `C runtime`, get the OS-dependent handle.
     * On UNIX, returns the `fd intact`, on Windows, this calls `_get_osfhandle`.
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
     * For a OS-dependent handle, get the file descriptor in the `C runtime`.
     * On UNIX, returns the `os_fd intact`. On Windows, this calls `_open_osfhandle`.
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
     * @return resource|int
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
    function uv_fs_req_cleanup(\UVFs $req): void
    {
        \uv_ffi()->uv_fs_req_cleanup($req());
    }

    /**
     * @param UVFs $req
     * @return uv_fs_type
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_get_type#c.uv_fs_get_type
     */
    function uv_fs_get_type(\UVFs $req): int
    {
        return \uv_ffi()->uv_fs_get_type($req());
    }

    /**
     * @param UVFs $req
     * @return ssize_t|int
     * @link http://docs.libuv.org/en/v1.x/fs.html#c.uv_fs_get_result
     */
    function uv_fs_get_result(\UVFs $req)
    {
        return \uv_ffi()->uv_fs_get_result($req());
    }

    /**
     * @param UVFs $req
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html#c.uv_fs_get_system_error
     */
    function uv_fs_get_system_error(\UVFs $req): int
    {
        return \uv_ffi()->uv_fs_get_system_error($req());
    }

    /**
     * @param UVFs $req
     * @return CData void_ptr
     * @link http://docs.libuv.org/en/v1.x/fs.html#c.uv_fs_get_ptr
     */
    function uv_fs_get_ptr(\UVFs $req): object
    {
        return \uv_ffi()->uv_fs_get_ptr($req());
    }

    /**
     * @param UVFs $req
     * @return string
     * @link http://docs.libuv.org/en/v1.x/fs.html#c.uv_fs_req_cleanup
     */
    function uv_fs_get_path(\UVFs $req): string
    {
        return \uv_ffi()->uv_fs_get_path($req());
    }

    /**
     * @param UVFs $req
     * @return CData|uv_stat_t
     * @link http://docs.libuv.org/en/v1.x/fs.html#c.uv_fs_get_statbuf
     */
    function uv_fs_get_statbuf(\UVFs $req): CData
    {
        return \uv_ffi()->uv_fs_get_statbuf($req());
    }

    /**
     * close specified file descriptor.
     *
     * @param UVLoop $loop
     * @param resource $fd PHP `stream`, or `socket`
     * @param callable $callback expect (bool $success)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_close#c.uv_fs_close
     */
    function uv_fs_close(\UVLoop $loop, $fd, callable $callback = null)
    {
        return UVFs::init($loop, \UV::FS_CLOSE, $fd, $callback);
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
     * @param callable|uv_fs_cb $callback - expect (resource $fd, $data).
     *
     * `$data` is > 0 if there is data available, 0 if libuv is done reading for
     * now, or < 0 on error.
     *
     * The callee is responsible for closing the `$stream` when an error happens.
     * Trying to read from the `$stream` again is undefined.
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_close#c.uv_fs_read
     */
    function uv_fs_read(\UVLoop $loop, $fd, int $offset, int $length, callable $callback = null)
    {
        return UVFs::init($loop, \UV::FS_READ, $fd, $offset, $length, $callback);
    }

    /**
     * async write.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param resource $fd PHP `stream`, or `socket`
     * @param string $buffer data
     * @param int $offset If the offset argument is `-1`, then the current file offset is used and updated.
     * @param callable|uv_fs_cb $callback expect (resource $fd, int $result)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_write#c.uv_fs_write
     */
    function uv_fs_write(\UVLoop $loop, $fd, string $buffer, int $offset = -1, callable $callback = null)
    {
        return UVFs::init($loop, \UV::FS_WRITE, $fd, $buffer, $offset, $callback);
    }

    /**
     * async fdatasync.
     * synchronize a file's in-core state with storage device
     *
     * @param UVLoop $loop
     * @param resource $fd
     * @param callable|uv_fs_cb $callback expect (resource $stream, int $result)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_scandir#c.uv_fs_scandir
     */
    function uv_fs_fdatasync(\UVLoop $loop, $fd, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_FDATASYNC, $fd, $callback);
    }

    /**
     * async scandir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param string $path
     * @param int $flags
     * @param callable|uv_fs_cb $callback expect (int|array $result_or_dir_contents)
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_scandir#c.uv_fs_scandir
     */
    function uv_fs_scandir(\UVLoop $loop, string $path, int $flags = 0, callable $callback = null)
    {
        return \UVFs::init($loop, \UV::FS_SCANDIR, $path, $flags, $callback);
    }

    /**
     * async stat.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param string $path
     * @param callable $callback expect ($result_or_stat)
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_stat#c.uv_fs_stat
     */
    function uv_fs_stat(\UVLoop $loop, string $path, callable $callback = null)
    {
        return \UVFs::init($loop, \UV::FS_STAT, $path, $callback);
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
        return \UVFs::init($loop, \UV::FS_LSTAT, $path, $callback);
    }

    /**
     * async fstat,
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop
     * @param resource $fd
     * @param callable $callback expect (resource $stream, int $stat)
     * @return array|int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_fstat#c.uv_fs_fstat
     */
    function uv_fs_fstat(\UVLoop $loop, $fd, callable $callback = null)
    {
        return UVFs::init($loop, \UV::FS_FSTAT, $fd, $callback);
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
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_sendfile#c.uv_fs_sendfile
     */
    function uv_fs_sendfile(\UVLoop $loop, $out_fd, $in_fd, int $offset, int $length, callable $callback = null)
    {
        return UVFs::init($loop, \UV::FS_SENDFILE, $out_fd, $in_fd, $offset, $length, $callback);
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
     * @param UVFsPoll $fs_poll
     * @param callable|uv_fs_poll_cb $callback expect (UVFsPoll $handle, int $status, array $prev_stat, array $cur_stat)
     * @param string $path
     * @param int $interval
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs_poll.html?highlight=uv_fs_poll_t#c.uv_fs_poll_start
     */
    function uv_fs_poll_start(\UVFsPoll $fs_poll, $callback, string $path, int $interval)
    {
        return $fs_poll->start($callback, $path, $interval);
    }

    /**
     * Stop the handle, the callback will no longer be called.
     *
     * @param UVFsPoll $fs_poll
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs_poll.html?highlight=uv_fs_poll_t#c.uv_fs_poll_stop
     */
    function uv_fs_poll_stop(\UVFsPoll $fs_poll)
    {
        return $fs_poll->stop();
    }

    /**
     * initialize file system poll handle.
     *
     * @param UVLoop $loop
     *
     * @return UVFsPoll|int uv_fs_poll_t
     * @link http://docs.libuv.org/en/v1.x/fs_poll.html?highlight=uv_fs_poll_t#c.uv_fs_poll_init
     */
    function uv_fs_poll_init(\UVLoop $loop = null)
    {
        return \UVFsPoll::init($loop);
    }

    function uv_fs_poll_getpath(\UVFsPoll $handle)
    {
        return $handle->getpath();
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
     * @link http://docs.libuv.org/en/v1.x/loop.html?highlight=uv_update_time#c.uv_update_time
     */
    function uv_update_time(\UVLoop $loop = null): void
    {
        if (\is_null($loop))
            $loop = \uv_default_loop();

        \uv_ffi()->uv_update_time($loop());
    }

    /**
     * Starts polling the file descriptor.
     *
     * **Events** is a bitmask made up of `UV::READABLE`, `UV::WRITABLE`, `UV::PRIORITIZED` and `UV::DISCONNECT`.
     * As soon as an event is detected the callback will be called with status set to 0, and the detected events
     * set on the events field.
     *
     * The `UV::PRIORITIZED` event is used to watch for sysfs interrupts or TCP out-of-band messages.
     *
     * The `UV::DISCONNECT` event is optional in the sense that it may not be reported and the user is free to ignore it,
     * but it can help optimize the shutdown path because an extra read or write call might be avoided.
     *
     * If an error happens while polling, status will be < 0 and corresponds with one of the `UV::E*` error codes (see Error handling). The user should not close the socket while the handle is active. If the user does that anyway, the callback
     * may be called reporting an error status, but this is not guaranteed.
     *
     * `Note:` Calling this function on a handle that is already active is fine. Doing so will update the events mask that
     * is being watched for.
     *
     * `Note:` Though `UV::DISCONNECT` can be set, it is unsupported on AIX and as such will not be set on the events
     * field in the callback.
     *
     * `Note:` If one of the events UV::READABLE or `UV::WRITABLE` are set, the callback will be called again, as long as
     * the given fd/socket remains readable or writable accordingly. Particularly in each of the following scenarios:
     *
     * -   The callback has been called because the socket became readable/writable and the callback did not conduct a read/write on this socket at all.

     * -   The callback committed a read on the socket, and has not read all the available data (when `UV::READABLE` is set).

     * -   The callback committed a write on the socket, but it remained writable afterwards (when `UV::WRITABLE` is set).

     * -   The socket has already became readable/writable before calling `uv_poll_start()` on a poll handle associated with this socket, and since then the state of the socket did not changed.
     *
     * In all of the above listed scenarios, the socket remains readable or writable and hence the callback will be called again (depending on the events set in the bit-mask). This behavior is known as level triggering.
     *
     * @param UVPoll $poll
     * @param int $events `UV::READABLE` and `UV::WRITABLE` flags.
     * @param callable|uv_poll_cb $callback expect (\UVPoll $poll, int $status, int $events, resource $fd)
     * - the callback `$fd` parameter is the same from `uv_poll_init`.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/poll.html?highlight=uv_poll_stop#c.uv_poll_start
     */
    function uv_poll_start(\UVPoll $poll, $events, callable $callback): int
    {
        return $poll->start($events, $callback);
    }

    /**
     * Initialize the `poll` watcher using a socket descriptor. On unix this is
     * identical to `uv_poll_init`. On windows it takes a `SOCKET` handle.
     *
     * @param UVLoop $loop
     * @param SOCKET $socket
     *
     * @return UVPoll|int
     */
    function uv_poll_init_socket(\UVLoop $loop, $socket)
    {
        return \UVPoll::init($loop, $socket);
    }

    /**
     * Initialize `poll` using a file descriptor.
     *
     * @param UVLoop $loop
     * @param resource $fd `stream`, or `socket`
     * - `$fd` is set to non-blocking mode.
     *
     * @return UVPoll|int
     * @link http://docs.libuv.org/en/v1.x/poll.html?highlight=uv_poll_init#c.uv_poll_init
     */
    function uv_poll_init(\UVLoop $loop, $fd)
    {
        return \UVPoll::init($loop, $fd);
    }

    /**
     * Stops polling the file descriptor, the callback will no longer be called.
     *
     * - Calling this function is effective immediately: any pending callback is also canceled,
     * even if the socket state change notification was already pending.
     *
     * @param UVPoll $poll
     * @return int
     * @link http://docs.libuv.org/en/v1.x/poll.html?highlight=uv_poll_stop#c.uv_poll_stop
     */
    function uv_poll_stop(\UVPoll $poll)
    {
        return \uv_ffi()->uv_poll_stop($poll());
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
        return $timer->start($timeout, $repeat, $callback);
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
        return $timer->stop();
    }

    /**
     * Gets the executable path, basically this will returns current php _binary_ file.
     *
     * @return string
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_chdir#c.uv_exepath
     */
    function uv_exepath(): string
    {
        $ptr = \ffi_characters(256);
        $size = \c_int_type('size_t', 'uv', \FFI::sizeof($ptr));
        $status = \uv_ffi()->uv_exepath($ptr, $size());

        return ($status === 0) ? \ffi_string($ptr) : $status;
    }

    /**
     * Change working directory.
     *
     * @param string $directory
     * @return bool
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_chdir#c.uv_chdir
     */
    function uv_chdir(string $directory): bool
    {
        return \uv_ffi()->uv_chdir($directory) === 0;
    }

    /**
     * Gets the current working directory.
     *
     * @return string
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_chdir#c.uv_cwd
     */
    function uv_cwd(): string
    {
        $ptr = \ffi_characters(256);
        $size = \c_int_type('size_t', 'uv', \FFI::sizeof($ptr));
        $status = \uv_ffi()->uv_cwd($ptr, $size());

        return ($status === 0) ? \ffi_string($ptr) : $status;
    }

    /**
     * Gets information about the CPUs on the system.
     *
     * @return array
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_cpu_info#c.uv_cpu_info
     */
    function uv_cpu_info(): array
    {
        return \UVMisc::cpu_info();
    }

    /**
     * Initialize signal handle.
     *
     * @param UVLoop $loop
     *
     * @return int|UVSignal
     * @link http://docs.libuv.org/en/v1.x/signal.html?highlight=uv_signal_init#c.uv_signal_init
     */
    function uv_signal_init(\UVLoop $loop = null)
    {
        return \UVSignal::init($loop);
    }

    /**
     * Start the signal handle with the given callback, watching for the given signal.
     *
     * @param UVSignal $handle
     * @param callable|uv_signal_cb $callback expect (\UVSignal handle, int signal)
     * @param int $signal
     * @return int
     * @link http://docs.libuv.org/en/v1.x/signal.html?highlight=uv_signal_init#c.uv_signal_start
     */
    function uv_signal_start(\UVSignal $handle, callable $callback, int $signal): int
    {
        return $handle->start($callback, $signal);
    }

    /**
     * Stop the signal handle, the callback will no longer be called.
     *
     * @param UVSignal $handle
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/signal.html?highlight=uv_signal_init#c.uv_signal_stop
     */
    function uv_signal_stop(\UVSignal $handle): int
    {
        return $handle->stop();
    }

    /**
     * Creates a container for each stdio handle or fd to be passed to a child process.
     * - Flags specify how a stdio should be transmitted to the child process.
     *
     * @param UV|resource $fd UV Stream or File Descriptor
     * @param integer $flags uv_stdio_flags:
     * - `UV::IGNORE`
     * - `UV::CREATE_PIPE`
     * - `UV::INHERIT_FD`
     * - `UV::INHERIT_STREAM`
     * - `UV::READABLE_PIPE`
     * - `UV::WRITABLE_PIPE`
     * - `UV::NONBLOCK_PIPE`
     * - `UV::OVERLAPPED_PIPE`
     *
     * @return false|UVStdio
     * @link http://docs.libuv.org/en/v1.x/process.html#c.uv_stdio_container_t
     */
    function uv_stdio_new($fd, int $flags = 0)
    {
        return (new \UVStdio())->create($fd, $flags);
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
     * @param null|array[]|UVStdio $stdio Array of **UVStdio** created with `uv_stdio_new()` which:
     * - Flags specifying how the stdio container should be passed to the child.
     * - The file descriptors that will be made available to the child process.
     * - The convention stdio[0] points to `fd 0` for stdin, `fd 1` is used for stdout, and `fd 2` is stderr.
     * - Note: On Windows file descriptors greater than 2 are available to the child process only if
     * the child processes uses the MSVCRT runtime.
     * @param null|string $cwd Current working directory for the subprocess.
     * @param array $env Environment for the new process. If NULL the parents environment is used.
     * @param null|callable|uv_exit_cb $callback Callback called after the process exits.
     * - Expects (\UVProcess $process, $stat, $signal)
     * @param null|int $flags  Various process flags that control how `uv_spawn()` behaves:
     * - `UV::PROCESS_SETUID`
     * - `UV::PROCESS_SETGID`
     * - `UV::PROCESS_WINDOWS_VERBATIM_ARGUMENTS`
     * - `UV::PROCESS_DETACHED`
     * - `UV::PROCESS_WINDOWS_HIDE`
     * - `UV::PROCESS_WINDOWS_HIDE_CONSOLE`
     * - `UV::PROCESS_WINDOWS_HIDE_GUI`
     * @param null|array $uid_gid options
     * Can change the child process’ user/group id. This happens only when the appropriate bits are set in the flags fields.
     * - Note:  This is not supported on Windows, uv_spawn() will fail and set the error to UV::ENOTSUP.
     *
     * @return int|UVProcess
     * @link http://docs.libuv.org/en/v1.x/process.html?highlight=uv_spawn#c.uv_spawn
     */
    function uv_spawn(
        UVLoop $loop,
        string $command,
        array $args,
        array $stdio,
        string $cwd = null,
        array $env = array(),
        callable $callback = null,
        int $flags = \UV::PROCESS_WINDOWS_HIDE,
        array $uid_gid = []
    ) {
        $process = \UVProcess::init($loop, 'struct _php_uv_s', 'process');
        return $process->spawn($loop, $command, $args, $stdio, $cwd, $env, $callback, $flags, $uid_gid);
    }

    /**
     * Sends the specified signal to the given process handle.
     * - Check the documentation on signal support, specially on Windows.
     *
     * @param UVProcess $process
     * @param int $signal
     * @link http://docs.libuv.org/en/v1.x/process.html?highlight=uv_spawn#c.uv_process_kill
     */
    function uv_process_kill(\UVProcess $process, int $signal)
    {
        return $process->kill($signal);
    }

    /**
     * Returns process id.
     *
     * @param UVProcess $process
     * @return int uv_pid_t
     * @link http://docs.libuv.org/en/v1.x/process.html?highlight=uv_spawn#c.uv_process_get_pid
     */
    function uv_process_get_pid(\UVProcess $process)
    {
        return $process->get_pid();
    }

    /**
     * Sends the specified signal to the given PID.
     * - Check the documentation on signal support, specially on Windows.
     *
     * @param int|uv_pid_t $pid process id
     * @param int $signal
     * @return int
     * @link http://docs.libuv.org/en/v1.x/process.html?highlight=uv_spawn#c.uv_kill
     */
    function uv_kill(int $pid, int $signal)
    {
        return \uv_ffi()->uv_kill($pid, $signal);
    }

    /**
     * Initializes a work request which will run the given `$callback` in a thread from the threadpool.
     * Once `$callback` is completed, `$after_callback` will be called on the loop thread.
     * Executes callbacks in another thread (requires Thread Safe enabled PHP).
     *
     * @param UVLoop $loop
     * @param callable|uv_work_cb $callback
     * @param callable|uv_after_work_cb $after_callback
     * @return int|UVWork
     * @link http://docs.libuv.org/en/v1.x/threadpool.html?highlight=uv_queue_work#c.uv_queue_work
     */
    function uv_queue_work(\UVLoop $loop, callable $callback, callable $after_callback)
    {
        return \UVWork::init($loop, $callback, $after_callback);
    }

    /**
     * Causes the calling thread to sleep for msec milliseconds.
     *
     * @param integer $msec
     * @return void
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_sleep#c.uv_sleep
     */
    function uv_sleep(int $msec): void
    {
        \uv_ffi()->uv_sleep($msec);
    }

    /**
     * Returns the ID of the calling thread.
     * - This is the same value that is returned in `uv_thread_create()` that created this thread.
     *
     * @return \UVThread
     * @link http://docs.libuv.org/en/v1.x/threading.html#c.uv_thread_self
     */
    function uv_thread_self(): \UVThread
    {
        return \UVThread::init('self', \uv_ffi()->uv_thread_self());
    }

    /**
     * Starts a new thread in the calling process.
     * - The new thread starts execution by invoking __routine__.
     * - __args__ is passed as the sole argument of `routine()`.
     *
     * @param callable|uv_thread_cb $routine
     * @param mixed $args
     * @return int|UVThread
     * @link http://docs.libuv.org/en/v1.x/threading.html#c.uv_thread_create
     */
    function uv_thread_create(callable $routine, $args = null)
    {
        $uv_thread = \UVThread::init();
        $status = \uv_ffi()->uv_thread_create($uv_thread(), $routine, $args);

        return $status === 0 ? $uv_thread : $status;
    }

    /**
     * Waits for the thread specified to terminate.
     * - If that thread has already terminated, then `uv_thread_join()` returns immediately.
     * - The thread specified must be joinable.
     *
     * @param UVThread $tid
     * @return int
     * @link http://docs.libuv.org/en/v1.x/threading.html#c.uv_thread_join
     */
    function uv_thread_join(\UVThread $tid)
    {
        return \uv_ffi()->uv_thread_join($tid());
    }

    /**
     * Compares two thread identifiers.
     * - If the two thread IDs are equal, returns a nonzero value; otherwise, it returns 0.
     *
     * @param UVThread $t1
     * @param UVThread $t2
     * @return int
     * @link http://docs.libuv.org/en/v1.x/threading.html#c.uv_thread_equal
     */
    function uv_thread_equal(\UVThread $t1, \UVThread $t2)
    {
        return \uv_ffi()->uv_thread_equal($t1(), $t2());
    }

    /**
     * The total thread-local storage size may be limited.
     * - That is, it may not be possible to create many TLS keys.
     *
     * @return int|\UVKey
     * @link http://docs.libuv.org/en/v1.x/threading.html?highlight=uv_key_create#c.uv_key_create
     */
    function uv_key_create()
    {
        $key = \UVKey::init();
        $status = \uv_ffi()->uv_key_create($key());

        return $status === 0 ? $key : $status;
    }

    /** @return void */
    function uv_key_delete(\UVKey &$key): void
    {
        \uv_ffi()->uv_key_delete($key());
    }

    /** @return void_ptr */
    function uv_key_get(\UVKey &$key): CData
    {
        return \uv_ffi()->uv_key_get($key());
    }

    /** @return void */
    function uv_key_set(\UVKey &$key, $value): void
    {
        \uv_ffi()->uv_key_set($key(), \ffi_void($value));
    }

    /**
     * Bind the pipe to a file path (Unix) or a name (Windows).
     * - Note: Paths on Unix get truncated to sizeof(sockaddr_un.sun_path) bytes, typically between 92 and 108 bytes.
     *
     * @param UVPipe $handle uv pipe handle.
     * @param string $named filepath.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_bind#c.uv_pipe_bind
     */
    function uv_pipe_bind(\UVPipe $handle, string $named)
    {
        $error = \uv_ffi()->uv_pipe_bind($handle(), $named);
        if ($error) {
            \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($error));
        }

        return $error;
    }

    /**
     * Connect to the Unix domain socket or the named pipe.
     * - Note: Paths on Unix get truncated to sizeof(sockaddr_un.sun_path) bytes, typically between 92 and 108 bytes.
     *
     * @param UVPipe $handle uv pipe handle.
     * @param string $path named pipe path.
     * @param callable|uv_connect_cb $callback callback expect (\UVPipe $pipe, int $status).
     * @return void
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_bind#c.uv_pipe_connect
     */
    function uv_pipe_connect(\UVPipe $handle, string $path, callable $callback): void
    {
        $handle->connect($path, $callback);
    }

    /**
     * Set the number of pending pipe instance handles when the pipe server is waiting for connections.
     * Note: This setting applies to Windows only.
     *
     * @param UVPipe $handle
     * @param int $count
     * @return void
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_pending_instances#c.uv_pipe_pending_instances
     */
    function uv_pipe_pending_instances(\UVPipe $handle, int $count): void
    {
        \uv_ffi()->uv_pipe_pending_instances($handle(), $count);
    }

    /**
     * Check if there are pending handles.
     *
     * @param \UVPipe $handle
     * @return integer
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_pending_count#c.uv_pipe_pending_count
     */
    function uv_pipe_pending_count(\UVPipe $handle): int
    {
        return \uv_ffi()->uv_pipe_pending_count($handle());
    }

    /**
     * Used to receive handles over IPC pipes.
     *
     * - First - call uv_pipe_pending_count(), if it’s > 0 then initialize a handle of the given type,
     * returned by `uv_pipe_pending_type()` and call `uv_accept(pipe, handle)`.
     *
     * @param \UVPipe $handle
     * @return integer
     * @link http://docs.libuv.org/en/v1.x/pipe.html?highlight=uv_pipe_pending_count#c.uv_pipe_pending_type
     */
    function uv_pipe_pending_type(\UVPipe $handle): int
    {
        return \uv_ffi()->uv_pipe_pending_type($handle());
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
        return \uv_ffi()->uv_idle_start($idle(), function (CData $handle) use ($callback, $idle) {
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
        return \uv_ffi()->uv_prepare_start($handle(), function (CData $prepare) use ($callback, $handle) {
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
        return \uv_ffi()->uv_check_start($handle(), function (CData $check) use ($callback, $handle) {
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
     * @link http://docs.libuv.org/en/v1.x/handle.html?highlight=uv_ref#c.uv_ref
     */
    function uv_ref(\UV $uv_handle)
    {
        \uv_ffi()->uv_ref($uv_handle(true));
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
     * Stop the timer, and if it is repeating restart it using the repeat value as the timeout.
     * - If the timer has never been started before it returns UV_EINVAL.
     *
     * @param UVTimer $timer uv_timer handle.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/timer.html?highlight=uv_timer_again#c.uv_timer_again
     */
    function uv_timer_again(\UVTimer $timer): int
    {
        if (\uv_is_active($timer)) {
            \ze_ffi()->zend_error(\E_NOTICE, "Passed uv timer resource has been started. You don't have to call this method");
            return false;
        }

        \zval_add_ref($timer);

        return \uv_ffi()->uv_timer_again($timer());
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
     * @link http://docs.libuv.org/en/v1.x/timer.html?highlight=uv_timer_again#c.uv_timer_set_repeat
     */
    function uv_timer_set_repeat(\UVTimer $timer, int $repeat)
    {
        \uv_ffi()->uv_timer_set_repeat($timer(), $repeat);
    }

    /**
     * Get the timer repeat value.
     *
     * @param UVTimer $timer uv_timer handle.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/timer.html?highlight=uv_timer_again#c.uv_timer_get_repeat
     */
    function uv_timer_get_repeat(\UVTimer $timer): int
    {
        return \uv_ffi()->uv_timer_get_repeat($timer());
    }

    /**
     * Asynchronous getnameinfo(3).
     *
     * Returns `0` on success or an `error code < 0` on failure.
     * If successful, the callback will get called sometime in the future with the lookup result;
     *
     * @param \UVLoop $loop
     * @param \UVSockAddr $addr
     * @param integer $flags
     * @param callable|uv_getnameinfo_cb $callback callable expect (int $status|string $hostname, string $service)
     * @return int|array
     * @link http://docs.libuv.org/en/v1.x/dns.html#c.uv_getnameinfo
     */
    function uv_getnameinfo(\UVLoop $loop, \UVSockAddr $addr, int $flags, callable $callback = null)
    {
        return \UVGetNameinfo::getnameinfo($loop, $callback, $addr, $flags);
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
        $dst = \ffi_characters(\INET6_ADDRSTRLEN);
        $status = \uv_ffi()->uv_inet_ntop($af, \ffi_void($src), $dst, \INET6_ADDRSTRLEN);
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
     * @return int|UVUdp UV which initialized for udp.
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_init#c.uv_udp_init
     */
    function uv_udp_init(\UVLoop $loop = null)
    {
        if (\is_null($loop))
            $loop = \uv_default_loop();

        return \UVUdp::init($loop);
    }

    /**
     * Bind the UDP handle to an IP address and port.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - address – struct sockaddr_in or struct sockaddr_in6 with the address and port to bind to.
     * - flags – Indicate how the socket will be bound, UV_UDP_IPV6ONLY and UV_UDP_REUSEADDR are supported.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param UVSockAddr $addr uv sockaddr(ipv4|ipv6) handle.
     * @param int $flags unused.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_init#c.uv_udp_bind
     */
    function uv_udp_bind(\UVUdp $handle, UVSockAddr $addr, int $flags = \UV::UDP_LINUX_RECVERR)
    {
        return $handle->bind($addr, $flags);
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
        return \uv_udp_bind($handle, $address, $flags);
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
     * @param callable|uv_udp_recv_cb $callback callback expect (\UVUdp $handle, $data, int $flags).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_recv_start#c.uv_udp_recv_start
     */
    function uv_udp_recv_start(\UVUdp $handle, callable $callback)
    {
        return $handle->recv($callback);
    }

    /**
     * Stop listening for incoming datagrams.
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     *
     * @param UVUdp $handle
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_recv_stop#c.uv_udp_recv_stop
     */
    function uv_udp_recv_stop(\UVUdp $handle)
    {
        return $handle->stop();
    }

    /**
     * Set membership for a multicast address
     *
     * - handle – UDP handle. Should have been initialized with uv_udp_init().
     * - multicast_addr – Multicast address to set membership for.
     * - interface_addr – Interface address.
     * - membership – Should be `UV::JOIN_GROUP` or `UV::LEAVE_GROUP`.
     *
     * @param UVUdp $handle UV handle (udp).
     * @param string $multicast_addr multicast address.
     * @param string $interface_addr interface address.
     * @param int $membership UV::JOIN_GROUP or UV::LEAVE_GROUP
     *
     * @return int 0 on success, or an error code < 0 on failure.
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_set_membership#c.uv_udp_set_membership
     */
    function uv_udp_set_membership(\UVUdp $handle, string $multicast_addr, string $interface_addr, int $membership)
    {
        return \uv_ffi()->uv_udp_set_membership($handle(), $multicast_addr, $interface_addr, $membership);
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
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_set_multicast_loop#c.uv_udp_set_multicast_loop
     */
    function uv_udp_set_multicast_loop(\UVUdp $handle, bool $enabled)
    {
        $r = \uv_ffi()->uv_udp_set_multicast_loop($handle(), (int)$enabled);
        if ($r) {
            \ze_ffi()->zend_error(\E_NOTICE, "uv_udp_set_muticast_loop failed");
        }

        return $r;
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
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_set_multicast_ttl#c.uv_udp_set_multicast_ttl
     */
    function uv_udp_set_multicast_ttl(\UVUdp $handle, int $ttl)
    {
        return $handle->multicast($ttl);
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
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_set_multicast_ttl#c.uv_udp_set_broadcast
     */
    function uv_udp_set_broadcast(\UVUdp $handle, bool $enabled)
    {
        $r = \uv_ffi()->uv_udp_set_broadcast($handle(), (int) $enabled);
        if ($r) {
            \ze_ffi()->zend_error(\E_NOTICE, "uv_udp_set_muticast_loop failed");
        }

        return $r;
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
     * @param callable|uv_udp_send_cb $callback callback expect (\UVUdp $handle, int $status).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_send#c.uv_udp_send
     */
    function uv_udp_send(\UVUdp $handle, string $data, UVSockAddr $uv_addr, callable $callback)
    {
        return $handle->send($data, $uv_addr, $callback);
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
        return \uv_udp_send($handle, $data, $uv_addr6, $callback);
    }

    /**
     * Walk the list of handles: callable will be executed with the given arg.
     *
     * @param UVLoop $loop
     * @param callable|uv_walk_cb $closure
     * @param array|null $opaque
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/loop.html?highlight=uv_walk#c.uv_walk
     */
    function uv_walk(\UVLoop $loop, callable $closure, array $opaque = null)
    {
        return \uv_ffi()->uv_walk($loop(), function (CData $handle, CData $args) use ($closure) {
            $closure($handle, $args);
        }, $opaque);
    }

    /**
     * Gets the load average. @see: https://en.wikipedia.org/wiki/Load_(computing)
     *
     * Note: returns [0,0,0] on Windows (i.e., it’s not implemented).
     *
     * @return array
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_loadavg#c.uv_loadavg
     */
    function uv_loadavg()
    {
        $average = \c_array_type('double', 'uv', 3);

        \uv_ffi()->uv_loadavg($average());

        return [$average()[0], $average()[1], $average()[2]];
    }

    /**
     * Initialize rwlock handle.
     *
     * @return int|UVLock returns uv rwlock handle.
     * @link http://docs.libuv.org/en/v1.x/guide/threads.html?highlight=uv_rwlock_init#locks
     */
    function uv_rwlock_init()
    {
        $lock = \UVLock::type_init('php_uv_lock_t', 'rwlock');
        $status = \uv_ffi()->uv_rwlock_init($lock());

        return $status === 0 ? $lock : $status;
    }

    /**
     * Set read lock.
     *
     * @param UVLock $handle UV handle (\UV rwlock).
     * @return void
     */
    function uv_rwlock_rdlock(\UVLock $handle)
    {
        $handle->rdlock();
    }

    /**
     * Try to set read lock.
     *
     * @param UVLock $handle
     *
     * @return bool
     */
    function uv_rwlock_tryrdlock(\UVLock $handle)
    {
        return $handle->tryrdlock();
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
        $handle->rdunlock();
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
     * Try to set write lock.
     *
     * @param UVLock $handle
     *
     * @return bool
     */
    function uv_rwlock_trywrlock(\UVLock $handle)
    {
        return $handle->trywrlock();
    }

    /**
     * Unlock write lock.
     *
     * @param UVLock $handle UV handle (\UV rwlock).
     *
     * @return void
     */
    function uv_rwlock_wrunlock(\UVLock $handle)
    {
        $handle->wrunlock();
    }

    /**
     * Initialize mutex handle.
     *
     * @return int|UVMutex mutex handle
     */
    function uv_mutex_init()
    {
        $mutex = \UVMutex::type_init('php_uv_lock_t', 'mutex');
        $status = \uv_ffi()->uv_mutex_init($mutex());

        return $status === 0 ? $mutex : $status;
    }

    /**
     * Lock mutex.
     *
     * @param UVMutex $lock mutex handle
     *
     * @return void
     */
    function uv_mutex_lock(\UVMutex $lock)
    {
    }

    /**
     * Unlock mutex.
     *
     * @param UVMutex $lock mute handle
     *
     * @return void
     */
    function uv_mutex_unlock(\UVMutex $lock)
    {
        $lock->unlock();
    }

    /**
     * Try to unlock mutex.
     *
     * @param UVMutex $lock
     *
     * @return bool
     */
    function uv_mutex_trylock(\UVMutex $lock)
    {
        return $lock->trylock();
    }

    /**
     * Initialize semaphore handle.
     *
     * @param int $value
     * @return UVSemaphore|int
     * @link http://docs.libuv.org/en/v1.x/threading.html?highlight=uv_sem_init#c.uv_sem_init
     */
    function uv_sem_init(int $value)
    {
        $semaphore = \UVSemaphore::type_init('php_uv_lock_t', 'semaphore');
        $status = \uv_ffi()->uv_sem_init($semaphore(), $value);

        return $status === 0 ? $semaphore : $status;
    }

    /**
     * Post semaphore.
     *
     * @param UVSemaphore $sem UV handle (\UV sem).
     *
     * @return void
     * @link http://docs.libuv.org/en/v1.x/threading.html?highlight=uv_sem_init#c.uv_sem_post
     */
    function uv_sem_post(\UVSemaphore $sem): void
    {
        \uv_ffi()->uv_sem_post($sem());
    }

    /**
     * @param UVSemaphore $sem
     *
     * @return void
     * @link http://docs.libuv.org/en/v1.x/threading.html?highlight=uv_sem_init#c.uv_sem_wait
     */
    function uv_sem_wait(\UVSemaphore $sem): void
    {
        \uv_ffi()->uv_sem_wait($sem());
    }

    /**
     * @param UVSemaphore $sem
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/threading.html?highlight=uv_sem_init#c.uv_sem_trywait
     */
    function uv_sem_trywait(\UVSemaphore $sem)
    {
        return \uv_ffi()->uv_sem_trywait($sem());
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
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_hrtime#c.uv_hrtime
     */
    function uv_hrtime()
    {
        return \uv_ffi()->uv_hrtime();
    }

    /**
     * Async fsync.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param callable|uv_fs_cb $callback callback expect (resource $fd, int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_fsync#c.uv_fs_fsync
     */
    function uv_fs_fsync(\UVLoop $loop, $fd, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_FSYNC, $fd, $callback);
    }

    /**
     * Async ftruncate.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param int $offset
     * @param callable|uv_fs_cb $callback callback expect (resource $fd, int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_fsync#c.uv_fs_ftruncate
     */
    function uv_fs_ftruncate(\UVLoop $loop, $fd, int $offset, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_FTRUNCATE, $fd, $offset, $callback);
    }

    /**
     * Async mkdir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param int $mode
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_mkdir#c.uv_fs_mkdir
     */
    function uv_fs_mkdir(\UVLoop $loop, string $path, int $mode, callable $callback = null)
    {
        return \UVFs::init($loop, \UV::FS_MKDIR, $path, $mode, $callback);
    }

    /**
     * Async rmdir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_rmdir#c.uv_fs_rmdir
     */
    function uv_fs_rmdir(\UVLoop $loop, string $path, callable $callback = null)
    {
        return \UVFs::init($loop, \UV::FS_RMDIR, $path, $callback);
    }

    /**
     * Async unlink.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_unlink#c.uv_fs_unlink
     */
    function uv_fs_unlink(\UVLoop $loop, string $path, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_UNLINK, $path, $callback);
    }

    /**
     * Async rename.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $from
     * @param string $to
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_rename#c.uv_fs_rename
     */
    function uv_fs_rename(\UVLoop $loop, string $from, string $to, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_RENAME, $from, $to, $callback);
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
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_utime#c.uv_fs_utime
     */
    function uv_fs_utime(\UVLoop $loop, string $path, int $utime, int $atime, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_UTIME, $path, $utime, $atime, $callback);
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
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_futime#c.uv_fs_futime
     */
    function uv_fs_futime(\UVLoop $loop, $fd, int $utime, int $atime, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_FUTIME, $fd, $utime, $atime, $callback);
    }

    /**
     * Async chmod.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $path
     * @param int $mode
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_chmod#c.uv_fs_chmod
     */
    function uv_fs_chmod(\UVLoop $loop, string $path, int $mode, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_CHMOD, $path, $mode, $callback);
    }

    /**
     * Async fchmod.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param resource $fd
     * @param int $mode
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_fchmod#c.uv_fs_fchmod
     */
    function uv_fs_fchmod(\UVLoop $loop, $fd, int $mode, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_FCHMOD, $fd, $mode, $callback);
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
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_chown#c.uv_fs_chown
     */
    function uv_fs_chown(\UVLoop $loop, string $path, int $uid, int $gid, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_CHOWN, $path, $uid, $gid, $callback);
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
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_fchown#c.uv_fs_fchown
     */
    function uv_fs_fchown(\UVLoop $loop, $fd, int $uid, int $gid, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_FCHOWN, $fd, $uid, $gid, $callback);
    }

    /**
     * Async link.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $from
     * @param string $to
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_link#c.uv_fs_link
     */
    function uv_fs_link(\UVLoop $loop, string $from, string $to, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_LINK, $from, $to, $callback);
    }

    /**
     * Async symlink.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * `Note:` On Windows the flags parameter can be specified to control how the symlink will be created:
     * - `UV::FS_SYMLINK_DIR`: indicates that path points to a directory.
     * - `UV::FS_SYMLINK_JUNCTION`: request that the symlink is created using junction points.
     *
     * @param UVLoop $loop uv_loop handle.
     * @param string $from
     * @param string $to
     * @param int $flags
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_symlink#c.uv_fs_symlink
     */
    function uv_fs_symlink(\UVLoop $loop, string $from, string $to, int $flags, callable $callback)
    {
        return \UVFs::init($loop, \UV::FS_SYMLINK, $from, $to, $flags, $callback);
    }

    /**
     * Async readlink.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param callable|uv_fs_cb $callback callback expect (int $result).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/fs.html?highlight=uv_fs_readlink#c.uv_fs_readlink
     */
    function uv_fs_readlink(\UVLoop $loop, string $path, callable $callback = null)
    {
        return \UVFs::init($loop, \UV::FS_READLINK, $path, $callback);
    }

    /**
     * Async readdir.
     * Executes a blocking system call asynchronously (in a thread pool) and call the specified callback in
     * the specified loop after completion.
     *
     * @param UVLoop $loop uv_loop handle
     * @param string $path
     * @param int $flags
     * @param callable|uv_fs_cb $callback callback expect ($result_or_dir_contents).
     *
     * @return int
     * @deprecated 1.0
     */
    function uv_fs_readdir(\UVLoop $loop, string $path, int $flags, callable $callback = null)
    {
        \ze_ffi()->zend_error(\E_DEPRECATED, "Use uv_fs_scandir() instead of uv_fs_readdir()");
        return \uv_fs_scandir($loop, $path, $flags, $callback);
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
     * @param callable|uv_fs_event_cb $callback callback expect (\UVFsEvent $handle, ?string $filename, int $events, int $status).
     *
     * @param int $flags `uv_fs_event_flags` that can be passed to control its behavior.
     * - `UV::FS_EVENT_WATCH_ENTRY`
     * - `UV::FS_EVENT_STAT`
     * - `UV::FS_EVENT_RECURSIVE`
     *
     * @return UVFsEvent|int
     * @link http://docs.libuv.org/en/v1.x/fs_event.html?highlight=uv_fs_event_init#c.uv_fs_event_init
     */
    function uv_fs_event_init(\UVLoop $loop, string $path, callable $callback, int $flags = 0)
    {
        return \UVFsEvent::init($loop, $path, $callback, $flags);
    }

    /**
     * Start the handle with the given callback, which will watch the specified path for changes. flags can be an ORed mask of `uv_fs_event_flags`.
     *
     * - Note: Currently the only supported flag is UV_FS_EVENT_RECURSIVE and only on OSX and Windows.
     *
     * @param \UVFsEvent $fs_event
     * @param string $path
     * @param callable|uv_fs_event_cb $callback callback expect (\UVFsEvent $handle, ?string $filename, int $events, int $status).
     * @param int $flags `uv_fs_event_flags` that can be passed to control its behavior.
     * - `UV::FS_EVENT_WATCH_ENTRY`
     * - `UV::FS_EVENT_STAT`
     * - `UV::FS_EVENT_RECURSIVE`
     * @return UVFsEvent|int
     * @link http://docs.libuv.org/en/v1.x/fs_event.html?highlight=uv_fs_event_init#c.uv_fs_event_start
     */
    function uv_fs_event_start(\UVFsEvent $fs_event, string $path, callable $callback, int $flags = 0)
    {
        \zval_add_ref($fs_event);
        $error = $fs_event->start($callback, $path, $flags);
        if ($error < 0) {
            \zval_del_ref($fs_event);
            \ze_ffi()->zend_error(\E_ERROR, "uv_fs_event_start failed");
            return $error;
        }

        return $fs_event;
    }

    /**
     * Stop the handle, the callback will no longer be called.
     *
     * @param \UVFsEvent $fs_event
     * @return integer
     * @link http://docs.libuv.org/en/v1.x/fs_event.html?highlight=uv_fs_event_init#c.uv_fs_event_stop
     */
    function uv_fs_event_stop(\UVFsEvent $fs_event): int
    {
        $status = \uv_ffi()->uv_fs_event_stop($fs_event());
        \zval_del_ref($fs_event);

        return $status;
    }

    /**
     * Get the path being monitored by the handle. The buffer must be preallocated by the user.
     * - Returns 0 on success or an error code < 0 in case of failure.
     * - On success, buffer will contain the path and size its length.
     *
     * If the buffer is not big enough `UV::ENOBUFS` will be returned and size will be set to the required size, including the null terminator.
     *
     * @param uv_fs_event_t $handle
     * @param char $buffer
     * @param size_t $size
     * @return string|int
     * @link http://docs.libuv.org/en/v1.x/fs_event.html?highlight=uv_fs_event_init#c.uv_fs_event_getpath
     */
    function uv_fs_event_getpath(\UVFsEvent $handle)
    {
        $buffer = \ffi_characters(\INET6_ADDRSTRLEN);
        $size = \INET6_ADDRSTRLEN;
        $status = \uv_ffi()->uv_fs_event_getpath($handle(), $buffer, $size);
        if ($status === \UV::ENOBUFS) {
            $buffer = \ffi_characters($size);
            $status = \uv_ffi()->uv_fs_event_getpath($handle(), $buffer, $size);
        }

        return $status === 0 ? \ffi_string($buffer) : $status;
    }

    /**
     * Gets the current system uptime. Depending on the system full or fractional seconds are returned.
     *
     * @return float
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_resident_set_memory#c.uv_uptime
     */
    function uv_uptime()
    {
        $size = \c_int_type('double', 'uv');
        $status = \uv_ffi()->uv_uptime($size());

        return $status === 0 ? $size()[0] : $status;
    }

    /**
     * Gets the amount of free memory available in the system, as reported by the kernel (in bytes).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_get_free_memory#c.uv_get_free_memory
     */
    function uv_get_free_memory()
    {
        return \uv_ffi()->uv_get_free_memory();
    }

    /**
     * Gets the total amount of physical memory in the system (in bytes).
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_get_free_memory#c.uv_get_total_memory
     */
    function uv_get_total_memory()
    {
        return \uv_ffi()->uv_get_total_memory();
    }

    /**
     * Gets the resident set size (RSS) for the current process.
     *
     * @return int
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_resident_set_memory#c.uv_resident_set_memory
     */
    function uv_resident_set_memory()
    {
        $size = \c_int_type('size_t', 'uv');
        $status = \uv_ffi()->uv_resident_set_memory($size());

        return $status === 0 ? $size()[0] : $status;
    }

    /**
     * Gets address information about the network interfaces on the system.
     *
     * An array of count elements is allocated and returned in addresses.
     * It must be freed by the user, calling uv_free_interface_addresses().
     *
     * @return array
     * @link http://docs.libuv.org/en/v1.x/misc.html?highlight=uv_interface_addresses#c.uv_interface_addresses
     */
    function uv_interface_addresses()
    {
        return \UVMisc::interface_addresses();
    }

    /**
     * Get the current address to which the handle is bound.
     *
     * @param UVTcp $uv_sock
     *
     * @return array ['address'], ['port'], ['family']
     * @link http://docs.libuv.org/en/v1.x/tcp.html?highlight=uv_tcp_getsockname#c.uv_tcp_getsockname
     */
    function uv_tcp_getsockname(\UVTcp $uv_sock): array
    {
        return $uv_sock->get_name(1);
    }

    /**
     * Get the address of the peer connected to the handle.
     *
     * @param UVTcp $uv_sock
     *
     * @return array ['address'], ['port'], ['family']
     * @link http://docs.libuv.org/en/v1.x/tcp.html?highlight=uv_tcp_getpeername#c.uv_tcp_getpeername
     */
    function uv_tcp_getpeername(\UVTcp $uv_sock)
    {
        return $uv_sock->get_name(2);
    }

    /**
     * Get the local IP and port of the UDP handle.
     *
     * @param UVUdp $uv_sockaddr
     *
     * @return array ['address'], ['port'], ['family']
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_getsockname#c.uv_udp_getsockname
     */
    function uv_udp_getsockname(\UVUdp $uv_sock)
    {
        return $uv_sock->get_name(3);
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
     * @link http://docs.libuv.org/en/v1.x/handle.html?highlight=uv_handle_get_type#c.uv_handle_get_type
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
     * @link http://docs.libuv.org/en/v1.x/tcp.html?highlight=uv_tcp_open#c.uv_tcp_open
     */
    function uv_tcp_open(\UVTcp $handle, $tcpfd)
    {
        return $handle->open($tcpfd);
    }

    /**
     * Enable TCP_NODELAY, which disables Nagle’s algorithm.
     *
     * @param UVTcp $handle libuv tcp handle.
     * @param bool $enable true means enabled. false means disabled.
     * @return int
     * @link http://docs.libuv.org/en/v1.x/tcp.html?highlight=uv_tcp_nodelay#c.uv_tcp_nodelay
     */
    function uv_tcp_nodelay(\UVTcp $handle, bool $enable)
    {
        return \uv_ffi()->uv_tcp_nodelay($handle, (int) $enable);
    }

    /**
     * Enable / disable simultaneous asynchronous accept requests that are queued by the operating
     * system when listening for new TCP connections.
     *
     * - This setting is used to tune a TCP server for the desired performance. Having simultaneous accepts
     * can significantly improve the rate of accepting connections (which is why it is enabled by default)
     * but may lead to uneven load distribution in multi-process setups.
     *
     * @param \UVUdp $handle
     * @param bool $enable true means enabled. false means disabled.
     * @return int
     * @link http://docs.libuv.org/en/v1.x/tcp.html?highlight=uv_tcp_simultaneous_accepts#c.uv_tcp_simultaneous_accepts
     */
    function uv_tcp_simultaneous_accepts(\UVTcp $handle, bool $enable)
    {
        return  \uv_ffi()->uv_tcp_simultaneous_accepts($handle(), (int) $enable);
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
     * @link http://docs.libuv.org/en/v1.x/udp.html?highlight=uv_udp_open#c.uv_udp_open
     */
    function uv_udp_open(\UVUdp $handle, $udpfd)
    {
        return $handle->open($udpfd);
    }
}
