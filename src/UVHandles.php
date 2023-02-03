<?php

declare(strict_types=1);

use FFI\CData;
use ZE\Zval;
use ZE\Resource;
use ZE\HashTable;
use ZE\PhpStream;

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
        protected ?CData $uv_loop = null;

        /** @var uv_Loop_t */
        protected ?CData $uv_loop_ptr = null;

        protected bool $uv_run_called = false;

        protected bool $uv_close_called = false;

        protected bool $is_default = false;

        public function __destruct()
        {
            if (\is_cdata($this->uv_loop_ptr)) {
                if ($this->uv_run_called && !$this->uv_close_called) {
                    /* in case we longjmp()'ed ... */
                    \uv_ffi()->uv_stop($this->uv_loop_ptr);
                    /* invalidate the stop ;-) */
                    \uv_ffi()->uv_run($this->uv_loop_ptr, \UV::RUN_DEFAULT);

                    \uv_ffi()->uv_walk($this->uv_loop_ptr, function (CData $handle, CData $args = null) {
                        $fd = $handle->u->fd;
                        if (Resource::is_valid($fd))
                            Resource::remove_fd($fd);
                        elseif (PhpStream::is_valid($fd))
                            PhpStream::remove_fd($fd);
                        if (\uv_ffi()->uv_is_active($handle))
                            \uv_ffi()->uv_close($handle, null);
                    }, null);
                    \uv_ffi()->uv_run($this->uv_loop_ptr, \UV::RUN_DEFAULT);

                    \uv_ffi()->uv_loop_close($this->uv_loop_ptr);
                }

                if (!$this->is_default && (!$this->uv_run_called || !$this->uv_close_called)) {
                    \ffi_set_free(true);
                    \ffi_free_if($this->uv_loop_ptr, $this->uv_loop);
                    \ffi_set_free(false);
                }

                $this->uv_loop_ptr = null;
                $this->uv_loop = null;

                $ext_uv = \ext_uv::get_module();
                if ($ext_uv->is_shutdown())
                    $ext_uv->request_shutdown(0, 0);
            }
        }

        protected function __construct(CData $default = null)
        {
            \uv_init();
            if ($default instanceof CData && \is_typeof($default, 'struct uv_loop_s*')) {
                $this->is_default = true;
                $this->uv_loop_ptr = $default;
            } else {
                $this->uv_loop = \uv_ffi()->new("struct uv_loop_s", false);
                $this->uv_loop_ptr = \ffi_ptr($this->uv_loop);
            }

            \ext_uv::get_module()->set_default($this);
        }

        public function __invoke(): ?CData
        {
            return $this->uv_loop_ptr;
        }

        public static function default(): self
        {
            $uv_default = \ext_uv::get_module()->get_default();
            if (!$uv_default instanceof \UVLoop) {
                $uv_default = new self(\uv_ffi()->uv_default_loop());
            }

            return $uv_default;
        }

        public function uv_ran(): void
        {
            $this->uv_run_called = true;
        }

        public function uv_closed(): void
        {
            $this->uv_close_called = true;
        }

        public static function init()
        {
            $loop = new self();
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
        protected ?Zval $fd = null;
        protected $fd_alt = null;
        protected ?\UVBuffer $buffer = null;

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

            $this->fd = null;
            $this->fd_alt = null;
            $this->buffer = null;
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
                function (CData $handle, int $suggested_size, CData $buf) {
                    $buf->base = \FFI::new('char[' . ($suggested_size + 1) . ']', false);
                    $buf->len = $suggested_size;
                },
                function (CData $stream, int $nRead, CData $data) use ($callback, $handle) {
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
            \uv_ffi()->uv_pipe_init(\uv_g(), $pipe(), 0);
            \uv_ffi()->uv_pipe_open($pipe(), $io);
            $handler = \uv_stream($pipe);
            \uv_ffi()->uv_read_start(
                $handler,
                function (CData $handle, int $suggested_size, CData $buf) {
                    $buf->base = \FFI::new('char[' . ($suggested_size + 1) . ']', false);
                    $buf->len = $suggested_size;
                },
                function (CData $stream, int $nRead, CData $data) use ($pipe) {
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

                        $writer = $this->__invoke(true);
                        \ffi_free_if($writer, $data->base, $stream, $handler);

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
                    $io = \get_fd_resource($pipe);
                }
            }

            $status = \uv_ffi()->uv_pipe_open($this->uv_struct_type, $io);
            if ($isPipeEmulated && $which === 1)
                $this->emulated($pipe[0]);

            return $status;
        }

        public function connect(string $path, callable $callback): void
        {
            $req = \UVConnect::init('struct uv_connect_s');
            \zval_add_ref($req);
            \uv_ffi()->uv_pipe_connect(
                $req(),
                $this->uv_struct_type,
                $path,
                function (CData $connect, int $status) use ($callback, $req) {
                    $callback($this, $status);
                    \zval_del_ref($req);
                }
            );
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

        public function get_winsize(&$width, &$height): int
        {
            $w = \zval_stack(0);
            $h = \zval_stack(1);

            $_width = \FFI::new('int');
            $_width_ptr = \FFI::addr($_width);
            $_height = \FFI::new('int');
            $_height_ptr = \FFI::addr($_height);

            $error = \uv_ffi()->uv_tty_get_winsize($this->uv_struct_type, $_width_ptr, $_height_ptr);

            $w->change_value($_width_ptr[0]);
            $h->change_value($_height_ptr[0]);

            \FFI::free($_width_ptr);
            \FFI::free($_height_ptr);

            return $error;
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

        public function bind(\UVSockAddr $addr, int $flags = 0)
        {
            $this->uv_sock = $addr;
            return \uv_ffi()->uv_tcp_bind($this->uv_struct_type, \uv_sockaddr($addr), $flags);
        }

        public function open($sock)
        {
            $fd = $sock;
            if (\is_resource($sock)) {
                $fd = \get_fd_resource($sock);
                if ($fd < 0) {
                    \ze_ffi()->zend_error(\E_WARNING, "file descriptor must be unsigned value or a valid resource");
                    return false;;
                }
            }

            $error = \uv_ffi()->uv_tcp_open($this->uv_struct_type, $fd);
            if ($error) {
                \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($error));
            }

            return $error;
        }

        public function connect(\UVSockAddr $addr, callable $callback)
        {
            $this->uv_sock = $addr;
            $req = \UVConnect::init('struct uv_connect_s');
            \zval_add_ref($req);
            return \uv_ffi()->uv_tcp_connect(
                $req(),
                $this->uv_struct_type,
                \uv_sockaddr($addr),
                function (CData $connect, int $status) use ($callback, $req) {
                    $callback($this, $status);
                    \zval_del_ref($req);
                }
            );
        }

        public function get_name(int $type)
        {
            $isIP6 = $this->uv_sock instanceof \UVSockAddrIPv6;
            $addr = $isIP6 ? \UVSockAddrIPv6::init() : \UVSockAddrIPv4::init();
            $addr_len = \c_int_type(
                'int',
                'uv',
                \FFi::sizeof($addr()[0]) * ($isIP6 ? 3 : 1)
            );

            switch ($type) {
                case 1:
                    \uv_ffi()->uv_tcp_getsockname($this->uv_struct_type, \uv_sockaddr($addr), $addr_len());
                    break;
                case 2:
                    \uv_ffi()->uv_tcp_getpeername($this->uv_struct_type, \uv_sockaddr($addr), $addr_len());
                    break;
                default:
                    \ze_ffi()->zend_error(\E_ERROR, "unexpected type");
                    break;
            };

            return \uv_address_to_array($addr);
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
        public static function init(?\UVLoop $loop, ...$arguments)
        {
            $udp = new static('struct _php_uv_s', 'udp');
            $status = \uv_ffi()->uv_udp_init($loop(), $udp());
            return ($status === 0) ? $udp : $status;
        }

        public function bind(\UVSockAddr $addr, int $flags = 0): int
        {
            $this->uv_sock = $addr;
            $r = \uv_ffi()->uv_udp_bind($this->uv_struct_type, \uv_sockaddr($addr), $flags);
            if ($r) {
                return \ze_ffi()->zend_error(\E_WARNING, "bind failed");
            }

            return $r;
        }

        public function stop()
        {
            if (!\uv_is_active($this)) {
                \ze_ffi()->zend_error(\E_NOTICE, "passed uv_resource has already stopped.");
                return false;
            }

            $r = \uv_ffi()->uv_udp_recv_stop($this->uv_struct_type);
            \zval_del_ref($this);

            return $r;
        }

        public function multicast(int $ttl)
        {
            if ($ttl > 255) {
                \ze_ffi()->zend_error(\E_NOTICE, "uv_udp_set_muticast_ttl: ttl parameter expected smaller than 255.");
                $ttl = 255;
            } elseif ($ttl < 1) {
                \ze_ffi()->zend_error(\E_NOTICE, "uv_udp_set_muticast_ttl: ttl parameter expected larger than 0.");
                $ttl = 1;
            }

            $r = \uv_ffi()->uv_udp_set_multicast_ttl($this->uv_struct_type, $ttl);
            if ($r) {
                \ze_ffi()->zend_error(\E_NOTICE, "uv_udp_set_muticast_ttl failed");
            }

            return $r;
        }

        public function open($sock)
        {
            $fd = $sock;
            if (\is_resource($sock)) {
                $fd = \get_fd_resource($sock);
                if ($fd < 0) {
                    \ze_ffi()->zend_error(\E_WARNING, "file descriptor must be unsigned value or a valid resource");
                    return false;;
                }
            }

            $error = \uv_ffi()->uv_udp_open($this->uv_struct_type, $fd);
            if ($error) {
                \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($error));
            }

            return $error;
        }

        /**
         * @param callable|uv_udp_recv_cb $callback
         * @return integer
         */
        public function recv(callable $callback): int
        {
            if (\uv_is_active($this)) {
                return \ze_ffi()->zend_error(\E_WARNING, "passed uv_object has already activated.");
            }

            $r = \uv_ffi()->uv_udp_recv_start(
                $this->uv_struct_type,
                function (CData $handle, int $suggested_size, CData $buf) {
                    $buf->base = \FFI::new('char[' . ($suggested_size + 1) . ']', false);
                    $buf->len = $suggested_size;
                },
                function (CData $udp, int $nRead, CData $data = null, CData $addr = null, int $flag) use ($callback) {
                    $callback($this, ($nRead > 0) ? \FFI::string($data->base) : $nRead, $flag);
                    if ($nRead > 0)
                        \FFI::free($data->base);

                    \zval_del_ref($callback);
                }
            );

            if ($r) {
                return \ze_ffi()->zend_error(\E_NOTICE, "read failed");
            }

            return $r;
        }

        /**
         * @param callable|uv_udp_send_cb $callback
         * @return integer
         */
        public function send(string $data, \UVSockAddr $addr, callable $callback): int
        {
            $address = \uv_address_to_array($addr);
            $ip = $address['address'];
            if ($addr instanceof \UVSockAddrIPv4)
                $this->uv_sock = $ip === '0.0.0.0' ? \uv_ip4_addr('127.0.0.1', $address['port']) : $addr;
            else
                $this->uv_sock = $ip === '::' ? \uv_ip6_addr('::1', $address['port']) : $addr;

            $send_req = \UVUdpSend::init('uv_udp_send_t');
            $buf = \uv_buf_init($data);
            \zval_add_ref($send_req);
            return \uv_ffi()->uv_udp_send(
                $send_req(),
                $this->uv_struct_type,
                $buf(),
                1,
                \uv_sockaddr($this->uv_sock),
                function (CData $req, int $status) use ($callback, $send_req, $buf) {
                    $callback($this, $status);
                    if (!\uv_is_closing($this)) { /* send_cb is invoked *before* the handle is marked as inactive - uv_close() will thus *not* increment the refcount and we must then not delete the refcount here */
                        \zval_del_ref($this);
                    }

                    $buf->free();
                    \zval_del_ref($send_req);
                    \zval_del_ref($callback);
                }
            );
        }

        public function get_name(int $type)
        {
            $addr = \UVSockaddr::init();
            $addr_len = \c_int_type(
                'int',
                'uv',
                \FFi::sizeof($addr()[0]) * ($this->uv_sock instanceof \UVSockAddrIPv6 ? 3 : 1)
            );

            switch ($type) {
                case 3:
                    \uv_ffi()->uv_udp_getsockname($this->uv_struct_type, $addr(), $addr_len());
                    break;
                default:
                    \ze_ffi()->zend_error(\E_ERROR, "unexpected type");
                    break;
            };

            return \uv_address_to_array($addr);
        }
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
        /** @var resource */
        protected $fd = null;

        public function fd($fd)
        {
            $this->fd = $fd;
        }

        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $poll = new static('struct _php_uv_s', 'poll');
            $resource = \reset($arguments);
            \stream_set_blocking($resource, false);
            $poll->fd($resource);
            $fd = \get_socket_fd(\zval_constructor($resource));
            if (\IS_WINDOWS)
                $status = \uv_ffi()->uv_poll_init_socket($loop(), $poll(), $fd);
            else
                $status = \uv_ffi()->uv_poll_init($loop(), $poll(), $fd);

            return $status === 0 ? $poll : $status;
        }

        public function start(int $events, callable $callback): int
        {
            if (!\uv_is_active($this)) {
                \zval_add_ref($this);
            }

            $uv_poll_cb = function (CData $handle, int $status, int $events) use ($callback) {
                if ($status == 0)
                    \zval_add_ref($this);

                $callback($this, $status, $events, $this->fd);
            };

            $error = \uv_ffi()->uv_poll_start($this->uv_struct_type, $events, $uv_poll_cb);
            if ($error) {
                \ze_ffi()->zend_error(\E_ERROR, "uv_poll_start failed");
            }

            return $error;
        }
    }
}

if (!\class_exists('UVFsPoll')) {
    /**
     * FS Poll handles allow the user to monitor a given path for changes.
     * Unlike `uv_fs_event_t`, fs poll handles use stat to detect when a file has changed so they can work on
     * file systems where fs event handles can’t.
     *
     * @return uv_fs_poll_t **pointer** by invoking `$UVFsPoll()`
     */
    final class UVFsPoll extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $fs_poll = new static('struct _php_uv_s', 'fs_poll');
            $status  = \uv_ffi()->uv_fs_poll_init($loop(), $fs_poll());

            return $status === 0 ? $fs_poll : $status;
        }

        public function start(callable $callback, string $path, int $interval): int
        {
            $uv_fs_poll_cb = function (CData $handle, int $status, CData $prev, CData $curr) use ($callback) {
                $callback($this, $status, \uv_stat_to_zval($prev), \uv_stat_to_zval($curr));
            };

            \zval_add_ref($this);
            $error = \uv_ffi()->uv_fs_poll_start($this->uv_struct_type, $uv_fs_poll_cb, $path, $interval);
            if ($error) {
                \zval_del_ref($this);
                \ze_ffi()->zend_error(\E_ERROR, "uv_fs_poll_start failed");
            }

            return $error;
        }

        public function stop()
        {
            if (!\uv_is_active($this)) {
                return;
            }

            $status = \uv_ffi()->uv_fs_poll_stop($this->uv_struct_type);
            \zval_del_ref($this);

            return $status;
        }

        public function getpath()
        {
            $buffer = \ffi_characters(\INET6_ADDRSTRLEN);
            $size = c_int_type('size_t', 'uv', \INET6_ADDRSTRLEN);
            $status = \uv_ffi()->uv_fs_poll_getpath($this->uv_struct_type, $buffer, $size());
            if ($status === \UV::ENOBUFS) {
                $buffer = \ffi_characters($size->value());
                $status = \uv_ffi()->uv_fs_poll_getpath($this->uv_struct_type, $buffer, $size());
            }

            return $status === 0 ? \ffi_string($buffer) : $status;
        }
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

        public function start(int $timeout, int $repeat, callable $callback = null): int
        {
            if ($timeout < 0)
                return \ze_ffi()->zend_error(\E_WARNING, "timeout value have to be larger than 0. given %lld", $timeout);

            if ($repeat < 0)
                return \ze_ffi()->zend_error(\E_WARNING, "repeat value have to be larger than 0. given %lld", $repeat);

            if (\uv_is_active($this))
                return \ze_ffi()->zend_error(\E_NOTICE, "Passed uv timer resource has been started. You don't have to call this method");

            \zval_add_ref($this);
            return \uv_ffi()->uv_timer_start(
                $this->uv_struct_type,
                \is_null($callback) ? function () {
                } :  function (CData $handle) use ($callback) {
                    $callback($this);
                },
                $timeout,
                $repeat
            );
        }

        public function stop(): int
        {
            if (!\uv_is_active($this))
                return \ze_ffi()->zend_error(\E_NOTICE, "Passed uv timer resource has been stopped. You don't have to call this method");

            $r = \uv_ffi()->uv_timer_stop($this->uv_struct_type);
            \zval_del_ref($this);

            return $r;
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
        public static function init(?UVLoop $loop, ...$arguments)
        {
            if (\is_null($loop))
                $loop = \uv_default_loop();

            $signal = new self('struct _php_uv_s', 'signal');
            $status = \uv_ffi()->uv_signal_init($loop(), $signal());
            return $status === 0 ? $signal : $status;
        }

        public function start(callable $callback, int $signal): int
        {
            if (\uv_is_active($this))
                return \ze_ffi()->zend_error(\E_NOTICE, "passed uv signal resource has been started. you don't have to call this method");

            return \uv_ffi()->uv_signal_start($this->uv_struct_type, function (CData $handle, int $signal) use ($callback) {
                \zval_add_ref($this);
                $callback($this, $signal);
                \zval_del_ref($this);
                unset($signal);
            }, $signal);
        }

        public function stop(): int
        {
            if (!\uv_is_active($this))
                return \ze_ffi()->zend_error(\E_NOTICE, "passed uv signal resource has been stopped. you don't have to call this method");

            $r = \uv_ffi()->uv_signal_stop($this->uv_struct_type);
            \zval_del_ref($this);

            return $r;
        }
    }
}

if (!\class_exists('UVStdio')) {
    /**
     * Stdio is an I/O wrapper for `uv_stdio_container_t` to be passed to uv_spawn().
     */
    final class UVStdio
    {
        protected ?\UVStream $stream = null;
        protected ?int $flags = 0;
        protected ?int $fd = -1;

        public function __destruct()
        {
            $this->stream = null;
            $this->flags = null;
            $this->fd = null;
        }

        public function stdio(string $key = null)
        {
            $stdio = ['stream' => $this->stream, 'flags' => $this->flags, 'fd' => $this->fd];

            return isset($stdio[$key]) ? $stdio[$key] : $stdio;
        }

        public function create($fd_handle, int $flags = 0)
        {
            $handle = \zval_stack(0);
            $fd = -1;
            if (\is_null($handle) || $handle->macro(\ZE::TYPE_P) == \ZE::IS_NULL) {
                $flags = \UV::IGNORE;
            } elseif ($handle->macro(\ZE::TYPE_P) == \ZE::IS_LONG) {
                $fd = $handle->macro(\ZE::LVAL_P);
                if ($flags & (\UV::CREATE_PIPE | \UV::INHERIT_STREAM)) {
                    \ze_ffi()->zend_error(\E_WARNING, "flags must not be UV::CREATE_PIPE or UV::INHERIT_STREAM for resources");
                    return false;
                }

                $flags |= \UV::INHERIT_FD;
            } elseif ($handle->macro(\ZE::TYPE_P) == \ZE::IS_RESOURCE) {
                $fd_resource = \fd_type('php_socket_t');
                $fd = $fd_resource();
                $stream = \ze_cast('php_stream *', \ze_ffi()->zend_fetch_resource_ex($handle(), NULL, \ze_ffi()->php_file_le_stream()));
                if (\is_cdata($stream)) {
                    if (\ze_ffi()->_php_stream_cast($stream, Resource::PHP_STREAM_AS_FD | Resource::PHP_STREAM_CAST_INTERNAL, \ffi_void($fd), 1) != \ZE::SUCCESS || $fd < 0) {
                        \ze_ffi()->zend_error(\E_WARNING, "passed resource without file descriptor");
                        return false;
                    }
                } else {
                    \ze_ffi()->zend_error(\E_WARNING, "passed unexpected resource, expected file or socket");
                    return false;
                }

                if ($flags & (\UV::CREATE_PIPE | \UV::INHERIT_STREAM)) {
                    \ze_ffi()->zend_error(\E_WARNING, "flags must not be UV::CREATE_PIPE or UV::INHERIT_STREAM for resources");
                    return false;
                }

                $flags |= \UV::INHERIT_FD;
                $fd = $fd[0];
                $fd_resource->add_pair($handle, $fd, $handle()->value->res->handle);
            } elseif ($handle->macro(\ZE::TYPE_P) == \ZE::IS_OBJECT && $fd_handle instanceof UV) {
                if ($flags & \UV::INHERIT_FD) {
                    \ze_ffi()->zend_error(\E_WARNING, "flags must not be UV::INHERIT_FD for UV handles");
                    return false;
                }

                if (($flags & (\UV::CREATE_PIPE | \UV::INHERIT_STREAM)) == (\UV::CREATE_PIPE | \UV::INHERIT_STREAM) || !($flags & (\UV::CREATE_PIPE | \UV::INHERIT_STREAM))) {
                    \ze_ffi()->zend_error(\E_WARNING, "flags must be exactly one of UV::INHERIT_STREAM or UV::CREATE_PIPE for UV handles");
                    return false;
                }
            } else {
                \ze_ffi()->zend_error(\E_WARNING, "passed unexpected value, expected instance of UV, file resource or socket object");
                return false;
            }

            $this->flags = $flags;
            if ($handle->macro(\ZE::TYPE_P) == \ZE::IS_OBJECT) {
                $this->stream = $fd_handle;
            } else {
                $this->fd = $fd;
            }

            return $this;
        }
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
        protected array $streams = [];

        public function kill(int $signal)
        {
            return \uv_ffi()->uv_process_kill($this->uv_struct_type, $signal);
        }

        public function get_pid()
        {
            return \uv_ffi()->uv_process_get_pid($this->uv_struct_type);
        }

        /**
         * @param UVLoop $loop
         * @param string $command
         * @param null|array $args
         * @param null|UVStdio[] $stdio
         * @param null|string $cwd
         * @param array $env
         * @param null|callable|uv_exit_cb $callback
         * @param null|int $flags
         * @param null|array $options
         *
         * @return int|UVProcess
         */
        public function spawn(
            \UVLoop $loop,
            string $command,
            array $args,
            array $stdio,
            string $cwd = null,
            array $env = array(),
            callable $callback = null,
            int $flags = \UV::PROCESS_WINDOWS_HIDE,
            array $uid_gid = []
        ) {
            $h = \zval_stack(2);

            $process_options = \c_struct_type('uv_process_options_s', 'uv');
            $process_options->memset(0, $process_options->sizeof());

            /* process stdio */
            $streams = [];
            $stdio_count = \count($stdio) > 0 ? \count($stdio) : 1;
            $container = \uv_ffi()->new('uv_stdio_container_t[' . $stdio_count . ']', false);
            foreach ($stdio as $key => $value) {
                if (!$value instanceof \UVStdio) {
                    \ze_ffi()->zend_error(\E_ERROR, "must be instance of UVStdio");
                }

                $container[$key]->flags = $value->stdio('flags');
                if ($container[$key]->flags & \UV::INHERIT_FD) {
                    $container[$key]->data->fd = $value->stdio('fd');
                } elseif ($container[$key]->flags & (\UV::CREATE_PIPE | \UV::INHERIT_STREAM)) {
                    $stream = $value->stdio('stream');
                    $streams[] = $stream;
                    $container[$key]->data->stream = \uv_stream($stream);
                } else {
                    \ze_ffi()->zend_error(\E_WARNING, "passes unexpected stdio flags");
                    return false;
                }

                $value->__destruct();
            }
            $this->streams = $streams;

            /* process args */
            $n = 0;
            $hash_len = $h->macro(\ZE::ARRVAL_P)->nNumOfElements;
            $commands = \ffi_char($command);
            $command_args = \FFI::new('char*[' . ($hash_len + 2) . ']', false);
            $command_args[$n] = $commands;

            $n++;
            foreach ($args as $value) {
                $command_args[$n] = \ffi_char($value);
                $n++;
            }
            $command_args[$n] = NULL;

            /* process env */
            $i = 0;
            $zenv = \FFI::new('char*[' . (\count($env) + 1) . ']', false);
            foreach ($env as $key => $value) {
                $tmp_env_entry = \sprintf('%s=%s', $key, $value);
                $zenv[$i] = \ffi_char($tmp_env_entry);
                $i++;
            }
            $zenv[$i] = NULL;
            $zenv_size = $i;

            $uid = \IS_LINUX && \array_key_exists('uid', $uid_gid) ? $uid_gid['uid'] : null;
            $gid = \IS_LINUX && \array_key_exists('gid', $uid_gid) ? $uid_gid['gid'] : null;

            $options = $process_options();
            $options->file    = $commands;
            $options->stdio   = \uv_cast('uv_stdio_container_t*', $container);
            $options->exit_cb =  function (CData $process, int $exit_status, int $term_signal) use ($callback, $process_options) {
                if (!\is_null($callback)) {
                    $callback($this, $exit_status, $term_signal);
                    \zval_del_ref($callback);
                }

                unset($exit_status);
                unset($term_signal);
                \zval_del_ref($process_options);
                \zval_del_ref($this);
            };

            $options->stdio_count = $stdio_count;
            $options->env   = \FFI::cast('char**', $zenv);
            $options->args  = \FFI::cast('char**', $command_args);

            if (\is_null($cwd)) {
                $cwd = \uv_cwd();
            }

            $options->cwd   = \ffi_char($cwd);
            $options->flags = $flags;
            $options->uid   = $uid;
            $options->gid   = $gid;

            $ret = \uv_ffi()->uv_spawn($loop(), $this->uv_struct_type, $options);
            if ($ret === 0) {
                \zval_add_ref($this);
                \zval_add_ref($process_options);
            } else {
                \zval_del_ref($this);
                \zval_del_ref($process_options);
            }

            if (\is_cdata($zenv)) {
                $p = 0;
                while ($zenv_size > $p) {
                    \FFI::free($zenv[$p]);
                    $p++;
                }

                \FFI::free($zenv);
            }

            if (\is_cdata($command_args)) {
                \FFI::free($command_args);
            }

            if (\is_cdata($container)) {
                \FFI::free($container);
            }

            return $ret === 0 ? $this : $ret;
        }
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

if (!\class_exists('UVSockAddr')) {
    /**
     * Address and port base structure
     * @return sockaddr by invoking `$UVSockAddr()`
     */
    class UVSockAddr extends \UVTypes
    {
        public function family(): int
        {
            return $this->__invoke()->sa_family;
        }

        public static function init(...$arguments)
        {
            return new static('struct sockaddr');
        }
    }
}

if (!\class_exists('UVSockAddrIPv4')) {
    /**
     * IPv4 Address and port structure
     * @deprecated 1.0
     */
    final class UVSockAddrIPv4 extends \UVSockAddr
    {
        public function family(): int
        {
            return $this->__invoke()->sin_family;
        }

        public static function init(...$arguments)
        {
            return new static('struct sockaddr_in');
        }
    }
}

if (!\class_exists('UVSockAddrIPv6')) {
    /**
     * IPv6 Address and port structure
     * @deprecated 1.0
     */
    final class UVSockAddrIPv6 extends \UVSockAddr
    {
        public function family(): int
        {
            return $this->__invoke()->sin6_family;
        }

        public static function init(...$arguments)
        {
            return new static('struct sockaddr_in6');
        }
    }
}

if (!\class_exists('UVSockaddrStorage')) {
    final class UVSockaddrStorage extends \UVSockAddr
    {
        public function family(): int
        {
            return $this->__invoke()->ss_family;
        }

        public static function init(...$arguments)
        {
            return new static('struct sockaddr_storage');
        }
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
    class UVLock extends \UVThreader
    {
        public function rdlock()
        {
            if ($this->struct_base->locked == 0x01) {
                \ze_ffi()->zend_error(\E_WARNING, "Cannot acquire a read lock while holding a write lock");
                return false;
            }

            \uv_ffi()->uv_rwlock_rdlock($this->struct_ptr);
            if (!$this->struct_base->locked++) {
                $this->struct_base->locked = 0x02;
            }

            $this->locked = $this->struct_base->locked;
        }

        public function tryrdlock()
        {
            if ($this->struct_base->locked == 0x01) {
                \ze_ffi()->zend_error(\E_WARNING, "Cannot acquire a read lock while holding a write lock");
                return false;
            }

            $error = \uv_ffi()->uv_rwlock_tryrdlock($this->struct_ptr);
            if ($error == 0) {
                if (!$this->struct_base->locked++) {
                    $this->struct_base->locked = 0x02;
                }

                $this->locked = $this->struct_base->locked;
                return true;
            } else {
                return false;
            }
        }

        public function rdunlock()
        {
            if ($this->struct_base->locked > 0x01) {
                \uv_ffi()->uv_rwlock_rdunlock($this->struct_ptr);
                if (--$this->struct_base->locked == 0x01) {
                    $this->struct_base->locked = 0x00;
                }

                $this->locked = $this->struct_base->locked;
            }
        }

        public function trywrlock()
        {
            if ($this->struct_base->locked) {
                \ze_ffi()->zend_error(E_WARNING, "Cannot acquire a write lock when already holding a lock");
                return false;
            }

            $error = \uv_ffi()->uv_rwlock_trywrlock($this->struct_ptr);
            if ($error == 0) {
                $this->struct_base->locked = 0x01;
                $this->locked = $this->struct_base->locked;
                return true;
            } else {
                return false;
            }
        }

        public function wrunlock()
        {
            if ($this->struct_base->locked == 0x01) {
                \uv_ffi()->uv_rwlock_wrunlock($this->struct_ptr);
                $this->struct_base->locked = 0x00;
                $this->locked = $this->struct_base->locked;
            }
        }
    }
}

if (!\class_exists('UVMutex')) {
    /**
     * @return uv_mutex_t **pointer** by invoking `$UVMutex()`
     */
    final class UVMutex extends \UVLock
    {
        public function trylock()
        {
            $error = \uv_ffi()->uv_mutex_trylock($this->struct_ptr);
            if ($error == 0) {
                $this->struct_base->locked = 0x01;
                $this->locked = $this->struct_base->locked;
                return true;
            } else {
                return false;
            }
        }

        public function unlock()
        {
            if ($this->struct_base->locked == 0x01) {
                \uv_ffi()->uv_mutex_unlock($this->struct_ptr);
                $this->struct_base->locked = 0x00;
                $this->locked = $this->struct_base->locked;
            }
        }
    }
}

if (!\class_exists('UVSemaphore')) {
    /**
     * @return uv_sem_t **pointer** by invoking `$UVSemaphore()`
     */
    final class UVSemaphore extends \UVLock
    {
    }
}

if (!\class_exists('UVThread')) {
    /**
     * @return uv_thread_t **pointer** by invoking `$UVThread()`
     */
    final class UVThread extends \UVTypes
    {
        protected function __construct(string $typedef = null, $value = null)
        {
            $this->uv_type = \uv_ffi()->new('uv_thread_t');
            $this->uv_type_ptr = \ffi_ptr($this->uv_type);
            if ($typedef === 'self' && !\is_null($value)) {
                $this->uv_type_ptr[0] = $value;
            }
        }

        public function value()
        {
            return $this->uv_type_ptr[0];
        }

        /** @return static */
        public static function init(...$arguments)
        {
            $type = \array_shift($arguments);
            return new static($type, \reset($arguments));
        }
    }
}

if (!\class_exists('UVKey')) {
    /**
     * @return uv_key_t **pointer** by invoking `$UVKey()`
     */
    final class UVKey extends \UVTypes
    {
        public function __destruct()
        {
            if (\is_cdata($this->uv_type_ptr))
                \uv_ffi()->uv_key_delete($this->uv_type_ptr);

            parent::__destruct();
        }

        /** @return static */
        public static function init(...$arguments)
        {
            return new static('uv_key_t');
        }
    }
}


if (!\class_exists('UVWork')) {
    /**
     * @return uv_work_t **pointer** by invoking `$UVWork()`
     */
    final class UVWork extends \UVRequest
    {
        public static function init(...$arguments)
        {
            if (\PHP_ZTS) {
                $loop = \array_shift($arguments);
                $work_cb = \array_shift($arguments);
                $after_cb = \array_shift($arguments);
                $work = new static('struct uv_work_s');
                \zval_add_ref($work);
                $r = \uv_ffi()->uv_queue_work(
                    $loop(),
                    $work(),
                    function (CData $req) use ($work_cb) {
                        //   $tsrm_ls = \ze_ffi()->ts_resource_ex(0, null);
                        //$tsrm_ls = \ze_ffi()->tsrm_new_interpreter_context();
                        //$old = \ze_ffi()->tsrm_set_interpreter_context($tsrm_ls);

                        //     \zend_pg('expose_php', 0);
                        //  \zend_pg('auto_globals_jit', 0);

                        // \ze_ffi()->php_request_startup();
                        //   \zend_eg('current_execute_data', null);
                        // \zend_eg('current_module', $phpext_uv_ptr);

                        // require_once 'vendor/symplely/zend-ffi/preload.php';
                        //   $work_cb();

                        //\ze_ffi()->php_request_shutdown(NULL);
                        //  \ze_ffi()->ts_free_thread();
                        // \ze_ffi()->tsrm_set_interpreter_context($old);
                        // \ze_ffi()->tsrm_free_interpreter_context($tsrm_ls);
                    },
                    function (CData $req, int $status) use ($after_cb, $work) {
                        //   $after_cb($status);
                        //   unset($status);
                        //   \FFI::free($req);
                        // \zval_del_ref($after_cb);
                        //   \zval_del_ref($work);
                    }
                );

                if ($r) {
                    return \ze_ffi()->zend_error(\E_ERROR, "uv_queue_work failed");
                }
            } else {
                return \ze_ffi()->zend_error(\E_ERROR, "this PHP doesn't support this uv_queue_work. please rebuild with --enable-maintainer-zts");
            }

            return $r === 0 ? $work : $r;
        }
    }
}

if (!\class_exists('UVGetNameinfo')) {
    final class UVGetNameinfo extends \UVRequest
    {
        /**
         * @param \UVLoop $loop
         * @param \UVSockAddr $addr
         * @param integer $flags
         * @param callable|uv_getnameinfo_cb $callback callable expect (int $status|string $hostname, string $service)
         * @return int|array['address'=>'x.x.x.x','port'=>'xx']
         */
        public static function getnameinfo(\UVLoop $loop, callable $callback = null, \UVSockAddr $addr, int $flags)
        {
            $nameInfo_req = new static('struct uv_getnameinfo_s');
            $getnameinfo_cb = \is_null($callback) ? null : function (CData $req, int $status, string $hostname, string $service) use ($callback, $nameInfo_req) {
                $callback(($status < 0 ? $status : $hostname), $service);
                unset($hostname);
                unset($service);
                \zval_del_ref($callback);
                $nameInfo_req->free();
            };

            $status = \uv_ffi()->uv_getnameinfo($loop(), $nameInfo_req(), $getnameinfo_cb, $addr(), $flags);
            if (\is_null($callback)) {
                $status = ['address' => \FFI::string($nameInfo_req()->host), 'port' => \FFI::string($nameInfo_req()->service)];
                \zval_del_ref($nameInfo_req);
            }

            return $status;
        }
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
                if (\in_array('ai_family', $hints, true)) {
                    $hint->ai_family = $hints['ai_family'];
                }

                if (\in_array('ai_socktype', $hints, true)) {
                    $hint->ai_socktype =  $hints['ai_socktype'];
                }

                if (\in_array('ai_protocol', $hints, true)) {
                    $hint->ai_socktype = $hints['ai_protocol'];
                }

                if (\in_array('ai_flags', $hints, true)) {
                    $hint->ai_flags =  $hints['ai_flags'];
                }
            }

            $addrinfo_req = new static('struct uv_getaddrinfo_s');

            return \uv_ffi()->uv_getaddrinfo(
                $loop(),
                $addrinfo_req(),
                function (CData $handle, int $status, $res) use ($callback, $addrinfo_req, $addrinfo) {
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
        /**
         * @param Zval $set
         * @return Zval|resource|null|void
         */
        public function fd($set = null)
        {
            if (\is_null($set))
                return $this->fd;

            $this->fd = $set instanceof Zval ? $set : null;
        }

        public function fd_alt($set = null)
        {
            if (\is_null($set))
                return $this->fd_alt;

            $this->fd_alt = $set instanceof Zval || \is_resource($set) ? $set : null;
        }

        /**
         * @param UVBuffer $read
         * @return UVBuffer|null|void
         */
        public function buffer($set = null)
        {
            if (\is_null($set))
                return $this->buffer;

            if ($set === 'free') {
                $buffer = $this->buffer;
                $this->buffer = null;
                if (\is_object($buffer))
                    \zval_del_ref($buffer);
            }

            $this->buffer = $set instanceof UVBuffer ? $set : null;
        }

        public static function init(...$arguments)
        {
            $result = -4058;
            $loop = \array_shift($arguments);
            $fs_type = \array_shift($arguments);
            $fdOrString = \array_shift($arguments);
            $callback = \array_pop($arguments);
            $uv_fSystem = new static('struct uv_fs_s');
            $uv_fs_cb = \is_null($callback) ? null : function (CData $req) use ($callback, $uv_fSystem) {
                $params = [];
                $params[0] = $uv_fSystem->fd_alt();
                $result = \uv_ffi()->uv_fs_get_result($req);
                $fs_ptr = \uv_ffi()->uv_fs_get_ptr($req);
                $fs_type = \uv_ffi()->uv_fs_get_type($req);
                switch ($fs_type) {
                    case \UV::FS_CLOSE:
                        Resource::remove_fd((int)$params[0]);
                    case \UV::FS_SYMLINK:
                    case \UV::FS_LINK:
                    case \UV::FS_CHMOD:
                    case \UV::FS_RENAME:
                    case \UV::FS_UNLINK:
                    case \UV::FS_RMDIR:
                    case \UV::FS_MKDIR:
                    case \UV::FS_CHOWN:
                    case \UV::FS_UTIME:
                    case \UV::FS_FUTIME:
                        $params[0] = $result;
                        break;
                    case \UV::FS_FCHMOD:
                    case \UV::FS_FCHOWN:
                    case \UV::FS_FTRUNCATE:
                    case \UV::FS_FDATASYNC:
                    case \UV::FS_FSYNC:
                        $params[1] = $result;
                        break;
                    case \UV::FS_OPEN:
                        if ($result < 0)
                            $params[0] = $result;
                        else
                            $params[0] = \create_uv_fs_resource($req, $result, $uv_fSystem);
                        break;
                    case \UV::FS_SCANDIR:
                        /* req->ptr may be NULL here, but uv_fs_scandir_next() knows to handle it */
                        if ($result < 0) {
                            $params[0] = $result;
                        } else {
                            $zval = \zval_array(\ze_ffi()->_zend_new_array(0));
                            $dent = \UVDirent::init('struct uv_dirent_s');
                            while (\UV::EOF != \uv_ffi()->uv_fs_scandir_next($req, $dent())) {
                                \ze_ffi()->add_next_index_string($zval(), $dent()->name);
                            }
                            $params[0] = \zval_native($zval);
                        }
                        break;
                    case \UV::FS_LSTAT:
                    case \UV::FS_STAT:
                        if (!\is_null($fs_ptr))
                            $params[0] = \uv_stat_to_zval((\uv_cast('uv_stat_t *', $fs_ptr)));
                        else
                            $params[0] = $result;
                        break;
                    case \UV::FS_FSTAT:
                        if (!\is_null($fs_ptr))
                            $params[1] = \uv_stat_to_zval((\uv_cast('uv_stat_t *', $fs_ptr)));
                        else
                            $params[1] = $result;
                        break;
                    case \UV::FS_READLINK:
                        if ($result == 0)
                            $params[0] = \ffi_string($fs_ptr);
                        else
                            $params[0] = $result;
                        break;
                    case \UV::FS_READ:
                        $buffer = $uv_fSystem->buffer();
                        if ($result >= 0)
                            $params[1] = $buffer->getString($result);
                        else
                            $params[1] = $result;
                        $uv_fSystem->buffer('free');
                        break;
                    case \UV::FS_SENDFILE:
                        $params[1] = $result;
                        break;
                    case \UV::FS_WRITE:
                        $params[1] = $result;
                        $uv_fSystem->buffer('free');
                        break;
                    case \UV::FS_UNKNOWN:
                    case \UV::FS_CUSTOM:
                    default:
                        \ze_ffi()->zend_error(\E_ERROR, "type; %d does not support yet.", $fs_type);
                        break;
                }

                $callback(...$params);

                if ($fs_type !== \UV::FS_OPEN) {
                    $uv_fSystem->free();
                    \zval_del_ref($uv_fSystem);
                }

                \zval_del_ref($callback);
                unset($params);
            };

            \zval_add_ref($uv_fSystem);
            if (\is_string($fdOrString)) {
                switch ($fs_type) {
                    case \UV::FS_OPEN:
                        $flags = \array_shift($arguments);
                        $mode = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_open($loop(), $uv_fSystem(), $fdOrString, $flags, $mode, $uv_fs_cb);
                        if (\is_null($callback))
                            return \create_uv_fs_resource($uv_fSystem(), $result, $uv_fSystem);
                        break;
                    case \UV::FS_UNLINK:
                        $result = \uv_ffi()->uv_fs_unlink($loop(), $uv_fSystem(), $fdOrString, $uv_fs_cb);
                        break;
                    case \UV::FS_MKDIR:
                        $result = \uv_ffi()->uv_fs_mkdir($loop(), $uv_fSystem(), $fdOrString, \array_shift($arguments), $uv_fs_cb);
                        break;
                    case \UV::FS_RENAME:
                        $result = \uv_ffi()->uv_fs_rename($loop(), $uv_fSystem(), $fdOrString, \array_shift($arguments), $uv_fs_cb);
                        break;
                    case \UV::FS_CHMOD:
                        $result = \uv_ffi()->uv_fs_chmod($loop(), $uv_fSystem(), $fdOrString, \array_shift($arguments), $uv_fs_cb);
                        break;
                    case \UV::FS_UTIME:
                        $atime = \array_shift($arguments);
                        $mtime = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_utime($loop(), $uv_fSystem(), $fdOrString, $atime, $mtime, $uv_fs_cb);
                        break;
                    case \UV::FS_CHOWN:
                        $uid = \array_shift($arguments);
                        $gid = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_chown($loop(), $uv_fSystem(), $fdOrString, $uid, $gid, $uv_fs_cb);
                        break;
                    case \UV::FS_LINK:
                        $result = \uv_ffi()->uv_fs_link($loop(), $uv_fSystem(), $fdOrString, \array_shift($arguments), $uv_fs_cb);
                        break;
                    case \UV::FS_SYMLINK:
                        $new_path = \array_shift($arguments);
                        $flags = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_symlink($loop(), $uv_fSystem(), $fdOrString, $new_path, $flags, $uv_fs_cb);
                        break;
                    case \UV::FS_RMDIR:
                        $result = \uv_ffi()->uv_fs_rmdir($loop(), $uv_fSystem(), $fdOrString, $uv_fs_cb);
                        break;
                    case \UV::FS_FSTAT:
                        $result = \uv_ffi()->uv_fs_lstat($loop(), $uv_fSystem(), $fdOrString, $uv_fs_cb);
                        if (\is_null($callback))
                            $result = \uv_stat_to_zval(\uv_fs_get_statbuf($uv_fSystem));
                        break;
                    case \UV::FS_STAT:
                        $result = \uv_ffi()->uv_fs_stat($loop(), $uv_fSystem(), $fdOrString, $uv_fs_cb);
                        if (\is_null($callback))
                            $result = \uv_stat_to_zval(\uv_fs_get_statbuf($uv_fSystem));
                        break;
                    case \UV::FS_SCANDIR:
                        $result = \uv_ffi()->uv_fs_scandir($loop(), $uv_fSystem(), $fdOrString, \array_shift($arguments), $uv_fs_cb);
                        if (\is_null($callback)) {
                            $zval = \zval_array(\ze_ffi()->_zend_new_array(0));
                            $dent = \UVDirent::init('struct uv_dirent_s');
                            while (\UV::EOF != \uv_ffi()->uv_fs_scandir_next($uv_fSystem(), $dent())) {
                                \ze_ffi()->add_next_index_string($zval(), $dent()->name);
                            }

                            $result = \zval_native($zval);
                        }
                        break;
                    case \UV::FS_READLINK:
                        $result = \uv_ffi()->uv_fs_readlink($loop(), $uv_fSystem(), $fdOrString, $uv_fs_cb);
                        if (\is_null($callback))
                            $result = \ffi_string(\uv_ffi()->uv_fs_get_ptr($uv_fSystem()));
                        break;
                    case \UV::FS_UNKNOWN:
                    case \UV::FS_CUSTOM:
                    default:
                        \ze_ffi()->zend_error(\E_ERROR, "type; %d does not support yet.", $fs_type);
                        break;
                }
            } elseif (\is_resource($fdOrString)) {
                [$zval, $fd] = \zval_to_fd_pair($fdOrString);
                $uv_fSystem->fd_alt($fdOrString);
                switch ($fs_type) {
                    case \UV::FS_FSTAT:
                        $result = \uv_ffi()->uv_fs_fstat($loop(), $uv_fSystem(), $fd, $uv_fs_cb);
                        if (\is_null($callback))
                            $result = \uv_stat_to_zval(\uv_fs_get_statbuf($uv_fSystem));
                        break;
                    case \UV::FS_SENDFILE:
                        $in = \array_shift($arguments);
                        [$zval_alt, $in_fd] = \zval_to_fd_pair($in);
                        $uv_fSystem->fd($zval_alt);
                        $offset = \array_shift($arguments);
                        $length = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_sendfile(
                            $loop(),
                            $uv_fSystem(),
                            $fd,
                            $in_fd,
                            $offset,
                            $length,
                            $uv_fs_cb
                        );
                        break;
                    case \UV::FS_CLOSE:
                        $result = \uv_ffi()->uv_fs_close($loop(), $uv_fSystem(), $fd, $uv_fs_cb);
                        if (\is_null($callback))
                            Resource::remove_fd($fd);
                        break;
                    case \UV::FS_FSYNC:
                        $result = \uv_ffi()->uv_fs_fsync($loop(), $uv_fSystem(), $fd, $uv_fs_cb);
                        break;
                    case \UV::FS_FDATASYNC:
                        $result = \uv_ffi()->uv_fs_fdatasync($loop(), $uv_fSystem(), $fd, $uv_fs_cb);
                        break;
                    case \UV::FS_FTRUNCATE:
                        $result = \uv_ffi()->uv_fs_ftruncate($loop(), $uv_fSystem(), $fd, \array_shift($arguments), $uv_fs_cb);
                        break;
                    case \UV::FS_FCHMOD:
                        $result = \uv_ffi()->uv_fs_fchmod($loop(), $uv_fSystem(), $fd, \array_shift($arguments), $uv_fs_cb);
                        break;
                    case \UV::FS_FUTIME:
                        $atime = \array_shift($arguments);
                        $mtime = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_futime($loop(), $uv_fSystem(), $fd, $atime, $mtime, $uv_fs_cb);
                        break;
                    case \UV::FS_FCHOWN:
                        $uid = \array_shift($arguments);
                        $gid = \array_shift($arguments);
                        $result = \uv_ffi()->uv_fs_fchown($loop(), $uv_fSystem(), $fd, $uid, $gid, $uv_fs_cb);
                        break;
                    case \UV::FS_READ;
                        $offset = \array_shift($arguments);
                        $length = \array_shift($arguments);
                        if ($length <= 0)
                            $length = 0;

                        if ($offset < 0)
                            $offset = -1;

                        $buf = \uv_buf_init($length);
                        $uv_fSystem->buffer($buf);
                        $result = \uv_ffi()->uv_fs_read($loop(), $uv_fSystem(), $fd, $buf(), 1, $offset, $uv_fs_cb);
                        break;
                    case \UV::FS_WRITE:
                        $data = \array_shift($arguments);
                        $offset = \array_shift($arguments);
                        $buf = \uv_buf_init($data);
                        $uv_fSystem->buffer($buf);
                        $result = \uv_ffi()->uv_fs_write($loop(), $uv_fSystem(), $fd, $buf(), 1, $offset, $uv_fs_cb);
                        break;
                    case \UV::FS_UNKNOWN:
                    case \UV::FS_CUSTOM:
                    default:
                        \ze_ffi()->zend_error(\E_ERROR, "type; %d does not support yet.", $fs_type);
                        break;
                }
            }

            if ($result < 0) {
                \zval_del_ref($uv_fSystem);
                \ze_ffi()->zend_error(\E_WARNING, "uv_%s failed: %s",  \strtolower(\UV::name($fs_type)), \uv_strerror($result));
            } elseif (\is_null($callback)) {
                $uv_fSystem->free();
                \zval_del_ref($uv_fSystem);
            }

            return  $result;
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
    final class UVFsEvent extends \UV
    {
        public static function init(?UVLoop $loop, ...$arguments)
        {
            $path = \array_shift($arguments);
            $callback = \array_shift($arguments);
            $flags = \array_shift($arguments);
            $fs_event = new static('struct _php_uv_s', 'fs_event');
            $status  = \uv_ffi()->uv_fs_event_init($loop(), $fs_event());

            return $status === 0 ? $fs_event->start($callback, $path, $flags) : $status;
        }

        public function start(callable $callback, string $path, int $flags): int
        {
            $uv_fs_event_cb = function (CData $handle, ?string $filename, int $events, int $status) use ($callback) {
                $callback($this, $filename, $events, $status);
            };

            return \uv_ffi()->uv_fs_event_start($this->uv_struct_type, $uv_fs_event_cb, $path, $flags);
        }
    }
}

if (!\class_exists('UVDirent')) {
    /**
     * @return uv_dirent_t **pointer** by invoking `$UVDirent()`
     */
    final class UVDirent extends \UVTypes
    {
    }
}

if (!\class_exists('UVStat')) {
    /**
     * @return uv_stat_t **pointer** by invoking `$UVStat()`
     */
    final class UVStat extends \UVTypes
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
        protected function __construct(?string $data, int $size = null)
        {
            $this->uv_type = \uv_ffi()->uv_buf_init(\FFI::new('char[' . ($size + 1) . ']'), (int)$size);
            $this->uv_type_ptr = \ffi_ptr($this->uv_type);

            if (!\is_null($size) && \is_null($data)) {
                $this->uv_type_ptr->base = \ffi_characters($size, false);
                $this->uv_type_ptr->len = $size;
            } elseif (!\is_null($data)) {
                $this->uv_type_ptr->base = \ffi_char($data);
                $this->uv_type_ptr->len = \strlen($data);
            }
        }

        public function free(): void
        {
            try {
                \ffi_free_if($this->uv_type_ptr->base);
            } catch (\Throwable $e) {
            }

            parent::free();
        }

        public function getString(int $length = null)
        {
            if (\is_cdata($this->uv_type_ptr->base) && !\is_null_ptr($this->uv_type_ptr->base)) {
                return \is_null($length)
                    ? \FFI::string($this->uv_type_ptr->base)
                    : \FFI::string($this->uv_type_ptr->base, $length);
            }
        }

        public static function init($data = null, ...$arguments)
        {
            $size = \array_shift($arguments);
            $size = \is_null($size) && \is_string($data) ? \strlen($data) : $size;
            return new static($data, $size);
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
    final class UVWriter extends \UVRequest
    {
        /**
         * @param UVStream|uv_stream_t $handle
         * @param string $data
         * @param callable|uv_write_cb $callback expect (\UV $handle, int $status)
         * @return int
         */
        public function write(\UVStream $handle, string $data, callable $callback = null): int
        {
            $buffer = \uv_buf_init($data);
            $r = \uv_ffi()->uv_write($this->uv_type_ptr, \uv_stream($handle), $buffer(), 1, \is_null($callback)
                ? function () {
                }
                :  function (CData $writer, int $status) use ($callback, $handle) {
                    $callback($handle, $status);
                    \FFI::free($writer);
                    \zval_del_ref($this);
                    \zval_del_ref($callback);
                });

            if ($r) {
                \ze_ffi()->zend_error(\E_WARNING, "write failed");
                \zval_del_ref($this);
                \zval_del_ref($buffer);
            }

            return $r;
        }

        /**
         * @param UVTcp|UVPipe|UVTty $handle
         * @param string $data
         * @param UVTcp|UVPipe $send
         * @param callable|uv_write_cb $callback expect (\UVStream $handle, int $status).
         */
        public function write2(\UVStream $handle, string $data, \UVStream $send, callable $callback)
        {
            $buffer = \uv_buf_init($data);
            $r = \uv_ffi()->uv_write2($this->uv_type_ptr, \uv_stream($handle), $buffer(), 1, \uv_stream($send), \is_null($callback)
                ? function () {
                }
                :  function (CData $writer, int $status) use ($callback, $handle) {
                    $callback($handle, $status);
                    \FFI::free($writer);
                    $this->free();
                    \zval_del_ref($this);
                    \zval_del_ref($callback);
                });

            if ($r) {
                \ze_ffi()->zend_error(\E_WARNING, "write2 failed");
                \zval_del_ref($this);
                \zval_del_ref($buffer);
            }

            return $r;
        }
    }
}

if (!\class_exists('UVShutdown')) {
    /**
     * @return uv_shutdown_t **pointer** by invoking `$UVShutdown()`
     */
    final class UVShutdown extends \UVRequest
    {
        public function shutdown(\UVStream $handle, callable $callback = null): int
        {
            \zval_add_ref($this);
            $r = \uv_ffi()->uv_shutdown($this->uv_type_ptr, \uv_stream($handle), !\is_null($callback)
                ? function (CData $shutdown, int $status) use ($callback, $handle) {
                    $callback($handle, $status);
                    \FFI::free($shutdown);
                    \zval_del_ref($this);
                } : null);

            if ($r) {
                \ze_ffi()->zend_error(\E_WARNING, "%s", \uv_strerror($r));
                \zval_del_ref($this);
            }

            return $r;
        }
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

if (!\class_exists('UVUdpSend')) {
    /**
     * @return uv_udp_send_t **pointer** by invoking `$UVUdpSend()`
     */
    final class UVUdpSend extends \UVRequest
    {
    }
}

if (!\class_exists('UVMisc')) {
    final class UVMisc
    {
        public static function interface_addresses()
        {
            $interfaces = \c_typedef('uv_interface_address_t', 'uv');
            $count = \c_int_type('int');
            $ptr = \ffi_characters(512);

            $error = \uv_ffi()->uv_interface_addresses(
                $interfaces->cast('uv_interface_address_t**'),
                $count()
            );

            if (0 == $error) {
                $count = $count->value();
                $free = 0;
                $interfaces->reset();
                $interface = $interfaces->cast_ptr();
                $return_value = [];
                for ($i = 0; $i < $count; $i++) {
                    $name = null;
                    try {
                        $name = \FFI::string($interface[$i]->name);
                    } catch (\Throwable $e) {
                    }

                    if (\is_string($name)) {
                        if ($interface[$i]->address->address4->sin_family == \AF_INET) {
                            \uv_ffi()->uv_ip4_name(\ffi_ptr($interface[$i]->address->address4), $ptr, 512);
                        } elseif ($interface[$i]->address->address6->sin6_family == \AF_INET6) {
                            \uv_ffi()->uv_ip6_name(\ffi_ptr($interface[$i]->address->address6), $ptr, 512);
                        }

                        $buffer = \ffi_string($ptr);
                        $return_value[$i] = [
                            'name' => $name,
                            'is_internal' => (bool) $interface[$i]->is_internal,
                            'address' => $buffer
                        ];
                        $free++;
                    }
                }

                if ($free > 0) {
                    \uv_ffi()->uv_free_interface_addresses($interface, $free);
                }

                return $return_value;
            }

            return $error;
        }

        public static function cpu_info()
        {
            $cpus_type = \c_typedef('uv_cpu_info_t', 'uv');
            $count = \c_int_type('int');

            $error = \uv_ffi()->uv_cpu_info($cpus_type->cast('uv_cpu_info_t**'), $count());
            if (0 == $error) {
                $count = $count->value();
                $free = 0;
                $cpus = $cpus_type->cast_ptr();
                $return_value = [];
                /*
                $ht = \ze_ffi()->_zend_new_array(0);
                $return_zvalue = \zval_array($ht);
                for ($i = 0; $i < $count; $i++) {
                    $tmp = \zval_array(\ze_ffi()->_zend_new_array(0));
                    $times = \zval_array(\ze_ffi()->_zend_new_array(0));

                    \ze_ffi()->add_assoc_string_ex($tmp(), 'model', \strlen("model"), \FFI::string($cpus[$i]->model));
                    \ze_ffi()->add_assoc_long_ex($tmp(), 'speed', \strlen("speed"), $cpus[$i]->speed);

                    \ze_ffi()->add_assoc_long_ex($times(), 'sys', \strlen("sys"), \uv_cast('size_t', $cpus[$i]->cpu_times->sys)->cdata);
                    \ze_ffi()->add_assoc_long_ex($times(), 'user', \strlen("user"), \uv_cast('size_t', $cpus[$i]->cpu_times->user)->cdata);
                    \ze_ffi()->add_assoc_long_ex($times(), 'idle', \strlen("idle"), \uv_cast('size_t', $cpus[$i]->cpu_times->idle)->cdata);
                    \ze_ffi()->add_assoc_long_ex($times(), 'irq', \strlen("irq"), \uv_cast('size_t', $cpus[$i]->cpu_times->irq)->cdata);
                    \ze_ffi()->add_assoc_long_ex($times(), 'nice', \strlen("nice"), \uv_cast('size_t', $cpus[$i]->cpu_times->nice)->cdata);
                    \ze_ffi()->add_assoc_zval_ex($tmp(), 'times', \strlen("times"), $times());

                    \ze_ffi()->zend_hash_next_index_insert($ht, $tmp());
                }

                $return_value = \zval_native($return_zvalue);
                */
                for ($i = 0; $i < $count; $i++) {
                    $model = null;
                    try {
                        $model = \FFI::string($cpus[$i]->model);
                    } catch (\Throwable $e) {
                    }

                    if (\is_string($model)) {
                        $return_value[$i] = [
                            'model' => $model,
                            'speed' => $cpus[$i]->speed,
                            'times' => [
                                'sys'   => \uv_cast('size_t', $cpus[$i]->cpu_times->sys)->cdata,
                                'user'  => \uv_cast('size_t', $cpus[$i]->cpu_times->user)->cdata,
                                'idle'  => \uv_cast('size_t', $cpus[$i]->cpu_times->idle)->cdata,
                                'irq'   => \uv_cast('size_t', $cpus[$i]->cpu_times->irq)->cdata,
                                'nice'  => \uv_cast('size_t', $cpus[$i]->cpu_times->nice)->cdata
                            ]
                        ];

                        $free++;
                    }
                }

                if ($free > 0) {
                    \uv_ffi()->uv_free_cpu_info($cpus[0], $free);
                }

                return $return_value;
            }

            return $error;
        }
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
        protected ?\CStruct $symbol;

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
                $this->symbol = null;

                parent::free();
            }
        }

        /**
         * @param string $definition
         * @return CData|int definition
         */
        public function loadSymbol(string $definition)
        {
            $this->symbol = \c_typedef('void_t', 'uv');
            $status = \uv_ffi()->uv_dlsym($this->uv_type_ptr, $definition, $this->symbol->addr());
            return $status === 0 ? $this->symbol->addr() : $status;
        }

        public function getSymbol(): CData
        {
            return $this->symbol->addr();
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
