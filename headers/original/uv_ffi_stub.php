<?php

/** @var callable (struct uv_loop_s* loop, struct uv__io_s* w, unsigned int events) */
interface uv__io_cb extends closure
{
}
/** @var callable (uv_handle_t* handle, size_t suggested_size, uv_buf_t* buf) */
interface uv_alloc_cb extends closure
{
}
/** @var callable (uv_stream_t* stream, ssize_t nread, const uv_buf_t* buf) */
interface uv_read_cb extends closure
{
}
/** @var callable (uv_write_t* req, int status) */
interface uv_write_cb extends closure
{
}
/** @var callable (uv_connect_t* req, int status) */
interface uv_connect_cb extends closure
{
}
/** @var callable (uv_shutdown_t* req, int status) */
interface uv_shutdown_cb extends closure
{
}
/** @var callable (uv_stream_t* server, int status) */
interface uv_connection_cb extends closure
{
}
/** @var callable (uv_handle_t* handle) */
interface uv_close_cb extends closure
{
}
/** @var callable (uv_poll_t* handle, int status, int events) */
interface uv_poll_cb extends closure
{
}
/** @var callable (uv_timer_t* handle) */
interface uv_timer_cb extends closure
{
}

/** @var callable (uv_async_t* handle) */
interface uv_async_cb extends closure
{
}
/** @var callable (uv_prepare_t* handle) */
interface uv_prepare_cb extends closure
{
}
/** @var callable (uv_check_t* handle) */
interface uv_check_cb extends closure
{
}
/** @var callable (uv_idle_t* handle) */
interface uv_idle_cb extends closure
{
}
/** @var callable (uv_process_t*, int64_t exit_status, int term_signal) */
interface uv_exit_cb extends closure
{
}
/** @var callable (uv_handle_t* handle, void* arg) */
interface uv_walk_cb extends closure
{
}
/** @var callable (uv_fs_t* req) */
interface uv_fs_cb extends closure
{
}
/** @var callable (uv_work_t* req) */
interface uv_work_cb extends closure
{
}
/** @var callable (uv_work_t* req, int status) */
interface uv_after_work_cb extends closure
{
}
/** @var callable (uv_getaddrinfo_t* req, int status, struct addrinfo* res) */
interface uv_getaddrinfo_cb extends closure
{
}
/** @var callable (uv_getnameinfo_t* req, int status, const char* hostname, const char* service) */
interface uv_getnameinfo_cb extends closure
{
}
/** @var callable (uv_random_t* req, int status, void* buf, size_t buflen) */
interface uv_random_cb extends closure
{
}
/** @var callable (uv_fs_event_t* handle, const char* filename, int events, int status) */
interface uv_fs_event_cb extends closure
{
}
/** @var callable (uv_fs_poll_t* handle, int status, const uv_stat_t* prev, const uv_stat_t* curr) */
interface uv_fs_poll_cb extends closure
{
}
/** @var callable (uv_signal_t* handle, int signum) */
interface uv_signal_cb extends closure
{
}

/** Event loop */
abstract class uv_loop_t extends FFI\CData
{
}
/** Base handle */
abstract class uv_handle_t extends FFI\CData
{
}
/** Base request */
abstract class uv_req_t extends FFI\CData
{
}
/** Timer handle */
abstract class uv_timer_t extends uv_handle_t
{
}
/** Prepare handle */
abstract class uv_prepare_t extends uv_handle_t
{
}
/** Check handle */
abstract class uv_check_t extends uv_handle_t
{
}
/** Idle handle */
abstract class uv_idle_t extends uv_handle_t
{
}
/** Async handle */
abstract class uv_async_t extends uv_handle_t
{
}
/** Poll handle */
abstract class uv_poll_t extends uv_handle_t
{
}
/** Signal handle */
abstract class uv_signal_t extends uv_handle_t
{
}
/** Process handle */
abstract class uv_process_t extends uv_handle_t
{
}
/** Stdio handle */
abstract class uv_stdio_container_t extends uv_handle_t
{
}
/** Stream handle */
abstract class uv_stream_t extends uv_handle_t
{
}
/** TCP handle */
abstract class uv_tcp_t extends uv_stream_t
{
}
/** Pipe handle */
abstract class uv_pipe_t extends uv_stream_t
{
}
/** TTY handle */
abstract class uv_tty_t extends uv_handle_t
{
}
/** UDP handle */
abstract class uv_udp_t extends uv_stream_t
{
}
/** FS Event handle */
abstract class uv_fs_event_t extends uv_handle_t
{
}
/** FS Poll handle */
abstract class uv_fs_poll_t extends uv_handle_t
{
}

abstract class FFI
{
    /** @return int */
    public function uv_loop_init(uv_loop_t &$loop)
    {
    }

    /** @return uv_loop_t */
    public function uv_loop_new()
    {
    }

    /** @return uv_loop_t */
    public function uv_default_loop()
    {
    }

    public function uv_loop_delete(uv_loop_t &$loop)
    {
    }

    public function uv_loop_close(uv_loop_t &$loop)
    {
    }

    public function uv_run(uv_loop_t &$loop, int $uv_run_mode)
    {
    }

    public function uv_stop(uv_loop_t &$loop)
    {
    }

    /** @return int */
    public function uv_now(uv_loop_t $uv_loop = null)
    {
    }

    public function uv_close(uv_handle_t &$handle, ?uv_close_cb $callback = null)
    {
    }

    /** @return int */
    public function uv_last_error(uv_loop_t &$loop = null)
    {
    }

    /** @return string */
    public function uv_err_name(int $error_code)
    {
    }

    /** @return string */
    public function uv_strerror(int $error_code)
    {
    }

    public function uv_update_time(uv_loop_t &$loop)
    {
    }

    public function uv_poll_start(uv_poll_t $poll, $events, ?uv_poll_cb $callback = null)
    {
    }

    /** @return int */
    public function uv_poll_init_socket(uv_loop_t $loop, $socket)
    {
    }

    /** @return int */
    public function uv_poll_init(uv_loop_t $loop, $fd)
    {
    }

    public function uv_poll_stop(uv_poll_t $poll)
    {
    }

    public function uv_shutdown(uv_stream_t $handle, ?uv_shutdown_cb $callback = null)
    {
    }

    /** @return int */
    public function uv_timer_init(uv_loop_t $loop = null)
    {
    }

    public function uv_timer_start(uv_timer_t $timer, int $timeout, int $repeat, uv_timer_cb $callback = null)
    {
    }

    public function uv_timer_stop(uv_timer_t $timer)
    {
    }

    public function uv_write(uv_handle_t $handle, string $data, callable $callback)
    {
    }

    public function uv_read_start(uv_stream_t $handle, callable $callback)
    {
    }

    public function uv_fs_open(uv_loop_t $loop, string $path, int $flag, int $mode, callable $callback)
    {
    }

    public function uv_fs_close(uv_loop_t $loop, $fd, callable $callback)
    {
    }

    public function uv_fs_read(uv_loop_t $loop, $fd, int $offset, int $length, callable $callback)
    {
    }

    public function uv_fs_write(uv_loop_t $loop, $fd, string $buffer, int $offset = -1, callable $callback)
    {
    }

    public function uv_fs_fdatasync(uv_loop_t $loop, $fd, callable $callback)
    {
    }

    public function uv_fs_scandir(uv_loop_t $loop, string $path, int $flags = 0, callable $callback)
    {
    }

    public function uv_fs_stat(uv_loop_t $loop, string $path, callable $callback)
    {
    }

    public function uv_fs_lstat(uv_loop_t $loop, string $path, callable $callback)
    {
    }

    public function uv_fs_fstat(uv_loop_t $loop, $fd, callable $callback)
    {
    }

    public function uv_fs_sendfile(uv_loop_t $loop, $out_fd, $in_fd, int $offset, int $length, callable $callback)
    {
    }

    public function uv_is_active(uv_handle_t $handle)
    {
    }

    public function uv_fs_poll_start(uv_poll_t $poll, $callback, string $path, int $interval)
    {
    }

    public function uv_fs_poll_stop(uv_poll_t $poll)
    {
    }

    /** @return int */
    public function uv_fs_poll_init(uv_loop_t $loop)
    {
    }

    public function uv_exepath()
    {
    }

    public function uv_cwd()
    {
    }

    public function uv_cpu_info()
    {
    }

    /** @return int */
    public function uv_signal_init(uv_loop_t $loop = null)
    {
    }

    public function uv_signal_start(UVSignal $handle, callable $callback, int $signal)
    {
    }

    public function uv_signal_stop(UVSignal $handle)
    {
    }

    public function uv_spawn(
        uv_loop_t $loop,
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

    public function uv_process_kill(UVProcess $process, int $signal)
    {
    }

    public function uv_process_get_pid(UVProcess $process)
    {
    }

    public function uv_kill(int $pid, int $signal)
    {
    }

    /** @return int */
    public function uv_pipe_init(uv_loop_t $loop, bool $ipc)
    {
    }

    public function uv_pipe_open(UVPipe $handle, int $pipe)
    {
    }

    public function uv_pipe_bind(UVPipe $handle, string $name)
    {
    }

    public function uv_pipe_connect(UVPipe $handle, string $path, callable $callback)
    {
    }

    public function uv_pipe_pending_instances(UVPipe $handle, $count)
    {
    }

    public function uv_stdio_new($fd, int $flags)
    {
    }

    /** @return int */
    public function uv_async_init(uv_loop_t &$loop, uv_async_t &$async, uv_async_cb $callback)
    {
    }

    /** @return int */
    public function uv_async_send(uv_async_t &$handle)
    {
    }

    public function uv_queue_work(uv_loop_t &$loop, callable $callback, callable $after_callback)
    {
    }

    public function uv_idle_init(uv_loop_t $loop = null)
    {
    }

    public function uv_idle_start(UVIdle $idle, callable $callback)
    {
    }

    public function uv_idle_stop(UVIdle $idle)
    {
    }

    /** @return int */
    public function uv_prepare_init(uv_loop_t $loop = null)
    {
    }

    public function uv_prepare_start(UVPrepare $handle, callable $callback)
    {
    }

    public function uv_prepare_stop(UVPrepare $handle)
    {
    }

    /** @return int */
    public function uv_check_init(uv_loop_t $loop = null)
    {
    }

    public function uv_check_start(UVCheck $handle, callable $callback)
    {
    }

    public function uv_check_stop(UVCheck $handle)
    {
    }

    public function uv_ref(uv_handle_t $uv_handle)
    {
    }

    public function uv_unref(uv_handle_t $uv_t)
    {
    }

    public function uv_tcp_bind(uv_tcp_t $uv_tcp, UVSockAddr $uv_sockaddr)
    {
    }

    public function uv_tcp_bind6(uv_tcp_t $uv_tcp, UVSockAddr $uv_sockaddr)
    {
    }

    public function uv_write2(uv_stream_t $handle, string $data, $send, callable $callback)
    {
    }

    public function uv_tcp_nodelay(uv_tcp_t $handle, bool $enable)
    {
    }

    public function uv_accept($server, $client)
    {
    }

    public function uv_listen($handle, int $backlog, callable $callback)
    {
    }

    public function uv_read_stop(uv_stream_t $handle)
    {
    }

    public function uv_ip4_addr(string $ipv4_addr, int $port)
    {
    }

    public function uv_ip6_addr(string $ipv6_addr, int $port)
    {
    }

    public function uv_tcp_connect(uv_tcp_t $handle, UVSockAddr $ipv4_addr, callable $callback)
    {
    }

    public function uv_tcp_connect6(uv_tcp_t $handle, UVSockAddrIPv6 $ipv6_addr, callable $callback)
    {
    }

    public function uv_timer_again(uv_timer_t $timer)
    {
    }

    public function uv_timer_set_repeat(uv_timer_t $timer, int $repeat)
    {
    }

    public function uv_timer_get_repeat(uv_timer_t $timer)
    {
    }

    public function uv_getaddrinfo(uv_loop_t $loop, callable $callback, string $node = null, string $service = null, array $hints = [])
    {
    }

    public function uv_ip4_name(UVSockAddr $address)
    {
    }

    public function uv_ip6_name(UVSockAddr $address)
    {
    }

    /** @return int */
    public function uv_tcp_init(uv_loop_t $loop = null)
    {
    }

    public function uv_udp_init(uv_loop_t $loop = null)
    {
    }

    public function uv_udp_bind(UVUdp $handle, UVSockAddr $address, int $flags = 0)
    {
    }

    public function uv_udp_bind6(UVUdp $handle, UVSockAddr $address, int $flags = 0)
    {
    }

    public function uv_udp_recv_start(UVUdp $handle, callable $callback)
    {
    }

    public function uv_udp_recv_stop(UVUdp $handle)
    {
    }

    public function uv_udp_set_membership(UVUdp $handle, string $multicast_addr, string $interface_addr, int $membership)
    {
    }

    public function uv_udp_set_multicast_loop(UVUdp $handle, bool $enabled)
    {
    }

    public function uv_udp_set_multicast_ttl(UVUdp $handle, int $ttl)
    {
    }

    public function uv_udp_set_broadcast(UVUdp $handle, bool $enabled)
    {
    }

    public function uv_udp_send(UVUdp $handle, string $data, UVSockAddr $uv_addr, callable $callback)
    {
    }

    public function uv_udp_send6(UVUdp $handle, string $data, UVSockAddrIPv6 $uv_addr6, callable $callback)
    {
    }

    public function uv_is_readable(uv_stream_t $handle)
    {
    }

    public function uv_is_writable(uv_stream_t $handle)
    {
    }

    public function uv_walk(uv_loop_t $loop, callable $closure, array $opaque = null)
    {
    }

    public function uv_guess_handle($uv)
    {
    }

    public function uv_loadavg()
    {
    }

    public function uv_rwlock_init()
    {
    }

    public function uv_rwlock_rdlock(UVLock $handle)
    {
    }

    public function uv_rwlock_tryrdlock(UVLock $handle)
    {
    }

    public function uv_rwlock_rdunlock(UVLock $handle)
    {
    }

    public function uv_rwlock_wrlock(UVLock $handle)
    {
    }

    public function uv_rwlock_trywrlock(UVLock $handle)
    {
    }

    public function uv_rwlock_wrunlock(UVLock $handle)
    {
    }

    public function uv_mutex_init()
    {
    }

    public function uv_mutex_lock(UVLock $lock)
    {
    }

    public function uv_mutex_trylock(UVLock $lock)
    {
    }

    public function uv_sem_init(int $value)
    {
    }

    public function uv_sem_post(UVLock $sem)
    {
    }

    public function uv_sem_wait(UVLock $sem)
    {
    }

    public function uv_sem_trywait(UVLock $sem)
    {
    }

    public function uv_hrtime()
    {
    }

    public function uv_fs_fsync(uv_loop_t $loop, $fd, callable $callback)
    {
    }

    public function uv_fs_ftruncate(uv_loop_t $loop, $fd, int $offset, callable $callback)
    {
    }

    public function uv_fs_mkdir(uv_loop_t $loop, string $path, int $mode, callable $callback)
    {
    }

    public function uv_fs_rmdir(uv_loop_t $loop, string $path, callable $callback)
    {
    }

    public function uv_fs_unlink(uv_loop_t $loop, string $path, callable $callback)
    {
    }

    public function uv_fs_rename(uv_loop_t $loop, string $from, string $to, callable $callback)
    {
    }

    public function uv_fs_utime(uv_loop_t $loop, string $path, int $utime, int $atime, callable $callback)
    {
    }

    public function uv_fs_futime(uv_loop_t $loop, $fd, int $utime, int $atime, callable $callback)
    {
    }

    public function uv_fs_chmod(uv_loop_t $loop, string $path, int $mode, callable $callback)
    {
    }

    public function uv_fs_fchmod(uv_loop_t $loop, $fd, int $mode, callable $callback)
    {
    }

    public function uv_fs_chown(uv_loop_t $loop, string $path, int $uid, int $gid, callable $callback)
    {
    }

    public function uv_fs_fchown(uv_loop_t $loop, $fd, int $uid, int $gid, callable $callback)
    {
    }

    public function uv_fs_link(uv_loop_t $loop, string $from, string $to, callable $callback)
    {
    }

    public function uv_fs_symlink(uv_loop_t $loop, string $from, string $to, int $flags, callable $callback)
    {
    }

    public function uv_fs_readlink(uv_loop_t $loop, string $path, callable $callback)
    {
    }


    public function uv_fs_readdir(uv_loop_t $loop, string $path, int $flags, callable $callback)
    {
    }

    /** @return int */
    public function uv_fs_event_init(uv_loop_t $loop, string $path, callable $callback, int $flags = 0)
    {
    }

    /** @return int */
    public function uv_tty_init(uv_loop_t $loop, $fd, int $readable)
    {
    }

    public function uv_tty_get_winsize(UVTty $tty, int &$width, int &$height)
    {
    }

    public function uv_tty_set_mode(UVTty $tty, int $mode)
    {
    }

    public function uv_tty_reset_mode()
    {
    }

    public function uv_uptime()
    {
    }

    public function uv_get_free_memory()
    {
    }

    public function uv_get_total_memory()
    {
    }

    public function uv_interface_addresses()
    {
    }

    public function uv_chdir(string $directory)
    {
    }

    public function uv_tcp_getsockname(uv_tcp_t $uv_sock)
    {
    }

    public function uv_tcp_getpeername(uv_tcp_t $uv_sock)
    {
    }

    public function uv_udp_getsockname(UVUdp $uv_sock)
    {
    }

    public function uv_resident_set_memory()
    {
    }

    public function uv_handle_get_type(uv_handle_t $uv)
    {
    }

    public function uv_tcp_open(uv_tcp_t $handle, int $tcpfd)
    {
    }

    public function uv_udp_open(UVUdp $handle, int $udpfd)
    {
    }

    public function uv_is_closing(uv_handle_t $handle)
    {
    }

    public function uv_run_once(uv_loop_t $uv_loop = null)
    {
    }
}
