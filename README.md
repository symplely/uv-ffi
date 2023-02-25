# uv-ffi

[![Windows ](https://github.com/symplely/uv-ffi/actions/workflows/Windows-ffi.yml/badge.svg?branch=0.3x)](https://github.com/symplely/uv-ffi/actions/workflows/Windows-ffi.yml)[![Linux ](https://github.com/symplely/uv-ffi/actions/workflows/linux-ffi.yml/badge.svg?branch=0.3x)](https://github.com/symplely/uv-ffi/actions/workflows/linux-ffi.yml)[![macOS ](https://github.com/symplely/uv-ffi/actions/workflows/macOS-ffi.yml/badge.svg?branch=0.3x)](https://github.com/symplely/uv-ffi/actions/workflows/macOS-ffi.yml)[![codecov](https://codecov.io/gh/symplely/uv-ffi/branch/main/graph/badge.svg?token=BUL9sf3Yv0)](https://codecov.io/gh/symplely/uv-ffi)

 An [Foreign function interface](https://en.wikipedia.org/wiki/Foreign_function_interface) ([FFI](https://github.com/libffi/libffi)) for PHP of **[libuv](http://docs.libuv.org/en/v1.x/)** cross-platform event-driven _asynchronous_ I/O library.

This **libuv ffi** implementation is based on PHP extension [ext-uv](https://github.com/amphp/ext-uv). All **ext-uv 0.3.0** _tests and functions_ been implemented, except **uv_queue_work**.

- Functionality works as expected under _`Windows`_, _`Linux`_, and _`Apple macOS`_, **PHP 7.4 to 8.2**.
- All functionality is interdependent on [zend-ffi](https://github.com/symplely/zend-ffi).

The actual threading feature of `uv_queue_work` in **ext-uv 0.3.0** is on pause. Getting native PThreads working with FFI, needs a lot more investigation and more likely C development of PHP source code. Seems someone else has started something similar <https://github.com/mrsuh/php-pthreads>.

**PR** are welcome, see [Documentation] and [Contributing].

Future versions of `uv-ffi` beyond **ext-uv 0.3.0** will include all current `libuv` features.

## Installation

There will be two ways:
    composer require symplely/uv-ffi
and:
    composer create-project symplely/uv-ffi .cdef/libuv

This package/repo is self-contained for Windows and Apple macOS, meaning it has **GitHub Actions** building `libuv` _binary_ `.dll` & `.dylib`, and committing back to repo. The other platforms will use the distributions included `libuv` _binary_ version.

The `create-project` will setup a different loading/installation area. This feature is still a work in progress.

Minimum `php.ini` setting:

```ini
extension=ffi
extension=openssl
extension=sockets

zend_extension=opcache

[opcache]
; Determines if Zend OPCache is enabled
opcache.enable=1

; Determines if Zend OPCache is enabled for the CLI version of PHP
opcache.enable_cli=1

[ffi]
; FFI API restriction. Possible values:
; "preload" - enabled in CLI scripts and preloaded files (default)
; "false"   - always disabled
; "true"    - always enabled
ffi.enable="true"

; List of headers files to preload, wildcard patterns allowed. `ffi.preload` has no effect on Windows.
; Replace `your-platform` with: windows, centos7, centos8+, macos, pi, ubuntu18.04, or ubuntu20.04
; This feature is untested, since not enabled for Windows.
;ffi.preload=path/to/vendor/symplely/uv-ffi/headers/uv_your-platform_generated.h

;This feature is untested, since not enabled for Windows.
;opcache.preload==path/to/vendor/symplely/uv-ffi/preload.php
```

## How to use

- The functions in file [UVFunctions.php](https://github.com/symplely/uv-ffi/blob/main/src/UVFunctions.php) is the **only** _means of accessing_ **libuv** features.

The following is a PHP `tcp echo server` converted from `C` [uv book](https://nikhilm.github.io/uvbook/networking.html#tcp) that's follows also. Most of the required `C` setup and cleanup code is done automatically.

This will print "Got a connection!" to console if visited.

```php
require 'vendor/autoload.php';

$loop = uv_default_loop();
define('DEFAULT_PORT', 7000);
define('DEFAULT_BACKLOG', 128);

$echo_write = function (UV $req, int $status) {
    if ($status) {
        printf("Write error %s\n", uv_strerror($status));
    }

    print "Got a connection!\n";
};

$echo_read = function (UVStream $client, int $nRead, string $buf = null) use ($echo_write) {
    if ($nRead > 0) {
        uv_write($client, $buf, $echo_write);
        return;
    }

    if ($nRead < 0) {
        if ($nRead != UV::EOF)
            printf("Read error %s\n", uv_err_name($nRead));

        uv_close($client);
    }
};

$on_new_connection = function (UVStream $server, int $status) use ($echo_read, $loop) {
    if ($status < 0) {
        printf("New connection error %s\n", uv_strerror($status));
        // error!
        return;
    }

    $client = uv_tcp_init($loop);
    if (uv_accept($server, $client) == 0) {
        uv_read_start($client, $echo_read);
    } else {
        uv_close($client);
    }
};

$server = uv_tcp_init($loop);

$addr = uv_ip4_addr("0.0.0.0", DEFAULT_PORT);

uv_tcp_bind($server, $addr);
$r = uv_listen($server, DEFAULT_BACKLOG, $on_new_connection);
if ($r) {
    printf("Listen error %s\n", uv_strerror($r));
    return 1;
}

uv_run($loop, UV::RUN_DEFAULT);
```

The [C source](https://github.com/libuv/libuv/blob/v1.x/docs/code/tcp-echo-server/main.c)

```cpp
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <uv.h>

#define DEFAULT_PORT 7000
#define DEFAULT_BACKLOG 128

uv_loop_t *loop;
struct sockaddr_in addr;

typedef struct {
    uv_write_t req;
    uv_buf_t buf;
} write_req_t;

void free_write_req(uv_write_t *req) {
    write_req_t *wr = (write_req_t*) req;
    free(wr->buf.base);
    free(wr);
}

void alloc_buffer(uv_handle_t *handle, size_t suggested_size, uv_buf_t *buf) {
    buf->base = (char*) malloc(suggested_size);
    buf->len = suggested_size;
}

void echo_write(uv_write_t *req, int status) {
    if (status) {
        fprintf(stderr, "Write error %s\n", uv_strerror(status));
    }
    free_write_req(req);
}

void echo_read(uv_stream_t *client, ssize_t nread, const uv_buf_t *buf) {
    if (nread > 0) {
        write_req_t *req = (write_req_t*) malloc(sizeof(write_req_t));
        req->buf = uv_buf_init(buf->base, nread);
        uv_write((uv_write_t*) req, client, &req->buf, 1, echo_write);
        return;
    }
    if (nread < 0) {
        if (nread != UV_EOF)
            fprintf(stderr, "Read error %s\n", uv_err_name(nread));
        uv_close((uv_handle_t*) client, NULL);
    }

    free(buf->base);
}

void on_new_connection(uv_stream_t *server, int status) {
    if (status < 0) {
        fprintf(stderr, "New connection error %s\n", uv_strerror(status));
        // error!
        return;
    }

    uv_tcp_t *client = (uv_tcp_t*) malloc(sizeof(uv_tcp_t));
    uv_tcp_init(loop, client);
    if (uv_accept(server, (uv_stream_t*) client) == 0) {
        uv_read_start((uv_stream_t*) client, alloc_buffer, echo_read);
    }
    else {
        uv_close((uv_handle_t*) client, NULL);
    }
}

int main() {
    loop = uv_default_loop();

    uv_tcp_t server;
    uv_tcp_init(loop, &server);

    uv_ip4_addr("0.0.0.0", DEFAULT_PORT, &addr);

    uv_tcp_bind(&server, (const struct sockaddr*)&addr, 0);
    int r = uv_listen((uv_stream_t*) &server, DEFAULT_BACKLOG, on_new_connection);
    if (r) {
        fprintf(stderr, "Listen error %s\n", uv_strerror(r));
        return 1;
    }
    return uv_run(loop, UV_RUN_DEFAULT);
}
```

## Error handling

Initialization functions `*_init()` or synchronous functions, which may fail will return a negative number on error.
Async functions that may fail will pass a status parameter to their callbacks. The error messages are defined as `UV::E*` constants.

You can use the `uv_strerror(int)` and `uv_err_name(int)` functions to get a `string` describing the error or the error name respectively.

I/O read callbacks (such as for files and sockets) are passed a parameter `nread`. If `nread` is less than 0, there was an error (`UV::EOF` is the end of file error, which you may want to handle differently).

In general, functions and status parameters contain the actual error code, which is 0 for success, or a negative number in case of error.

## Documentation

All `functions/methods/classes` have there original **Libuv** _documentation_, _signatures_ embedded in DOC-BLOCKS.

For deeper usage understanding, see [An Introduction to libuv](https://codeahoy.com/learn/libuv/toc/).

The following functions are present in _Windows_, but not in _Linux_ **ubuntu 20.04** up, might need rechecking though.

```cpp
 void uv_library_shutdown(void);
 int uv_pipe(uv_file fds[2], int read_flags, int write_flags);
 int uv_socketpair(int type,
                            int protocol,
                            uv_os_sock_t socket_vector[2],
                            int flags0,
                            int flags1);
 int uv_try_write2(uv_stream_t* handle,
                            const uv_buf_t bufs[],
                            unsigned int nbufs,
                            uv_stream_t* send_handle);
 int uv_udp_using_recvmmsg(const uv_udp_t* handle);
 uint64_t uv_timer_get_due_in(const uv_timer_t* handle);
 unsigned int uv_available_parallelism(void);
 uint64_t uv_metrics_idle_time(uv_loop_t* loop);
 int uv_fs_get_system_error(const uv_fs_t*);
 int uv_fs_lutime(uv_loop_t* loop,
                           uv_fs_t* req,
                           const char* path,
                           double atime,
                           double mtime,
                           uv_fs_cb cb);
 int uv_ip_name(const struct sockaddr* src, char* dst, size_t size);

// ubuntu 18.04
char *uv_strerror_r(int err, char *buf, size_t buflen);
char *uv_err_name_r(int err, char *buf, size_t buflen);
uv_handle_type uv_handle_get_type(const uv_handle_t *handle);
const char *uv_handle_type_name(uv_handle_type type);
uv_handle_type uv_handle_get_type(const uv_handle_t *handle);
const char *uv_handle_type_name(uv_handle_type type);
void *uv_handle_get_data(const uv_handle_t *handle);
uv_loop_t *uv_handle_get_loop(const uv_handle_t *handle);
void uv_handle_set_data(uv_handle_t *handle, void *data);
void *uv_req_get_data(const uv_req_t *req);
void uv_req_set_data(uv_req_t *req, void *data);
uv_req_type uv_req_get_type(const uv_req_t *req);
const char *uv_req_type_name(uv_req_type type);
size_t uv_stream_get_write_queue_size(const uv_stream_t *stream);
int uv_tcp_close_reset(uv_tcp_t *handle, uv_close_cb close_cb);
int uv_udp_connect(uv_udp_t *handle, const struct sockaddr *addr);
int uv_udp_getpeername(const uv_udp_t *handle,
                       struct sockaddr *name,
                       int *namelen);
int uv_udp_set_source_membership(uv_udp_t *handle,
                                 const char *multicast_addr,
                                 const char *interface_addr,
                                 const char *source_addr,
                                 uv_membership membership);
size_t uv_udp_get_send_queue_size(const uv_udp_t *handle);
size_t uv_udp_get_send_queue_count(const uv_udp_t *handle);
void uv_tty_set_vterm_state(uv_tty_vtermstate_t state);
int uv_tty_get_vterm_state(uv_tty_vtermstate_t *state);
uv_pid_t uv_process_get_pid(const uv_process_t *);
int uv_open_osfhandle(uv_os_fd_t os_fd);
int uv_os_getpriority(uv_pid_t pid, int *priority);
int uv_os_setpriority(uv_pid_t pid, int priority);
int uv_os_environ(uv_env_item_t **envitems, int *count);
void uv_os_free_environ(uv_env_item_t *envitems, int count);
int uv_os_uname(uv_utsname_t *buffer);
uv_fs_type uv_fs_get_type(const uv_fs_t *);
ssize_t uv_fs_get_result(const uv_fs_t *);
void *uv_fs_get_ptr(const uv_fs_t *);
const char *uv_fs_get_path(const uv_fs_t *);
uv_stat_t *uv_fs_get_statbuf(uv_fs_t *);
int uv_fs_mkstemp(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *tpl,
                  uv_fs_cb cb);
int uv_fs_opendir(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *path,
                  uv_fs_cb cb);
int uv_fs_readdir(uv_loop_t *loop,
                  uv_fs_t *req,
                  uv_dir_t *dir,
                  uv_fs_cb cb);
int uv_fs_closedir(uv_loop_t *loop,
                   uv_fs_t *req,
                   uv_dir_t *dir,
                   uv_fs_cb cb);
int uv_fs_lchown(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 uv_uid_t uid,
                 uv_gid_t gid,
                 uv_fs_cb cb);
int uv_fs_statfs(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 uv_fs_cb cb);
int uv_random(uv_loop_t *loop,
              uv_random_t *req,
              void *buf,
              size_t buflen,
              unsigned flags,
              uv_random_cb cb);
uint64_t uv_get_constrained_memory(void);
void uv_sleep(unsigned int msec);
int uv_gettimeofday(uv_timeval64_t *tv);
int uv_thread_create_ex(uv_thread_t *tid,
                        const uv_thread_options_t *params,
                        uv_thread_cb entry,
                        void *arg);
void *uv_loop_get_data(const uv_loop_t *);
void uv_loop_set_data(uv_loop_t *, void *data);
```

## Contributing

Contributions are encouraged and welcome; I am always happy to get feedback or pull requests on Github :) Create [Github Issues](https://github.com/symplely/uv-ffi/issues) for bugs and new features and comment on the ones you are interested in.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
