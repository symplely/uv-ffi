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
/** @var callable (uv_write_t &$req, int status) */
interface uv_write_cb extends closure
{
}
/** @var callable (uv_connect_t &$req, int status) */
interface uv_connect_cb extends closure
{
}
/** @var callable (uv_shutdown_t &$req, int status) */
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
/** @var callable (uv_fs_t &$req) */
interface uv_fs_cb extends closure
{
}
/** @var callable (uv_work_t &$req) */
interface uv_work_cb extends closure
{
}
/** @var callable (uv_work_t &$req, int status) */
interface uv_after_work_cb extends closure
{
}
/** @var callable (uv_getaddrinfo_t &$req, int status, struct addrinfo* res) */
interface uv_getaddrinfo_cb extends closure
{
}
/** @var callable (uv_getnameinfo_t &$req, int status, const_char $hostname, const_char $service) */
interface uv_getnameinfo_cb extends closure
{
}
/** @var callable (uv_random_t &$req, int status, void* buf, size_t buflen) */
interface uv_random_cb extends closure
{
}
/** @var callable (uv_fs_event_t* handle, const_char $filename, int events, int status) */
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
/**Write request type. */
abstract class uv_write_t extends uv_req_t
{
}
/** File system request type */
abstract class uv_fs_t extends uv_req_t
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
abstract class uv_tty_t extends uv_stream_t
{
}
/** UDP handle */
abstract class uv_udp_t extends uv_stream_t
{
}
/** FS Event request */
abstract class uv_fs_event_t extends uv_handle_t
{
}
/** FS Poll request */
abstract class uv_fs_poll_t extends uv_req_t
{
}
/** getaddrinfo request type */
abstract class uv_getaddrinfo_t extends uv_getaddrinfo_s
{
}
/** getnameinfo request type. */
abstract class uv_getnameinfo_t extends uv_getnameinfo_s
{
}
/** The kind of the libuv handle */
abstract class uv_handle_type extends int
{
}
/** File system request type */
abstract class uv_fs_type extends int
{
}
abstract class uv_req_type extends int
{
}
abstract class uint64_t extends int
{
}
abstract class int64_t extends int
{
}
/** Abstract representation of a file descriptor. On Unix systems this is a typedef of `int`
 * and on Windows a `HANDLE` */
abstract class uv_os_fd_t extends Resource
{
}
abstract class uv_lib_t extends UVTypes
{
}
/** Cross platform representation of a file handle. */
abstract class uv_file extends int
{
}
abstract class uv_os_sock_t extends php_socket_t
{
}
abstract class uv_buf_t extends FFI\CData
{
}
abstract class sockaddr_storage extends FFI\CData
{
}
abstract class UVSockAddr extends sockaddr
{
}
abstract class sockaddr_in extends sockaddr
{
}
abstract class sockaddr_in6 extends sockaddr
{
}
abstract class addrinfo extends FFI\CData
{
}
abstract class uv_uid_t extends string
{
}
abstract class uv_gid_t extends string
{
}
abstract class uv_dir_t extends FFI\CData
{
}
abstract class uv_stat_t  extends FFI\CData
{
}
abstract class uv_dirent_t extends FFI\CData
{
}
abstract class UVDirent extends uv_dirent_t
{
}
abstract class uv_getaddrinfo_s extends uv_req_t
{
}
abstract class uv_getnameinfo_s extends uv_req_t
{
}
abstract class UVGetAddrinfo extends uv_getaddrinfo_t
{
}
abstract class UVGetNameinfo extends uv_getnameinfo_t
{
}
abstract class DWORD extends int
{
}
abstract class HANDLE extends void_ptr
{
}
abstract class char extends FFI\CData
{
}
abstract class const_char extends string
{
}


abstract class UVTcp extends uv_tcp_t
{
}
abstract class UVPipe extends uv_pipe_t
{
}
abstract class UVTty extends uv_tty_t
{
}
abstract class UVLoop extends uv_loop_t
{
}
abstract class UVTimer extends uv_timer_t
{
}
abstract class UVCheck extends uv_check_t
{
}
abstract class UVPrepare extends uv_prepare_t
{
}
abstract class UVStream extends uv_stream_t
{
}
abstract class UVIdle extends uv_idle_t
{
}
abstract class UVFs extends uv_fs_t
{
}
abstract class UVTypes extends \FFI\CData
{
}
abstract class UVBuffer extends uv_buf_t
{
}
abstract class UVWriter extends uv_write_t
{
}
abstract class UVLib extends uv_lib_t
{
}
abstract class UVStat extends uv_stat_t
{
}
abstract class UVRequest extends uv_req_t
{
}
abstract class UV extends uv_handle_t
{
}
abstract class uv_shutdown_t extends uv_req_t
{
}
abstract class int_ptr extends FFI\CData
{
}
abstract class void_t extends FFI\CData
{
}
abstract class void_ptr extends void_t
{
}
abstract class sockaddr extends FFI\CData
{
}
abstract class uv_connect_t extends uv_req_t
{
}

interface FFI
{
    /** @return int */
    public function uv_loop_init(uv_loop_t &$loop);

    /** @return uv_loop_t */
    public function uv_loop_new();

    /** @return uv_loop_t */
    public function uv_default_loop();

    /** @return void */
    public function uv_loop_delete(uv_loop_t &$loop);

    /** @return void */
    public function uv_loop_close(uv_loop_t &$loop);

    /** @return void */
    public function uv_run(uv_loop_t &$loop, int $uv_run_mode);

    /** @return void */
    public function uv_stop(uv_loop_t &$loop);

    /** @return int */
    public function uv_now(uv_loop_t &$uv_loop = null);

    /** @return void */
    public function uv_close(uv_handle_t &$handle, ?uv_close_cb $callback = null);

    /** @return string */
    public function uv_err_name(int $error_code);

    /** @return string */
    public function uv_strerror(int $error_code);

    /** @return void */
    public function uv_update_time(uv_loop_t &$loop);

    /** @return int */
    public function uv_write(uv_write_t &$req, uv_stream_t &$handle, uv_buf_t $bufs, int $nbufs, uv_write_cb $callback);

    /** @return int */
    public function uv_try_write(uv_stream_t &$handle, uv_buf_t $bufs, int $nbufs);

    /** @return int */
    public function uv_read_start(uv_stream_t &$handle, uv_alloc_cb $alloc_cb, uv_read_cb $read_cb);

    /** @return int */
    public function uv_read_stop(uv_stream_t &$handle);

    /** @return uv_buf_t */
    public function uv_buf_init(char &$base, int $len);

    /** @return int */
    public function uv_dlopen(char $filename, uv_lib_t &$lib);

    public function uv_dlclose(uv_lib_t &$lib);

    /** @return int */
    public function uv_dlsym(uv_lib_t &$lib, string $name, void_ptr &$ptr);

    /** @return string */
    public function uv_dlerror(uv_lib_t &$lib);

    /** @return int */
    public function uv_shutdown(uv_shutdown_t &$req, uv_stream_t &$handle, uv_shutdown_cb $cb);

    /** @return int */
    public function uv_tty_init(uv_loop_t &$loop, uv_tty_t &$tty, uv_file $fd, int $readable);

    /** @return int */
    public function uv_tty_set_mode(uv_tty_t &$tty, int $mode);

    /** @return uv_os_fd_t */
    public function uv_get_osfhandle(int $fd);

    /** @return int */
    public function uv_open_osfhandle(uv_os_fd_t $fd);

    /** @return int */
    public function uv_fileno(uv_handle_t &$handle, uv_os_fd_t &$fd);

    /** @return uv_handle_type */
    public function uv_guess_handle(uv_file $file);

    /** @return int */
    public function uv_available_parallelism();

    /** @return int */
    public function uv_pipe(uv_file &$fds, int $read_flags, int $write_flags);

    /** @return int */
    public function uv_pipe_chmod(uv_pipe_t &$handle, int $flags);

    /** @return int */
    public function uv_pipe_init(uv_loop_t &$loop, uv_pipe_t &$pipe, int $ipc);

    /** @return int|false */
    public function uv_pipe_open(uv_pipe_t &$handle, uv_file $pipe);

    /** @return int */
    public function uv_async_init(uv_loop_t &$loop, uv_async_t &$async, uv_async_cb $callback);

    /** @return int */
    public function uv_async_send(uv_async_t &$handle);

    public function uv_library_shutdown();

    /** @return int */
    public function uv_poll_init_socket(uv_loop_t &$loop,  uv_poll_t &$handle, uv_os_sock_t $socket);

    /** @return int */
    public function uv_poll_start(uv_poll_t &$handle, int $events, uv_poll_cb $callback);

    /** @return int */
    public function uv_poll_init(uv_loop_t &$loop, uv_poll_t &$handle, int $fd);

    /** @return int */
    public function uv_poll_stop(uv_poll_t &$handle);

    /** @return int */
    public function uv_timer_init(uv_loop_t &$loop = null, uv_timer_t &$handle);

    /** @return int */
    public function uv_timer_start(uv_timer_t &$timer, uv_timer_cb $callback = null, uint64_t $timeout, uint64_t $repeat);

    /** @return int */
    public function uv_timer_stop(uv_timer_t &$timer);

    /** @return int */
    public function  uv_timer_again(uv_timer_t &$handle);

    /** @return void */
    public function  uv_timer_set_repeat(uv_timer_t &$handle, uint64_t $repeat);

    /** @return uint64_t */
    public function  uv_timer_get_repeat(uv_timer_t &$handle);

    /** @return uint64_t */
    public function uv_timer_get_due_in(uv_timer_t &$handle);

    /** @return int */
    public function uv_cancel(uv_req_t &$req);

    /** @return void_ptr|object */
    public function uv_req_get_data(uv_req_t &$req);

    /** @return void */
    public function uv_req_set_data(uv_req_t &$req, void_ptr &$data);

    /** @return uv_req_type|int */
    public function uv_req_get_type(uv_req_t &$req);

    /** @return const_char|string */
    public function uv_req_type_name(uv_req_type $type);

    /** @return uv_fs_type|int */
    public function uv_fs_get_type(uv_fs_t &$ptr);

    /** @return ssize_t|int */
    public function uv_fs_get_result(uv_fs_t &$ptr);

    /** @return int */
    public function uv_fs_get_system_error(uv_fs_t &$ptr);

    /** @return void_ptr|object */
    public function uv_fs_get_ptr(uv_fs_t &$ptr);

    /** @return const_char|string */
    public function uv_fs_get_path(uv_fs_t &$ptr);

    /** @return uv_stat_t */
    public function uv_fs_get_statbuf(uv_fs_t &$ptr);

    /** @return void */
    public function uv_fs_req_cleanup(uv_fs_t &$req);

    /** @return int */
    public function uv_fs_open(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, int $flags, int $mode, uv_fs_cb $callback);

    /** @return int */
    public function uv_fs_unlink(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $callback);

    /** @return int */
    public function uv_fs_close(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_fs_cb $callback);

    /** @return int */
    public function uv_fs_read(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_buf_t $bufs, int $nbufs, int64_t $offset, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_write(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_buf_t $bufs, int $nbufs, int64_t $offset, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_copyfile(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, const_char $new_path, int $flags, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_mkdir(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, int $mode, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_mkdtemp(uv_loop_t &$loop, uv_fs_t &$req, const_char $tpl, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_mkstemp(uv_loop_t &$loop, uv_fs_t &$req, const_char $tpl, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_rmdir(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_scandir(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, int $flags, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_scandir_next(uv_fs_t &$req, uv_dirent_t &$ent);

    /** @return int */
    public function uv_fs_opendir(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_readdir(uv_loop_t &$loop, uv_fs_t &$req, uv_dir_t &$dir, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_closedir(uv_loop_t &$loop, uv_fs_t &$req, uv_dir_t &$dir, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_stat(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_fstat(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_rename(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, const_char $new_path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_fsync(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_fdatasync(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_ftruncate(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, int64_t $offset, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_sendfile(uv_loop_t &$loop, uv_fs_t &$req, uv_file $out_fd, uv_file $in_fd, int64_t $in_offset, size_t $length, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_access(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, int $mode, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_chmod(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, int $mode, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_utime(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, double $atime, double $mtime, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_futime(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, double $atime, double $mtime, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_lutime(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, double $atime, double $mtime, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_lstat(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_link(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, const_char $new_path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_symlink(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, const_char $new_path, int $flags, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_readlink(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_realpath(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_fchmod(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, int $mode, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_chown(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_uid_t $uid, uv_gid_t $gid, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_fchown(uv_loop_t &$loop, uv_fs_t &$req, uv_file $file, uv_uid_t $uid, uv_gid_t $gid, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_lchown(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_uid_t $uid, uv_gid_t $gid, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_statfs(uv_loop_t &$loop, uv_fs_t &$req, const_char $path, uv_fs_cb $cb);

    /** @return int */
    public function uv_fs_poll_init(uv_loop_t $loop, uv_fs_poll_t &$handle);

    /** @return int */
    public function uv_fs_poll_start(uv_fs_poll_t &$handle, uv_fs_poll_cb $poll_cb, const_char $path, int $interval);

    /** @return int */
    public function uv_fs_poll_stop(uv_fs_poll_t &$handle);

    /** @return int */
    public function uv_fs_poll_getpath(uv_fs_poll_t &$handle, char &$buffer, size_t &$size);

    /** @return int */
    public function uv_is_active(uv_handle_t $handle);

    public function uv_exepath();

    public function uv_cwd();

    public function uv_cpu_info();

    /** @return int */
    public function uv_signal_init(uv_loop_t $loop = null);

    public function uv_signal_start(UVSignal $handle, callable $callback, int $signal);

    public function uv_signal_stop(UVSignal $handle);

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
    );

    public function uv_process_kill(UVProcess $process, int $signal);

    public function uv_process_get_pid(UVProcess $process);

    public function uv_kill(int $pid, int $signal);

    public function uv_pipe_bind(UVPipe $handle, string $name);

    public function uv_pipe_connect(UVPipe $handle, string $path, callable $callback);

    public function uv_pipe_pending_instances(UVPipe $handle, $count);

    public function uv_stdio_new($fd, int $flags);

    public function uv_queue_work(uv_loop_t &$loop, callable $callback, callable $after_callback);


    /** @return int */
    public function uv_prepare_init(uv_loop_t $loop = null);

    /** @return int */
    public function uv_prepare_start(UVPrepare $handle, callable $callback);

    /** @return int */
    public function uv_prepare_stop(UVPrepare $handle);

    /** @return int */
    public function uv_check_init(uv_loop_t &$loop, uv_check_t &$check);

    /** @return int */
    public function uv_check_start(uv_check_t &$check, uv_check_cb $callback);

    /** @return int */
    public function uv_check_stop(uv_check_t $check);

    /** @return int */
    public function uv_idle_init(uv_loop_t $loop, uv_idle_t &$idle);

    /** @return int */
    public function uv_idle_start(uv_idle_t &$idle, uv_idle_cb $callback);

    /** @return int */
    public function uv_idle_stop(uv_idle_t &$idle);

    /** @return void */
    public function uv_ref(uv_handle_t &$uv_handle);

    /** @return void */
    public function uv_unref(uv_handle_t &$uv_t);

    public function uv_tcp_bind6(uv_tcp_t $uv_tcp, UVSockAddr $uv_sockaddr);

    public function uv_write2(uv_stream_t $handle, string $data, $send, uv_write_cb $callback);

    public function uv_tcp_nodelay(uv_tcp_t $handle, bool $enable);

    /** @return int */
    public function uv_tcp_init(uv_loop_t &$loop, uv_tcp_t &$handle);

    /** @return int */
    public function uv_accept(uv_stream_t &$server, uv_stream_t &$client);

    /** @return int */
    public function uv_tcp_bind(uv_tcp_t &$handle, sockaddr &$addr, int $flags);

    /** @return int */
    public function uv_listen(uv_stream_t &$stream, int $backlog, uv_connection_cb $cb);

    /** @return int */
    public function uv_ip4_addr(const_char $ip, int $port, sockaddr_in &$addr);

    /** @return int */
    public function uv_ip4_name(sockaddr_in &$src, char &$dst, size_t $size);

    /** @return int */
    public function uv_ip6_addr(const_char $ip, int $port, sockaddr_in6 &$addr);

    /** @return int */
    public function uv_ip6_name(sockaddr_in6 &$src, char &$dst, size_t $size);

    /** @return int */
    public function uv_tcp_connect(uv_connect_t &$req, uv_tcp_t &$handle, sockaddr &$addr, uv_connect_cb $cb);

    /** @return int */
    public function uv_getaddrinfo(uv_loop_t &$loop, uv_getaddrinfo_t &$req, uv_getaddrinfo_cb $getaddrinfo_cb, const_char $node, const_char $service, addrinfo &$hints);

    /** @return int */
    public function uv_getnameinfo(uv_loop_t &$loop, uv_getnameinfo_t &$req, uv_getnameinfo_cb $getnameinfo_cb, sockaddr &$addr, int $flags);

    /** @return void */
    public function uv_freeaddrinfo(?addrinfo &$ai);

    /** @return int */
    public function uv_inet_ntop(int $af, void_ptr &$src, char &$dst, size_t $size);

    /** @return int */
    public function uv_inet_pton(int $af, const_char $src, void_ptr &$dst);

    public function uv_udp_init(uv_loop_t $loop = null);

    public function uv_udp_bind(UVUdp $handle, UVSockAddr $address, int $flags = 0);

    public function uv_udp_bind6(UVUdp $handle, UVSockAddr $address, int $flags = 0);

    public function uv_udp_recv_start(UVUdp $handle, callable $callback);

    public function uv_udp_recv_stop(UVUdp $handle);

    public function uv_udp_set_membership(UVUdp $handle, string $multicast_addr, string $interface_addr, int $membership);

    public function uv_udp_set_multicast_loop(UVUdp $handle, bool $enabled);

    public function uv_udp_set_multicast_ttl(UVUdp $handle, int $ttl);

    public function uv_udp_set_broadcast(UVUdp $handle, bool $enabled);

    public function uv_udp_send(UVUdp $handle, string $data, UVSockAddr $uv_addr, callable $callback);

    public function uv_udp_send6(UVUdp $handle, string $data, UVSockAddrIPv6 $uv_addr6, callable $callback);

    /** @return int */
    public function uv_is_readable(uv_stream_t &$handle);

    /** @return int */
    public function uv_is_writable(uv_stream_t &$handle);

    /** @return uv_loop_t */
    public function uv_handle_get_loop(uv_handle_t &$handle);

    public function uv_walk(uv_loop_t $loop, callable $closure, array $opaque = null);

    public function uv_loadavg();

    public function uv_rwlock_init();

    public function uv_rwlock_rdlock(UVLock $handle);

    public function uv_rwlock_tryrdlock(UVLock $handle);

    public function uv_rwlock_rdunlock(UVLock $handle);

    public function uv_rwlock_wrlock(UVLock $handle);

    public function uv_rwlock_trywrlock(UVLock $handle);

    public function uv_rwlock_wrunlock(UVLock $handle);

    public function uv_mutex_init();

    public function uv_mutex_lock(UVLock $lock);

    public function uv_mutex_trylock(UVLock $lock);

    public function uv_sem_init(int $value);

    public function uv_sem_post(UVLock $sem);

    public function uv_sem_wait(UVLock $sem);

    public function uv_sem_trywait(UVLock $sem);

    public function uv_hrtime();

    /** @return int */
    //  public function uv_fs_event_init(uv_loop_t $loop, string $path, callable $callback, int $flags = 0);

    /** @return int */
    public function uv_fs_event_init(uv_loop_t &$loop, uv_fs_event_t &$handle);

    /** @return int */
    public function uv_fs_event_start(uv_fs_event_t &$handle, uv_fs_event_cb $cb, const_char $path, int $flags);

    /** @return int */
    public function uv_fs_event_stop(uv_fs_event_t &$handle);

    /** @return int */
    public function uv_fs_event_getpath(uv_fs_event_t &$handle, char &$buffer, size_t $size);

    public function uv_tty_get_winsize(UVTty $tty, int &$width, int &$height);

    public function uv_tty_reset_mode();

    public function uv_uptime();

    public function uv_get_free_memory();

    public function uv_get_total_memory();

    public function uv_interface_addresses();

    public function uv_chdir(string $directory);

    /** @return int */
    public function uv_tcp_getsockname(uv_tcp_t &$handle, sockaddr &$name, int_ptr &$namelen);

    /** @return int */
    public function uv_tcp_getpeername(uv_tcp_t &$handle, sockaddr &$name, int_ptr &$namelen);

    public function uv_udp_getsockname(UVUdp $uv_sock);

    public function uv_resident_set_memory();

    /** @return uv_handle_type */
    public function uv_handle_get_type(uv_handle_t &$uv);

    public function uv_tcp_open(uv_tcp_t $handle, int $tcpfd);

    public function uv_udp_open(UVUdp $handle, int $udpfd);

    /** @return int */
    public function uv_is_closing(uv_handle_t &$handle);

    public function uv_run_once(uv_loop_t $uv_loop = null);
}
