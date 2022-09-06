<?php

declare(strict_types=1);

/**
 * Base handle type for `libuv` handles.
 * All handle types (including stream types) subclass
 * - UVTcp,
 * - UVUdp,
 * - UVPipe,
 * - ...etc
 *
 * All API functions defined here work with any handle type.
 * `Libuv` handles are not movable. Pointers to handle structures passed
 * to functions must remain valid for the duration of the requested operation.
 * Take care when using stack allocated handles.
 *
 * This is a full-featured event loop backed by epoll, kqueue, IOCP, event ports.
 * - Asynchronous TCP and UDP sockets
 * - Asynchronous DNS resolution
 * - Asynchronous file and file system operations
 * - File system events
 * - ANSI escape code controlled TTY
 * - IPC with socket sharing, using Unix domain sockets or named pipes (Windows)
 * - Child processes
 * - Thread pool
 * - Signal handling
 * - High resolution clock
 * - Threading and synchronization primitives
 *
 * @return uv_handle_t **pointer** by invoking `$UV()`
 * @see https://libuv.org/
 */
class UV extends UVHandler
{
    const INET_ADDRSTRLEN = 22;
    const INET6_ADDRSTRLEN = 65;

    /**
     * This flag indicates an event that becomes active when the provided file
     * descriptor(usually a stream resource, or socket) is ready for reading.
     *
     * - Also a flag for `uv_pipe_chmod` function.
     */
    const READABLE = 1;

    /**
     * This flag indicates an event that becomes active when the provided file
     * descriptor(usually a stream resource, or socket) is ready for reading.
     *
     * - Also a flag for `uv_pipe_chmod` function.
     */
    const WRITABLE = 2;

    /**
     * A `uv_poll_event` flag.
     */
    const DISCONNECT = 4;

    /**
     * A `uv_poll_event` flag.
     */
    const PRIORITIZED = 8;

    /**
     * Runs the event loop until there are no more active and referenced
     * handles or requests.
     * Mode used to run the loop with.
     */
    const RUN_DEFAULT = 0;

    /**
     * Poll for i/o once. Note that this function blocks
     * if there are no pending callbacks.
     * Mode used to run the loop with.
     */
    const RUN_ONCE = 1;

    /**
     * Poll for i/o once but don’t block if there are no pending callbacks.
     * Mode used to run the loop with.
     */
    const RUN_NOWAIT = 2;

    /**
     * FS Event monitor type
     */
    const CHANGE = 1;

    /**
     * FS Event monitor type
     */
    const RENAME = 2;

    /**
     * Open the file for read-only access.
     */
    const O_RDONLY = \IS_WINDOWS ? 0x0000 : 1;

    /**
     * Open the file for write-only access.
     */
    const O_WRONLY = \IS_WINDOWS ? 0x0001 : 2;

    /**
     * Open the file for read-write access.
     */
    const O_RDWR = \IS_WINDOWS ? 0x0002 : 3;

    /**
     * The file is created if it does not already exist.
     */
    const O_CREAT = \IS_WINDOWS ? 0x0100 : 4;

    /**
     * If the O_CREAT flag is set and the file already exists,
     * fail the open.
     */
    const O_EXCL = \IS_WINDOWS ? 0x0400 : 5;

    /**
     * If the file exists and is a regular file, and the file is
     * opened successfully for write access, its length shall be truncated to zero.
     */
    const O_TRUNC = \IS_WINDOWS ? 0x0200 : 6;

    /**
     * The file is opened in append mode. Before each write,
     * the file offset is positioned at the end of the file.
     */
    const O_APPEND = \IS_WINDOWS ? 0x0008 : 7;

    /**
     * If the path identifies a terminal device, opening the path will not cause that
     * terminal to become the controlling terminal for the process (if the process does
     * not already have one).
     *
     * - Note O_NOCTTY is not supported on Windows.
     */
    const O_NOCTTY = 8;

    const O_CLOEXEC = 0x00100000;

    /**
     * read, write, execute/search by owner
     */
    const S_IRWXU = 00700;

    /**
     * read permission, owner
     */
    const S_IRUSR = 00400;

    /**
     * write permission, owner
     */
    const S_IWUSR = 00200;

    /**
     * execute/search permission, owner
     */
    const S_IXUSR = 00100;

    /**
     * read, write, execute/search by group
     */
    const S_IRWXG = 00070;

    /**
     * read permission, group
     */
    const S_IRGRP = 00040;

    /**
     * write permission, group
     */
    const S_IWGRP = 00020;

    /**
     * execute/search permission, group
     */
    const S_IXGRP = 00010;

    /**
     * read, write, execute/search by others
     */
    const S_IRWXO = 00007;

    /**
     * read permission, others
     */
    const S_IROTH = 00004;

    /**
     * write permission, others
     */
    const S_IWOTH = 00002;

    /**
     * execute/search permission, others
     */
    const S_IXOTH = 00001;

    /**
     * bit mask type of file
     */
    const S_IFMT = 0170000;

    /**
     * block special file type
     */
    const S_IFBLK = 0060000;

    /**
     * character special file type
     */
    const S_IFCHR = 0020000;

    /**
     * FIFO special file type
     */
    const S_IFIFO = 0010000;

    /**
     * regular file type
     */
    const S_IFREG = 0100000;

    /**
     * directory file type
     */
    const S_IFDIR = 0040000;

    /**
     * symbolic link file type
     */
    const S_IFLNK = 0120000;

    /**
     * socket file type
     */
    const S_IFSOCK = 0140000;

    const AF_INET = \AF_INET;
    const AF_INET6 = \AF_INET6;
    const AF_UNIX = \AF_UNIX;
    const AF_UNSPEC = 0;

    /** dummy for IP */
    const IPPROTO_IP = 0;

    /** control message protocol */
    const IPPROTO_ICMP = 1;

    /** tcp */
    const IPPROTO_TCP = 6;

    /** user datagram protocol */
    const IPPROTO_UDP = 17;
    const IPPROTO_IPV6 = 41;
    const IPPROTO_RAW = 255;
    const IPPROTO_RSVP = 46;

    const LEAVE_GROUP = 1;
    const JOIN_GROUP = 2;

    /**
     * Flags specifying how a stdio should be transmitted to the child process.
     */
    const IGNORE = 0x00;

    /**
     * Flags specifying how a stdio should be transmitted to the child process.
     */
    const CREATE_PIPE = 0x01;

    /**
     * Flags specifying how a stdio should be transmitted to the child process.
     */
    const INHERIT_FD = 0x02;

    /**
     * Flags specifying how a stdio should be transmitted to the child process.
     */
    const INHERIT_STREAM = 0x04;

    /**
     * When `UV::CREATE_PIPE` is specified, `UV::READABLE_PIPE` and `UV::WRITABLE_PIPE`
     * determine the direction of flow, from the child process' perspective. Both
     * flags may be specified to create a duplex data stream.
     */
    const READABLE_PIPE = 0x10;
    const WRITABLE_PIPE = 0x20;

    const NONBLOCK_PIPE = 0x40;

    /**
     * Open the child pipe handle in overlapped mode on Windows.
     * On Unix it is silently ignored.
     */
    const OVERLAPPED_PIPE = 0x40;

    /**
     *  Disables dual stack mode.
     */
    const UDP_IPV6ONLY = 1;

    /**
     * Indicates message was truncated because read buffer was too small. The
     * remainder was discarded by the OS. Used in uv_udp_recv_cb.
     */
    const UDP_PARTIAL = 2;

    /**
     * Set the child process' user id.
     */
    const PROCESS_SETUID = (1 << 0);

    /**
     * Set the child process' group id.
     */
    const PROCESS_SETGID = (1 << 1);

    /**
     * Do not wrap any arguments in quotes, or perform any other escaping, when
     * converting the argument list into a command line string. This option is
     * only meaningful on Windows systems. On Unix it is silently ignored.
     */
    const PROCESS_WINDOWS_VERBATIM_ARGUMENTS = (1 << 2);

    /**
     * Spawn the child process in a detached state - this will make it a process
     * group leader, and will effectively enable the child to keep running after
     * the parent exits. Note that the child process will still keep the
     * parent's event loop alive unless the parent process calls uv_unref() on
     * the child's process handle.
     */
    const PROCESS_DETACHED = (1 << 3);

    /**
     * Hide the subprocess window that would normally be created. This option is
     * only meaningful on Windows systems. On Unix it is silently ignored.
     */
    const PROCESS_WINDOWS_HIDE = (1 << 4);

    /**
     * Hide the subprocess console window that would normally be created. This
     * option is only meaningful on Windows systems. On Unix it is silently
     * ignored.
     */
    const PROCESS_WINDOWS_HIDE_CONSOLE = (1 << 5);

    /**
     * Hide the subprocess GUI window that would normally be created. This
     * option is only meaningful on Windows systems. On Unix it is silently
     * ignored.
     */
    const PROCESS_WINDOWS_HIDE_GUI = (1 << 6);

    /**
     * Initial/normal terminal mode
     */
    const TTY_MODE_NORMAL = 0;

    /**
     * Raw input mode (On Windows, ENABLE_WINDOW_INPUT is also enabled)
     */
    const TTY_MODE_RAW = 1;

    /**
     * Binary-safe I/O mode for IPC (Unix-only)
     */
    const TTY_MODE_IO = 2;

    /**
     * The SIGHUP signal is sent to a process when its controlling terminal is closed. It was originally designed to
     * notify the process of a serial line drop (a hangup). In modern systems, this signal usually means that the
     * controlling pseudo or virtual terminal has been closed. Many daemons will reload their configuration files and
     * reopen their logfiles instead of exiting when receiving this signal. nohup is a command to make a command ignore
     * the signal.
     */
    const SIGHUP = 1;

    /**
     * The SIGINT signal is sent to a process by its controlling terminal when a user wishes to interrupt the process.
     * This is typically initiated by pressing Ctrl-C, but on some systems, the "delete" character or "break" key can be
     * used.
     */
    const SIGINT = 2;

    /**
     * The SIGQUIT signal is sent to a process by its controlling terminal when the user requests that the process quit
     * and perform a core dump.
     */
    const SIGQUIT = 3;

    /**
     * The SIGILL signal is sent to a process when it attempts to execute an illegal, malformed, unknown, or privileged
     * instruction.
     */
    const SIGILL = 4;

    /**
     * The SIGTRAP signal is sent to a process when an exception (or trap) occurs: a condition that a debugger has
     * requested to be informed of — for example, when a particular function is executed, or when a particular variable
     * changes value.
     */
    const SIGTRAP = 5;

    /**
     * The SIGABRT signal is sent to a process to tell it to abort, i.e. to terminate. The signal is usually initiated
     * by the process itself when it calls abort function of the C Standard Library, but it can be sent to the process
     * from outside like any other signal.
     */
    const SIGABRT = 6;

    const SIGIOT = 6;

    /**
     * The SIGBUS signal is sent to a process when it causes a bus error. The conditions that lead to the signal being
     * sent are, for example, incorrect memory access alignment or non-existent physical address.
     */
    const SIGBUS = 7;

    const SIGFPE = 8;

    /**
     * The SIGKILL signal is sent to a process to cause it to terminate immediately (kill). In contrast to SIGTERM and
     * SIGINT, this signal cannot be caught or ignored, and the receiving process cannot perform any clean-up upon
     * receiving this signal.
     */
    const SIGKILL = 9;

    /**
     * The SIGUSR1 signal is sent to a process to indicate user-defined conditions.
     */
    const SIGUSR1 = 10;

    /**
     * The SIGUSR1 signa2 is sent to a process to indicate user-defined conditions.
     */
    const SIGUSR2 = 12;

    /**
     * The SIGSEGV signal is sent to a process when it makes an invalid virtual memory reference, or segmentation fault,
     * i.e. when it performs a segmentation violation.
     */
    const SIGSEGV = 11;

    /**
     * The SIGPIPE signal is sent to a process when it attempts to write to a pipe without a process connected to the
     * other end.
     */
    const SIGPIPE = 13;

    /**
     * The SIGALRM, SIGVTALRM and SIGPROF signal is sent to a process when the time limit specified in a call to a
     * preceding alarm setting function (such as setitimer) elapses. SIGALRM is sent when real or clock time elapses.
     * SIGVTALRM is sent when CPU time used by the process elapses. SIGPROF is sent when CPU time used by the process
     * and by the system on behalf of the process elapses.
     */
    const SIGALRM = 14;

    /**
     * The SIGTERM signal is sent to a process to request its termination. Unlike the SIGKILL signal, it can be caught
     * and interpreted or ignored by the process. This allows the process to perform nice termination releasing
     * resources and saving state if appropriate. SIGINT is nearly identical to SIGTERM.
     */
    const SIGTERM = 15;

    const SIGSTKFLT = 16;
    const SIGCLD = 17;

    /**
     * The SIGCHLD signal is sent to a process when a child process terminates, is interrupted, or resumes after being
     * interrupted. One common usage of the signal is to instruct the operating system to clean up the resources used by
     * a child process after its termination without an explicit call to the wait system call.
     */
    const SIGCHLD = 17;

    /**
     * The SIGCONT signal instructs the operating system to continue (restart) a process previously paused by the
     * SIGSTOP or SIGTSTP signal. One important use of this signal is in job control in the Unix shell.
     */
    const SIGCONT = 18;

    /**
     * The SIGSTOP signal instructs the operating system to stop a process for later resumption.
     */
    const SIGSTOP = 19;

    /**
     * The SIGTSTP signal is sent to a process by its controlling terminal to request it to stop (terminal stop). It is
     * commonly initiated by the user pressing Ctrl+Z. Unlike SIGSTOP, the process can register a signal handler for or
     * ignore the signal.
     */
    const SIGTSTP = 20;

    /**
     * The SIGTTIN signal is sent to a process when it attempts to read in from the tty while in the background.
     * Typically, this signal is received only by processes under job control; daemons do not have controlling
     */
    const SIGTTIN = 21;

    /**
     * The SIGTTOU signal is sent to a process when it attempts to write out from the tty while in the background.
     * Typically, this signal is received only by processes under job control; daemons do not have controlling
     */
    const SIGTTOU = 22;

    /**
     * The SIGURG signal is sent to a process when a socket has urgent or out-of-band data available to read.
     */
    const SIGURG = 23;

    /**
     * The SIGXCPU signal is sent to a process when it has used up the CPU for a duration that exceeds a certain
     * predetermined user-settable value. The arrival of a SIGXCPU signal provides the receiving process a chance to
     * quickly save any intermediate results and to exit gracefully, before it is terminated by the operating system
     * using the SIGKILL signal.
     */
    const SIGXCPU = 24;

    /**
     * The SIGXFSZ signal is sent to a process when it grows a file larger than the maximum allowed size
     */
    const SIGXFSZ = 25;

    /**
     * The SIGVTALRM signal is sent to a process when the time limit specified in a call to a preceding alarm setting
     * function (such as setitimer) elapses. SIGVTALRM is sent when CPU time used by the process elapses.
     */
    const SIGVTALRM = 26;

    /**
     * The SIGPROF signal is sent to a process when the time limit specified in a call to a preceding alarm setting
     * function (such as setitimer) elapses. SIGPROF is sent when CPU time used by the process and by the system on
     * behalf of the process elapses.
     */
    const SIGPROF = 27;

    /**
     * The SIGWINCH signal is sent to a process when its controlling terminal changes its size (a window change).
     */
    const SIGWINCH = 28;

    /**
     * The SIGPOLL signal is sent when an event occurred on an explicitly watched file descriptor. Using it effectively
     * leads to making asynchronous I/O requests since the kernel will poll the descriptor in place of the caller. It
     * provides an alternative to active polling.
     */
    const SIGPOLL = 29;

    const SIGIO = 29;

    /**
     * The SIGPWR signal is sent to a process when the system experiences a power failure.
     */
    const SIGPWR = 30;

    /**
     * The SIGSYS signal is sent to a process when it passes a bad argument to a system call. In practice, this kind of
     * signal is rarely encountered since applications rely on libraries (e.g. libc) to make the call for them.
     */
    const SIGSYS = 31;

    const SIGBABY = 31;

    /*
    * By default, if the fs event watcher is given a directory name, we will
    * watch for all events in that directory. This flags overrides this behavior
    * and makes fs_event report only changes to the directory entry itself. This
    * flag does not affect individual files watched.
    * This flag is currently not implemented yet on any backend.
    */
    const FS_EVENT_WATCH_ENTRY = 1;

    /*
    * By default uv_fs_event will try to use a kernel interface such as inotify
    * or kqueue to detect events. This may not work on remote file systems such
    * as NFS mounts. This flag makes fs_event fall back to calling stat() on a
    * regular interval.
    * This flag is currently not implemented yet on any backend.
    */
    const FS_EVENT_STAT = 2;

    /*
    * By default, event watcher, when watching directory, is not registering
    * (is ignoring) changes in its subdirectories.
    * This flag will override this behavior on platforms that support it.
    */
    const FS_EVENT_RECURSIVE = 4;

    // Base handle
    const UNKNOWN_HANDLE = 0;
    const ASYNC = 1;
    const CHECK = 2;
    const FS_EVENT = 3;
    const FS_POLL = 4;
    const HANDLE = 5;
    const IDLE = 6;
    const NAMED_PIPE = 7;
    const POLL = 8;
    const PREPARE = 9;
    const PROCESS = 10;
    const STREAM = 11;
    const TCP = 12;
    const TIMER = 13;
    const TTY = 14;
    const UDP = 15;
    const SIGNAL = 16;
    const FILE = 17;
    const HANDLE_TYPE_MAX = 18;

    // Base request
    const UNKNOWN_REQ = 0;
    const REQ = 1;
    const CONNECT = 2;
    const WRITE = 3;
    const SHUTDOWN = 4;
    const UDP_SEND = 5;
    const FS = 6;
    const WORK = 7;
    const GETADDRINFO = 8;
    const GETNAMEINFO = 9;
    const REQ_TYPE_MAX = 10;

    const E2BIG = (- (7));
    const EACCES = (- (13));
    const EADDRINUSE = (- (98));
    const EADDRNOTAVAIL = (- (99));
    const EAFNOSUPPORT = (- (97));
    const EAGAIN = (- (11));
    const EAI_ADDRFAMILY = (-3000);
    const EAI_AGAIN = (-3001);
    const EAI_BADFLAGS = (-3002);
    const EAI_BADHINTS = (-3013);
    const EAI_CANCELED = (-3003);
    const EAI_FAIL = (-3004);
    const EAI_FAMILY = (-3005);
    const EAI_MEMORY = (-3006);
    const EAI_NODATA = (-3007);
    const EAI_NONAME = (-3008);
    const EAI_OVERFLOW = (-3009);
    const EAI_PROTOCOL = (-3014);
    const EAI_SERVICE = (-3010);
    const EAI_SOCKTYPE = (-3011);
    const EALREADY = (- (114));
    const EBADF = (- (9));
    const EBUSY = (- (16));
    const ECANCELED = (- (125));
    const ECHARSET = (-4080);
    const ECONNABORTED = (- (103));
    const ECONNREFUSED = (- (111));
    const ECONNRESET = (- (104));
    const EDESTADDRREQ = (- (89));
    const EEXIST = (- (17));
    const EFAULT = (- (14));
    const EFBIG = (- (27));
    const EHOSTUNREACH = (- (113));
    const EINTR = (- (4));
    const EINVAL = (- (22));
    const EIO = (- (5));
    const EISCONN = (- (106));
    const EISDIR = (- (21));
    const ELOOP = (- (40));
    const EMFILE = (- (24));
    const EMSGSIZE = (- (90));
    const ENAMETOOLONG = (- (36));
    const ENETDOWN = (- (100));
    const ENETUNREACH = (- (101));
    const ENFILE = (- (23));
    const ENOBUFS = (- (105));
    const ENODEV = (- (19));
    const ENOENT = (- (2));
    const ENOMEM = (- (12));
    const ENONET = (- (64));
    const ENOPROTOOPT = (- (92));
    const ENOSPC = (- (28));
    const ENOSYS = (- (38));
    const ENOTCONN = (- (107));
    const ENOTDIR = (- (20));
    const ENOTEMPTY = (- (39));
    const ENOTSOCK = (- (88));
    const ENOTSUP = (- (95));
    const EOVERFLOW = (- (75));
    const EPERM = (- (1));
    const EPIPE = (- (32));
    const EPROTO = (- (71));
    const EPROTONOSUPPORT = (- (93));
    const EPROTOTYPE = (- (91));
    const ERANGE = (- (34));
    const EROFS = (- (30));
    const ESHUTDOWN = (- (108));
    const ESPIPE = (- (29));
    const ESRCH = (- (3));
    const ETIMEDOUT = (- (110));
    const ETXTBSY = (- (26));
    const EXDEV = (- (18));
    const UNKNOWN = (-4094);
    const EOF = (-4095);
    const ENXIO = (- (6));
    const EMLINK = (- (31));
    const EHOSTDOWN = (- (112));
    const EREMOTEIO = (- (121));
    const ENOTTY = (- (25));
    const EFTYPE = (-4028);
    const EILSEQ = (- (84));
    const ESOCKTNOSUPPORT = (- (94));
    const ERRNO_MAX = (-4095) - 1;

    // File system request type
    const FS_UNKNOWN = -1;
    const FS_CUSTOM = 0;
    const FS_OPEN = 1;
    const FS_CLOSE = 2;
    const FS_READ = 3;
    const FS_WRITE = 4;
    const FS_SENDFILE = 5;
    const FS_STAT = 6;
    const FS_LSTAT = 7;
    const FS_FSTAT = 8;
    const FS_FTRUNCATE = 9;
    const FS_UTIME = 10;
    const FS_FUTIME = 11;
    const FS_ACCESS = 12;
    const FS_CHMOD = 13;
    const FS_FCHMOD = 14;
    const FS_FSYNC = 15;
    const FS_FDATASYNC = 16;
    const FS_UNLINK = 17;
    const FS_RMDIR = 18;
    const FS_MKDIR = 19;
    const FS_MKDTEMP = 20;
    const FS_RENAME = 21;
    const FS_SCANDIR = 22;
    const FS_LINK = 23;
    const FS_SYMLINK = 24;
    const FS_READLINK = 25;
    const FS_CHOWN = 26;
    const FS_FCHOWN = 27;
    const FS_REALPATH = 28;
    const FS_COPYFILE = 29;
    const FS_LCHOWN = 30;
    const FS_OPENDIR = 31;
    const FS_READDIR = 32;
    const FS_CLOSEDIR = 33;
    const FS_STATFS = 34;
    const FS_MKSTEMP = 35;
    const FS_LUTIME = 36;

    private static array $constant_names = [];
    /**
     * Returns the type name of code
     *
     * @param int $valueCode Integer value of type
     */
    public static function name(int $valueCode): string
    {
        if (empty(self::$constant_names)) {
            static::$constant_names = \array_flip((new \ReflectionClass(static::class))->getConstants());
        }

        // We should use only low byte to get the name of constant
        $valueCode &= 0xFF;
        if (!isset(static::$constant_names[$valueCode])) {
            return \ze_ffi()->zend_error(\E_WARNING, 'Unknown code %s', $valueCode);
        }

        return static::$constant_names[$valueCode];
    }
}
