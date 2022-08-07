<?php

declare(strict_types=1);

use FFI\CData;

if (!\class_exists('UVLoop')) {
    /**
     * The event loop is the central part of `libuv's` functionality.
     * It takes care of polling for i/o and scheduling callbacks to
     * be run based on different sources of events.
     * @return uv_loop_t **pointer** by invoking `$UVLoop()`
     */
    final class UVLoop
    {
        /** @var uv_Loop_t */
        protected ?CData $uv_loop;
        protected static ?CData $uv_loop_ptr = null;
        protected static ?UVLoop $uv_default = null;

        public function __destruct()
        {
            \uv_ffi()->uv_stop(self::$uv_loop_ptr); /* in case we longjmp()'ed ... */
            \uv_ffi()->uv_run(self::$uv_loop_ptr, \UV::RUN_DEFAULT); /* invalidate the stop ;-) */
            //  uv_walk(loop, destruct_uv_loop_walk_cb, NULL);
            // uv_run(loop, UV_RUN_DEFAULT);
            \uv_ffi()->uv_loop_close(self::$uv_loop_ptr);
            $this->free();
            Core::clear_stdio();
            Core::clear_ffi();
        }

        protected function free()
        {
            self::$uv_default = null;
            if (\is_cdata(self::$uv_loop_ptr) && !\is_null_ptr(self::$uv_loop_ptr)) {
                \FFI::free(self::$uv_loop_ptr);

                self::$uv_loop_ptr = null;
                $this->uv_loop = null;
            }
        }

        protected function __construct(bool $compile = true, ?string $library = null, ?string $include = null, $default = false)
        {
            \uv_init($compile, $library, $include);
            \zend_init();

            if (!$default) {
                $this->uv_loop = \uv_struct("struct uv_loop_s");
                self::$uv_loop_ptr = \ffi_ptr($this->uv_loop);
                self::$uv_default = $this;
            }
        }

        public function __invoke(): CData
        {
            return self::$uv_loop_ptr;
        }

        public static function default(bool $compile = true, string $library = null, string $include = null): self
        {
            if (!self::$uv_default instanceof \UVLoop)
                self::$uv_default = new self($compile, $library, $include, true);

            if (!\is_cdata(self::$uv_loop_ptr))
                self::$uv_loop_ptr = \uv_ffi()->uv_default_loop();

            return self::$uv_default;
        }

        public static function init(bool $compile = true, ?string $library = null, ?string $include = null)
        {
            $loop = new self($compile, $library, $include);
            $status = \uv_ffi()->uv_loop_init($loop());

            return ($status === 0) ? $loop : $status;
        }
    }
}

if (!\class_exists('UVAsync')) {
    /**
     * Async handles allow the user to wakeup the event loop and get a callback called from another thread.
     * @return uv_async_t **pointer** by invoking `$UVAsync()`
     */
    final class UVAsync extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            $async = new self('struct _php_uv_s', 'async');
            $callback = \reset($arguments);
            $status = \uv_ffi()->uv_async_init($loop(), $async(), function () use ($callback, $async) {
                $callback($async);
            });

            return ($status === 0) ? $async : $status;
        }
    }
}

if (!\class_exists('UVRequest')) {
    /**
     * The base `uv_req_t` class type for all libuv `request` types.
     */
    abstract class UVRequest extends \UVTypes
    {
        public function __invoke(bool $by_req = false): ?\FFI\CData
        {
            if ($by_req)
                return \uv_request($this->uv_type_ptr);

            return $this->uv_type_ptr;
        }

        public function free(): void
        {
            if (\is_cdata($this->uv_type_ptr) && \is_typeof($this->uv_type_ptr, 'struct uv_fs_s*'))
                \uv_ffi()->uv_fs_req_cleanup($this->uv_type_ptr);

            parent::free();
        }

        public static function cancel(object $req)
        {
        }
    }
}

if (!\class_exists('UVStream')) {
    /**
     * Stream handles provide an abstraction of a duplex communication channel.
     * `UVStream` is an abstract type, `libuv` provides 3 stream implementations
     * in the form of `UVTcp`, `UVPipe` and `UVTty`
     * @return uv_stream_t **pointer** by invoking `$UVStream()`
     */
    class UVStream extends \UV
    {
        /**
         * @param UV|object $handle
         * @param callable|uv_read_cb $callback
         * @return integer
         */
        public static function read(object $handle, callable $callback): int
        {
            if (!\uv_fileno($handle) instanceof Resource) {
                return \ze_ffi()->zend_error(\E_WARNING, "passed UV handle is not initialized yet");
            }

            $r = \uv_ffi()->uv_read_start(
                \uv_stream($handle),
                function (object $handle, int $suggested_size, CData $buf) {
                    $buf->base = \FFI::new('char[' . ($suggested_size + 1) . ']', false);
                    $buf->len = $suggested_size;
                },
                function (object $stream, int $nRead, CData $data) use ($callback, $handle) {
                    if ($nRead > 0 || $nRead === \UV::EOF)
                        \zval_add_ref($handle);

                    $callback($handle, $nRead, ($nRead > 0) ? \FFI::string($data->base) : null);
                    if ($nRead > 0)
                        \FFI::free($data->base);

                    \zval_del_ref($callback);
                }
            );

            if ($r) {
                \ze_ffi()->zend_error(\E_NOTICE, \uv_strerror($r));
            }

            return $r;
        }
    }
}

if (!\class_exists('UVPipe')) {
    /**
     * Pipe handles provide an abstraction over streaming files on
     * Unix (including local domain sockets, pipes, and FIFOs) and named pipes on Windows.
     * @return uv_pipe_t **pointer** by invoking `$UVPipe()`
     */
    final class UVPipe extends \UVStream
    {
        protected function emulated($io): void
        {
            $pipe = new static('struct _php_uv_s', 'pipe');
            \uv_ffi()->uv_pipe_init(\UVLoop::default()(), $pipe(), 0);
            \uv_ffi()->uv_pipe_open($pipe(), $io);
            $handler = \uv_stream($pipe);
            // $handler->data = \ffi_void($pipe(true));
            \uv_ffi()->uv_read_start(
                $handler,
                function (object $handle, int $suggested_size, CData $buf) {
                    $buf->base = \FFI::new('char[' . ($suggested_size + 1) . ']', false);
                    $buf->len = $suggested_size;
                },
                function (object $stream, int $nRead, CData $data) use ($pipe) {
                    if ($nRead > 0)
                        \ze_ffi()->_php_stream_printf(\stream_stdout(), \FFI::string($data->base));

                    if ($nRead <= 0) {
                        $handler = $pipe(true);
                        if (!\uv_is_closing($pipe)) {
                            $fd = $handler->u->fd;
                            if (Resource::is_valid($fd))
                                Resource::remove_fd($fd);
                            elseif (PhpStream::is_valid($fd))
                                PhpStream::remove_fd($fd);

                            \uv_ffi()->uv_close($handler, null);
                        }

                        \FFI::free($data->base);
                        \FFI::free($stream->data);
                        \FFI::free($stream);
                        \FFI::free($handler->data);
                        \FFI::free($handler);

                        $writer = $this->__invoke(true);
                        \FFI::free($writer->data);
                        \FFI::free($writer);

                        $pipe->free();
                        $this->free();
                    }
                }
            );

            \zval_add_ref($pipe);
        }

        public function open($pipe, bool $emulated = true)
        {
            $io = $pipe;
            $isPipeEmulated = false;
            if (\is_resource($io)) {
                if (\get_resource_type($io) === 'uv_pipe') {
                    $io = Resource::get_fd((int)$pipe, false, true);
                } elseif (\IS_WINDOWS && $emulated) {
                    $which = ($io === \STDOUT || $io === \STDERR) ? 1 : 0;
                    $pipe = static::pair(\UV::NONBLOCK_PIPE, \UV::NONBLOCK_PIPE, false);
                    $io = $pipe[$which];
                    $isPipeEmulated = true;
                } else {
                    $io = \fd_from(\zval_stack(0));
                }
            }

            $status = \uv_ffi()->uv_pipe_open($this->__invoke(), $io);
            if ($isPipeEmulated && $which === 1)
                $this->emulated($pipe[0]);

            return $status;
        }

        /**
         * @param int $read_flags
         * @param int $write_flags
         * @param boolean $getResource
         * @return array<resource,resource>|int
         */
        public static function pair(
            int $read_flags = \UV::NONBLOCK_PIPE,
            int $write_flags = \UV::NONBLOCK_PIPE,
            bool $getResource = true
        ) {
            $pipe = \fd_type();
            $fd = $pipe();
            $status = \uv_ffi()->uv_pipe($fd, $read_flags, $write_flags);

            if ($status === 0) {
                $f1 = $fd[1];
                $f0 = $fd[0];
                $zval_1 = \zval_resource(\zend_register_resource(
                    $f1,
                    \zend_register_list_destructors_ex(function (CData $rsrc) {
                    }, null, "uv_pipe", 20220101)
                ));

                $zval_2 = \zval_resource(\zend_register_resource(
                    $f0,
                    \zend_register_list_destructors_ex(function (CData $rsrc) {
                    }, null, "uv_pipe", 20220101)
                ));

                $ht = \zend_new_pair($zval_1(), $zval_2());
                $zval_3 = \zval_array($ht);
                $array = \zval_native($zval_3);

                $pipe->add_pair($zval_3, $f1, (int)$array[1], $f0, (int)$array[0]);
                if ($getResource)
                    return $array;

                return $fd;
            }

            return $status;
        }

        /** @return static|int */
        public static function init(?\UVLoop $loop, ...$arguments)
        {
            $pipe = new static('struct _php_uv_s', 'pipe');
            $status = \uv_ffi()->uv_pipe_init($loop(), $pipe(), \reset($arguments));
            return ($status === 0) ? $pipe : $status;
        }
    }
}

if (!\class_exists('UVTty')) {
    /**
     * TTY handles represent a stream for the console.
     * @return uv_tty_t **pointer** by invoking `$UVTty()`
     */
    final class UVTty extends \UVStream
    {
        public static function init(?\UVLoop $loop, ...$arguments)
        {
            $tty = new static('struct _php_uv_s', 'tty');
            $status = \uv_ffi()->uv_tty_init($loop(), $tty(), \array_shift($arguments), \reset($arguments));
            return ($status === 0) ? $tty : $status;
        }
    }
}

if (!\class_exists('UVTcp')) {
    /**
     * TCP handles are used to represent both TCP streams and servers.
     * @return uv_tcp_t **pointer** by invoking `$UVTcp()`
     */
    final class UVTcp extends \UVStream
    {
        public static function init(?\UVLoop $loop, ...$arguments)
        {
            $tcp = new static('struct _php_uv_s', 'tcp');
            $status = \uv_ffi()->uv_tcp_init($loop(), $tcp());
            return ($status === 0) ? $tcp : $status;
        }
    }
}

if (!\class_exists('UVUdp')) {
    /**
     * UDP handles encapsulate UDP communication for both clients and servers.
     * @return uv_udp_t **pointer** by invoking `$UVUdp()`
     */
    final class UVUdp extends \UV
    {
    }
}

if (!\class_exists('UVPoll')) {
    /**
     * Poll handles are used to watch file descriptors for readability, writability
     * and disconnection similar to the purpose of poll(2).
     *
     * The purpose of poll handles is to enable integrating external libraries that rely on
     * the event loop to signal it about the socket status changes, like c-ares or libssh2.
     * Using `UVPoll` for any other purpose is not recommended; `UVTcp`, `UVUdp`, etc.
     * provide an implementation that is faster and more scalable than what can be achieved
     * with `UVPoll`, especially on Windows.
     *
     * It is possible that poll handles occasionally signal that a file descriptor is readable
     * or writable even when it isn't. The user should therefore always be prepared to handle
     * EAGAIN or equivalent when it attempts to read from or write to the fd.
     *
     * It is not okay to have multiple active poll handles for the same socket, this can cause
     * libuv to busyloop or otherwise malfunction.
     *
     * The user should not close a file descriptor while it is being polled by an active poll
     * handle. This can cause the handle to report an error, but it might also start polling
     * another socket. However the fd can be safely closed immediately after a call to
     * uv_poll_stop() or uv_close().
     *
     * Note: On windows only sockets can be polled with poll handles. On Unix any file descriptor that would be accepted by poll(2) can be used.
     *
     * Note: On AIX, watching for disconnection is not supported.
     * @return uv_poll_t **pointer** by invoking `$UVPoll()`
     */
    final class UVPoll extends \UV
    {
    }
}

if (!\class_exists('UVTimer')) {
    /**
     * Timer handles are used to schedule callbacks to be called in the future.
     * @return uv_timer_t **pointer** by invoking `$UVTimer()`
     */
    final class UVTimer extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $timer = new self('struct _php_uv_s', 'timer');
            $status = \uv_ffi()->uv_timer_init($loop(), $timer());
            return $status === 0 ? $timer : $status;
        }
    }
}

if (!\class_exists('UVSignal')) {
    /**
     * Signal handles implement Unix style signal handling on a per-event loop bases.
     *
     * UNIX signal handling on a per-event loop basis. The implementation is not
     * ultra efficient so don't go creating a million event loops with a million
     * signal watchers.
     *
     * Note to Linux users: `SIGRT0` and `SIGRT1` (signals 32 and 33) are used by the
     * NPTL pthreads library to manage threads. Installing watchers for those
     * signals will lead to unpredictable behavior and is strongly discouraged.
     * Future versions of libuv may simply reject them.
     *
     * Some signal support is available on `Windows`:
     *
     *   `SIGINT` is normally delivered when the user presses CTRL+C. However, like
     *   on Unix, it is not generated when terminal raw mode is enabled.
     *
     *   `SIGBREAK` is delivered when the user pressed CTRL+BREAK.
     *
     *   `SIGHUP` is generated when the user closes the console window. On `SIGHUP` the
     *   program is given approximately 10 seconds to perform cleanup. After that
     *   Windows will unconditionally terminate it.
     *
     *   `SIGWINCH` is raised whenever libuv detects that the console has been
     *   resized. `SIGWINCH` is emulated by libuv when the program uses an uv_tty_t
     *   handle to write to the console. `SIGWINCH` may not always be delivered in a
     *   timely manner; libuv will only detect size changes when the cursor is
     *   being moved. When a readable uv_tty_handle is used in raw mode, resizing
     *   the console buffer will also trigger a `SIGWINCH` signal.
     *
     * Watchers for other signals can be successfully created, but these signals
     * are never generated. These signals are: `SIGILL`, `SIGABRT`, `SIGFPE`, `SIGSEGV`,
     * `SIGTERM` and `SIGKILL`.
     *
     * Note that calls to raise() or abort() to programmatically raise a signal are
     * not detected by libuv; these will not trigger a signal watcher.
     * @return uv_signal_t **pointer** by invoking `$UVSignal()`
     */
    final class UVSignal extends \UV
    {
    }
}

if (!\class_exists('UVProcess')) {
    /**
     * Process handles will spawn a new process and allow the user to control it and
     * establish communication channels with it using streams.
     * @return uv_process_t **pointer** by invoking `$UVProcess()`
     */
    final class UVProcess extends \UV
    {
    }
}

if (!\class_exists('UVIdle')) {
    /**
     * Idle handles will run the given callback once per loop iteration, right before
     * the `UVPrepare` handles.
     *
     * `Note:` The notable difference with prepare handles is that when there are active idle
     *  handles, the loop will perform a zero timeout poll instead of blocking for i/o.
     *
     * `Warning:` Despite the name, idle handles will get their callbacks called on every loop
     *  iteration, not when the loop is actually "idle".
     * @return uv_idle_t **pointer** by invoking `$UVIdle()`
     */
    final class UVIdle extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $idle = new self('struct _php_uv_s', 'idle');
            $status = \uv_ffi()->uv_idle_init($loop(), $idle());
            return $status === 0 ? $idle : $status;
        }
    }
}

if (!\class_exists('UVPrepare')) {
    /**
     * Prepare handles will run the given callback once per loop iteration, right before
     * polling for i/o.
     * @return uv_prepare_t **pointer** by invoking `$UVIdle()`
     */
    final class UVPrepare extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $prepare = new self('struct _php_uv_s', 'prepare');
            $status = \uv_ffi()->uv_prepare_init($loop(), $prepare());
            return $status === 0 ? $prepare : $status;
        }
    }
}

if (!\class_exists('UVCheck')) {
    /**
     * Check handles will run the given callback once per loop iteration, right after polling for i/o.
     * @return uv_check_t **pointer** by invoking `$UVCheck()`
     */
    final class UVCheck extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $check = new self('struct _php_uv_s', 'check');
            $status = \uv_ffi()->uv_check_init($loop(), $check());
            return $status === 0 ? $check : $status;
        }
    }
}

if (!\class_exists('UVStdio')) {
    /**
     * Stdio is an I/O wrapper to be passed to uv_spawn().
     * @return uv_stdio_container_t **pointer** by invoking `$UVStdio()`
     */
    final class UVStdio
    {
    }
}

if (!\class_exists('UVSockAddr')) {
    /**
     * Address and port base structure
     * @return sockaddr_in by invoking `$UVSockAddr()`
     */
    abstract class UVSockAddr extends \UVTypes
    {
    }
}

if (!\class_exists('UVSockAddrIPv4')) {
    /**
     * IPv4 Address and port structure
     * @deprecated 1.0
     */
    final class UVSockAddrIPv4 extends \UVSockAddr
    {
    }
}

if (!\class_exists('UVSockAddrIPv6')) {
    /**
     * IPv6 Address and port structure
     * @deprecated 1.0
     */
    final class UVSockAddrIPv6 extends \UVSockAddr
    {
    }
}

if (!\class_exists('UVLock')) {
    /**
     * Lock handle (Lock, Mutex, Semaphore)
     *
     * `libuv` provides cross-platform implementations for multiple threading and synchronization primitives.
     *
     * The API largely follows the pthreads API.
     * @return uv_rwlock_t **pointer** by invoking `$UVLock()`
     */
    final class UVLock
    {
    }
}

if (!\class_exists('UVGetAddrinfo')) {
    final class UVGetAddrinfo extends \UVRequest
    {
        /**
         * @param UVLoop $loop
         * @param callable|uv_getaddrinfo_cb $callback callable expect (array|int $addresses_or_error).
         * @param string $node
         * @param string $service
         * @param array $hints
         *
         * @return int
         */
        public static function getaddrinfo(\UVLoop $loop, callable $callback, string $node, ?string $service, array $hints = [])
        {
            $addrinfo = \Addrinfo::init('struct addrinfo');
            $hint = $addrinfo();
            if (!\is_null($hints)) {
                /** @var HashTable */
                $h = HashTable::init_value(\zval_stack(4)()->value->arr);
                if ($data = $h->str_find("ai_family")) {
                    $hint->ai_family = $data->macro(ZE::LVAL_P);
                }

                if (($data = $h->str_find("ai_socktype"))) {
                    $hint->ai_socktype = $data->macro(ZE::LVAL_P);
                }

                if (($data = $h->str_find("ai_protocol"))) {
                    $hint->ai_socktype = $data->macro(ZE::LVAL_P);
                }

                if (($data = $h->str_find("ai_flags"))) {
                    $hint->ai_flags = $data->macro(ZE::LVAL_P);
                }
            }

            $addrinfo_req = new static('struct uv_getaddrinfo_s');

            return \uv_ffi()->uv_getaddrinfo(
                $loop(),
                $addrinfo_req(),
                function (object $handle, int $status, $res) use ($callback, $addrinfo_req, $addrinfo) {
                    if ($status != 0) {
                        $result = null;
                    } else {
                        $params = \zval_array(\ze_ffi()->_zend_new_array(0));
                        $address = $res;
                        while (!\is_null($address)) {
                            if ($address->ai_family == \AF_INET) {
                                $ip = \uv_inet_ntop(
                                    $address->ai_family,
                                    (\is_null($address->ai_addr)
                                        ? $address
                                        : \ffi_ptr(\uv_cast('struct sockaddr_in*', $address->ai_addr)->sin_addr))
                                );
                                \ze_ffi()->add_next_index_string($params(), $ip);
                            }

                            $address = $address->ai_next;
                        }

                        $address = $res;
                        while (!\is_null($address)) {
                            if ($address->ai_family == \AF_INET6) {
                                $ip = \uv_inet_ntop(
                                    $address->ai_family,
                                    (\is_null($address->ai_addr)
                                        ? $address
                                        : \ffi_ptr(\uv_cast('struct sockaddr_in6*', $address->ai_addr)->sin6_addr))
                                );
                                \ze_ffi()->add_next_index_string($params(), $ip);
                            }

                            $address = $address->ai_next;
                        }

                        $result = \zval_native($params);
                    }

                    $callback($status, $result);

                    unset($result);
                    \uv_freeaddrinfo($res);
                    \zval_del_ref($callback);
                    $addrinfo_req->free();
                    $addrinfo->free();
                },
                $node,
                $service,
                $addrinfo()
            );
        }
    }
}

if (!\class_exists('UVFs')) {
    /**
     * File system operations. All functions defined in this document take a callback, which is allowed to be NULL.
     * If the callback is NULL the request is completed synchronously, otherwise it will be performed asynchronously.
     *
     * All file operations are run on the threadpool. See Thread pool work scheduling for information on the threadpool size.

     * - Note: On Windows uv_fs_* functions use utf-8 encoding.
     * @link http://docs.libuv.org/en/v1.x/guide/filesystem.html?highlight=uv_fs_cb#filesystem-operations
     * @return uv_fs_t **pointer** by invoking `$UVFs()`
     */
    final class UVFs extends \UVRequest
    {
        protected ?int $index = null;
        protected static $instances = null;
        protected static int $type_index = 0;

        protected ?string $uv_fs_type = null;

        protected function __construct(string $typedef, string $fs_type)
        {
            parent::__construct($typedef);
            $this->uv_fs_type = $fs_type;
        }

        public static function init(...$arguments)
        {
            /*
            $loop = \array_shift($arguments);
            $fs_type = \array_shift($arguments);
            $fdCharObject = \array_shift($arguments);
            $callback = \array_pop($arguments);
            if (\is_string($fdCharObject)) {
                $uv_fSystem = new static('struct uv_fs_s', (string) $fs_type);
                $fSystem = $uv_fSystem();
                $fSystem->cb = $callback;
                \array_push($arguments, $fSystem->cb);
                switch ($fs_type) {
                    case \UV::FS_OPEN:
                        $result = \uv_ffi()->uv_fs_open($loop(), $fSystem, $fdCharObject, ...$arguments);
                        break;
                    case \UV::FS_UNLINK:
                        $result = \uv_ffi()->uv_fs_unlink($loop(), $fSystem, $fdCharObject, ...$arguments);
                        break;
                }

                $uv_fSystem->add($result);
            } elseif ($fdCharObject instanceof Resource || \is_resource($fdCharObject)) {
                if (static::is_valid($fdCharObject))
                    $fSystem =  static::get_request($fdCharObject);

                $fSystem->cb = $callback;
                \array_push($arguments, $fSystem->cb);
                switch ($fs_type) {
                    case \UV::FS_CLOSE:
                        $result = \uv_ffi()->uv_fs_close($loop(), $fSystem, $fdCharObject, ...$arguments);
                        break;
                }
            }

            return  $result;
            /*
\uv_ffi()->uv_fs_close(uv_loop_t* loop, uv_fs_t* req, uv_file file, uv_fs_cb cb);
\uv_ffi()->uv_fs_read(uv_loop_t* loop, uv_fs_t* req, uv_file file, const uv_buf_t bufs[], unsigned int nbufs, int64_t offset, uv_fs_cb cb);
\uv_ffi()->uv_fs_write(uv_loop_t* loop, uv_fs_t* req, uv_file file, const uv_buf_t bufs[], unsigned int nbufs, int64_t offset, uv_fs_cb cb);
\uv_ffi()->uv_fs_fstat(uv_loop_t* loop, uv_fs_t* req, uv_file file, uv_fs_cb cb);
\uv_ffi()->uv_fs_fsync(uv_loop_t* loop, uv_fs_t* req, uv_file file, uv_fs_cb cb);
\uv_ffi()->uv_fs_fdatasync(uv_loop_t* loop, uv_fs_t* req, uv_file file, uv_fs_cb cb);
\uv_ffi()->uv_fs_ftruncate(uv_loop_t* loop, uv_fs_t* req, uv_file file, int64_t offset, uv_fs_cb cb);
\uv_ffi()->uv_fs_sendfile(uv_loop_t* loop, uv_fs_t* req, uv_file out_fd, uv_file in_fd, int64_t in_offset, size_t length, uv_fs_cb cb);
\uv_ffi()->uv_fs_futime(uv_loop_t* loop, uv_fs_t* req, uv_file file, double atime, double mtime, uv_fs_cb cb);
\uv_ffi()->uv_fs_fchmod(uv_loop_t* loop, uv_fs_t* req, uv_file file, int mode, uv_fs_cb cb);
\uv_ffi()->uv_fs_fchown(uv_loop_t* loop, uv_fs_t* req, uv_file file, uv_uid_t uid, uv_gid_t gid, uv_fs_cb cb);

\uv_ffi()->uv_fs_open(uv_loop_t* loop, uv_fs_t* req, const char* path, int flags, int mode, uv_fs_cb cb);
\uv_ffi()->uv_fs_unlink(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_copyfile(uv_loop_t* loop, uv_fs_t* req, const char* path, const char* new_path, int flags, uv_fs_cb cb);
\uv_ffi()->uv_fs_mkdir(uv_loop_t* loop, uv_fs_t* req, const char* path, int mode, uv_fs_cb cb);
\uv_ffi()->uv_fs_mkdtemp(uv_loop_t* loop, uv_fs_t* req, const char* tpl, uv_fs_cb cb);
\uv_ffi()->uv_fs_mkstemp(uv_loop_t* loop, uv_fs_t* req, const char* tpl, uv_fs_cb cb);
\uv_ffi()->uv_fs_rmdir(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_rename(uv_loop_t* loop, uv_fs_t* req, const char* path, const char* new_path, uv_fs_cb cb);
\uv_ffi()->uv_fs_access(uv_loop_t* loop, uv_fs_t* req, const char* path, int mode, uv_fs_cb cb);
\uv_ffi()->uv_fs_chmod(uv_loop_t* loop, uv_fs_t* req, const char* path, int mode, uv_fs_cb cb);
\uv_ffi()->uv_fs_utime(uv_loop_t* loop, uv_fs_t* req, const char* path, double atime, double mtime, uv_fs_cb cb);
\uv_ffi()->uv_fs_lutime(uv_loop_t* loop, uv_fs_t* req, const char* path, double atime, double mtime, uv_fs_cb cb);
\uv_ffi()->uv_fs_lstat(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_link(uv_loop_t* loop, uv_fs_t* req, const char* path, const char* new_path, uv_fs_cb cb);
\uv_ffi()->uv_fs_symlink(uv_loop_t* loop, uv_fs_t* req, const char* path, const char* new_path, int flags, uv_fs_cb cb);
\uv_ffi()->uv_fs_readlink(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_realpath(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_chown(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_uid_t uid, uv_gid_t gid, uv_fs_cb cb);
\uv_ffi()->uv_fs_lchown(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_uid_t uid, uv_gid_t gid, uv_fs_cb cb);
\uv_ffi()->uv_fs_stat(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_statfs(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);
\uv_ffi()->uv_fs_scandir(uv_loop_t* loop, uv_fs_t* req, const char* path, int flags, uv_fs_cb cb);
\uv_ffi()->uv_fs_opendir(uv_loop_t* loop, uv_fs_t* req, const char* path, uv_fs_cb cb);

\uv_ffi()->uv_fs_scandir_next(uv_fs_t* req, uv_dirent_t* ent);
\uv_ffi()->uv_fs_readdir(uv_loop_t* loop, uv_fs_t* req, uv_dir_t* dir, uv_fs_cb cb);
\uv_ffi()->uv_fs_closedir(uv_loop_t* loop, uv_fs_t* req, uv_dir_t* dir, uv_fs_cb cb);
*/
        }

        public function add(int $fd): self
        {
            if (!isset(static::$instances[$fd])) {
                static::$instances[$fd] = $this;
                $this->index = $fd;
            }

            return $this;
        }

        public function clear(): void
        {
            if (isset(static::$instances[$this->index])) {
                static::$instances[$this->index] = null;
                $this->index = null;
            }
        }

        /**
         * @param resource $fd
         * @return bool
         */
        public static function is_valid($fd): bool
        {
            return isset(static::$instances[$fd]) && static::$instances[$fd] instanceof static;
        }

        /**
         * @param resource $fd
         * @return uv_fs_t
         */
        public static function get_request($fd)
        {
            if (static::is_valid($fd)) {
                /** @var UVfs */
                $fs = static::$instances[$fd];
                return $fs();
            }
        }
    }
}

if (!\class_exists('UVFsEvent')) {
    /**
     * FS Event handles allow the user to monitor a given path for changes, for example,
     * if the file was renamed or there was a generic change in it.
     *
     * This handle uses the best backend for the job on each platform.
     *
     * `inotify` on Linux.
     *
     * `FSEvents` on Darwin.
     *
     * `kqueue` on BSDs.
     *
     * `ReadDirectoryChangesW` on Windows.
     *
     * `event ports` on Solaris.
     *
     * `unsupported` on Cygwin
     * @return uv_fs_event_t **pointer** by invoking `$UVFsEvent()`
     */
    final class UVFsEvent extends \UVRequest
    {
    }
}

if (!\class_exists('UVBuffer')) {
    /**
     * Buffer data type.
     *
     * @return uv_buf_t **pointer** by invoking `$UVBuffer()`
     */
    final class UVBuffer extends \UVTypes
    {
        protected function __construct(string $data = null, CData $init = null)
        {
            if (\is_null($init)) {
                parent::__construct('struct uv_buf_t');
            } else {
                $this->uv_type_ptr = $init;
            }

            if (!\is_null($data)) {
                $this->uv_type_ptr->base = \ffi_char($data);
                $this->uv_type_ptr->len = \strlen($data);
            }
        }

        public function free(): void
        {
            if (
                \is_null($this->uv_type)
                && \is_cdata($this->uv_type_ptr->base)
                && !\is_null_ptr($this->uv_type_ptr->base)
            )
                \FFI::free($this->uv_type_ptr->base);

            parent::free();
        }

        public function getString()
        {
            if (\is_cdata($this->uv_type_ptr->base) && !\is_null_ptr($this->uv_type_ptr->base)) {
                return \FFI::string($this->uv_type_ptr->base);
            }
        }

        public static function init($data = null, ...$arguments)
        {
            return new static($data, \is_null($data) ? null : \uv_buf_init_alloc(\strlen($data)));
        }
    }
}
if (!\class_exists('Addrinfo')) {
    final class Addrinfo extends \UVTypes
    {
    }
}

if (!\class_exists('UVWriter')) {
    /**
     * Write request type.
     *
     * Careful attention must be paid when reusing objects of this type. When a stream is in non-blocking mode, write
     * requests sent with `uv_write` will be queued. Reusing objects at this point is _undefined behavior_.
     *
     * - It is safe to reuse the `UVWriter` object only after the callback passed to uv_write is fired.
     *
     * @return uv_write_t **pointer** by invoking `$UVWriter()`
     */
    // final class UVWriter extends \UVTypes
    final class UVWriter extends \UVRequest
    {
    }
}

if (!\class_exists('UVShutdown')) {
    /**
     * @return uv_shutdown_t **pointer** by invoking `$UVShutdown()`
     */
    final class UVShutdown extends \UVRequest
    {
    }
}

if (!\class_exists('UVConnect')) {
    /**
     * @return uv_connect_t **pointer** by invoking `$UVConnect()`
     */
    final class UVConnect extends \UVRequest
    {
    }
}

if (!\class_exists('UVLib')) {
    /**
     * Provides cross platform way of loading shared libraries and retrieving a `symbol` from them.
     *
     * @return symbol _definition_ **pointer** by invoking `$UVLib()`
     */
    final class UVLib extends \UVTypes
    {
        protected ?CData $uv_symbol;
        protected ?CData $uv_symbol_ptr;

        public function __invoke()
        {
            return $this->getSymbol();
        }

        public function loadOpen($filename): int
        {
            return \uv_ffi()->uv_dlopen($filename, $this->uv_type_ptr);
        }

        public function free(): void
        {
            $this->loadClose();
        }

        public function loadClose(): void
        {
            if (\is_cdata($this->uv_type_ptr)) {
                \uv_ffi()->uv_dlclose($this->uv_type_ptr);
                \FFI::free($this->uv_symbol_ptr);
                $this->uv_symbol_ptr = null;
                $this->uv_symbol = null;

                parent::free();
            }
        }

        /**
         * @param string $definition
         * @return object|int definition
         */
        public function loadSymbol(string $definition)
        {
            $this->uv_symbol = \uv_ffi()->new('void_t');
            $this->uv_symbol_ptr = \ffi_ptr($this->uv_symbol);
            $status = \uv_ffi()->uv_dlsym($this->uv_type_ptr, $definition, $this->uv_symbol_ptr);
            return $status === 0 ? $this->uv_symbol_ptr : $status;
        }

        public function getSymbol(): CData
        {
            return $this->uv_symbol_ptr;
        }

        public function loadError(): string
        {
            return \uv_ffi()->uv_dlerror($this->uv_type_ptr);
        }

        /**
         * @param string $filename
         * @return static|int
         */
        public static function init(...$filename)
        {
            $lib = new static('uv_lib_t');
            $status = $lib->loadOpen(\reset($filename));
            return $status === 0 ? $lib : $status;
        }
    }
}
