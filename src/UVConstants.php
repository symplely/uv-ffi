<?php

declare(strict_types=1);

if (!\defined('O_TEMPORARY')) {
    /**
     * temporary file bit (file is deleted when last handle is closed).
     */
    \define('O_TEMPORARY', 0x0040);
}

if (!\defined('O_TEXT')) {
    /**
     * file mode is text (translated).
     */
    \define('O_TEXT', 0x4000);
}

if (!\defined('O_NOINHERIT')) {
    /**
     * child process doesn't inherit file.
     */
    \define('O_NOINHERIT', 0x0080);
}

if (!\defined('O_SEQUENTIAL')) {
    /**
     * file access is primarily sequential.
     */
    \define('O_SEQUENTIAL', 0x0020);
}

if (!\defined('O_SYNC')) {
    /**
     * FILE_FLAG_WRITE_THROUGH
     */
    \define('O_SYNC', 0x08000000);
}

if (!\defined('O_RANDOM')) {
    /**
     * file access is primarily random.
     */
    \define('O_RANDOM', 0x0010);
}

if (!\defined('O_BINARY')) {
    /**
     * Open the file for binary access.
     */
    \define('O_BINARY', 0x8000);
}

if (!\defined('O_CLOEXEC')) {
    \define('O_CLOEXEC', 0x00100000);
}

if (!\defined('O_RDONLY')) {
    /**
     * Open the file for read-only access.
     */
    \define('O_RDONLY', \IS_WINDOWS ? 0x0000 : UV::O_RDONLY);
}

if (!\defined('O_WRONLY')) {
    /**
     * Open the file for write-only access.
     */
    \define('O_WRONLY', \IS_WINDOWS ? 0x0001 : UV::O_WRONLY);
}

if (!\defined('O_RDWR')) {
    /**
     * Open the file for read-write access.
     */
    \define('O_RDWR', \IS_WINDOWS ? 0x0002 : UV::O_RDWR);
}

if (!\defined('O_CREAT')) {
    /**
     * The file is created if it does not already exist.
     */
    \define('O_CREAT', \IS_WINDOWS ? 0x0100 : UV::O_CREAT);
}

if (!\defined('O_EXCL')) {
    /**
     * If the O_CREAT flag is set and the file already exists,
     * fail the open.
     */
    \define('O_EXCL', \IS_WINDOWS ? 0x0400 : UV::O_EXCL);
}

if (!\defined('O_TRUNC')) {
    /**
     * If the file exists and is a regular file, and the file is
     * opened successfully for write access, its length shall be truncated to zero.
     */
    \define('O_TRUNC', \IS_WINDOWS ? 0x0200 : UV::O_TRUNC);
}

if (!\defined('O_APPEND')) {
    /**
     * The file is opened in append mode. Before each write,
     * the file offset is positioned at the end of the file.
     */
    \define('O_APPEND', \IS_WINDOWS ? 0x0008 : UV::O_APPEND);
}

if (!\defined('O_NOCTTY') && !\IS_WINDOWS) {
    /**
     * If the path identifies a terminal device, opening the path will not cause that
     * terminal to become the controlling terminal for the process (if the process does
     * not already have one).
     *
     * - Note O_NOCTTY is not supported on Windows.
     */
    \define('O_NOCTTY', UV::O_NOCTTY);
}

if (!\defined('S_IRWXU')) {
    /**
     * read, write, execute/search by owner
     */
    \define('S_IRWXU', UV::S_IRWXU);
}

if (!\defined('S_IRUSR')) {
    /**
     * read permission, owner
     */
    \define('S_IRUSR', UV::S_IRUSR);
}

if (!\defined('S_IWUSR')) {
    /**
     * write permission, owner
     */
    \define('S_IWUSR', UV::S_IWUSR);
}

if (!\defined('S_IXUSR')) {
    /**
     * read, write, execute/search by group
     */
    \define('S_IXUSR', UV::S_IXUSR);
}

if (\IS_WINDOWS && !\defined('SIGBABY')) {
    /**
     * The SIGUSR1 signal is sent to a process to indicate user-defined conditions.
     */
    \define('SIGUSR1', 10);

    /**
     * The SIGUSR2 signa2 is sent to a process to indicate user-defined conditions.
     */
    \define('SIGUSR2', 12);

    /**
     * The SIGHUP signal is sent to a process when its controlling terminal is closed.
     */
    \define('SIGHUP', 1);

    /**
     * The SIGINT signal is sent to a process by its controlling terminal
     * when a user wishes to interrupt the process.
     */
    \define('SIGINT', 2);

    /**
     * The SIGQUIT signal is sent to a process by its controlling terminal
     * when the user requests that the process quit.
     */
    \define('SIGQUIT', 3);

    /**
     * The SIGILL signal is sent to a process when it attempts to execute an illegal,
     * malformed, unknown, or privileged instruction.
     */
    \define('SIGILL', 4);

    /**
     * The SIGTRAP signal is sent to a process when an exception (or trap) occurs.
     */
    \define('SIGTRAP', 5);

    /**
     * The SIGABRT signal is sent to a process to tell it to abort, i.e. to terminate.
     */
    \define('SIGABRT', 6);

    \define('SIGIOT', 6);

    /**
     * The SIGBUS signal is sent to a process when it causes a bus error.
     */
    \define('SIGBUS', 7);

    \define('SIGFPE', 8);

    /**
     * The SIGKILL signal is sent to a process to cause it to terminate immediately (kill).
     */
    \define('SIGKILL', 9);

    /**
     * The SIGSEGV signal is sent to a process when it makes an invalid virtual memory reference, or segmentation fault,
     */
    \define('SIGSEGV', 11);

    /**
     * The SIGPIPE signal is sent to a process when it attempts to write to a pipe without
     * a process connected to the other end.
     */
    \define('SIGPIPE', 13);

    /**
     * The SIGALRM, SIGVTALRM and SIGPROF signal is sent to a process when the time limit specified
     * in a call to a preceding alarm setting function (such as setitimer) elapses.
     */
    \define('SIGALRM', 14);

    /**
     * The SIGTERM signal is sent to a process to request its termination.
     * Unlike the SIGKILL signal, it can be caught and interpreted or ignored by the process.
     */
    \define('SIGTERM', 15);

    \define('SIGSTKFLT', 16);
    \define('SIGCLD', 17);

    /**
     * The SIGCHLD signal is sent to a process when a child process terminates, is interrupted,
     * or resumes after being interrupted.
     */
    \define('SIGCHLD', 17);

    /**
     * The SIGCONT signal instructs the operating system to continue (restart) a process previously paused by the
     * SIGSTOP or SIGTSTP signal.
     */
    \define('SIGCONT', 18);

    /**
     * The SIGSTOP signal instructs the operating system to stop a process for later resumption.
     */
    \define('SIGSTOP', 19);

    /**
     * The SIGTSTP signal is sent to a process by its controlling terminal to request it to stop (terminal stop).
     */
    \define('SIGTSTP', 20);

    /**
     * The SIGTTIN signal is sent to a process when it attempts to read in from the tty while in the background.
     */
    \define('SIGTTIN', 21);

    /**
     * The SIGTTOU signal is sent to a process when it attempts to write out from the tty while in the background.
     */
    \define('SIGTTOU', 22);

    /**
     * The SIGURG signal is sent to a process when a socket has urgent or out-of-band data available to read.
     */
    \define('SIGURG', 23);

    /**
     * The SIGXCPU signal is sent to a process when it has used up the CPU for a duration that exceeds a certain
     * predetermined user-settable value.
     */
    \define('SIGXCPU', 24);

    /**
     * The SIGXFSZ signal is sent to a process when it grows a file larger than the maximum allowed size
     */
    \define('SIGXFSZ', 25);

    /**
     * The SIGVTALRM signal is sent to a process when the time limit specified in a call to a preceding alarm setting
     * function (such as setitimer) elapses.
     */
    \define('SIGVTALRM', 26);

    /**
     * The SIGPROF signal is sent to a process when the time limit specified in a call to a preceding alarm setting
     * function (such as setitimer) elapses.
     */
    \define('SIGPROF', 27);

    /**
     * The SIGWINCH signal is sent to a process when its controlling terminal changes its size (a window change).
     */
    \define('SIGWINCH', 28);

    /**
     * The SIGPOLL signal is sent when an event occurred on an explicitly watched file descriptor.
     */
    \define('SIGPOLL', 29);

    \define('SIGIO', 29);

    /**
     * The SIGPWR signal is sent to a process when the system experiences a power failure.
     */
    \define('SIGPWR', 30);

    /**
     * The SIGSYS signal is sent to a process when it passes a bad argument to a system call.
     */
    \define('SIGSYS', 31);

    \define('SIGBABY', 31);
}
