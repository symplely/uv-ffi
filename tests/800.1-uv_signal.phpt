--TEST--
Test uv_signal
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$in  = uv_pipe_init(uv_default_loop(), ('/' == \DIRECTORY_SEPARATOR));
$out = uv_pipe_init(uv_default_loop(), ('/' == \DIRECTORY_SEPARATOR));

$signal1 = uv_signal_init();

uv_signal_start($signal1, function ($signal1) {
    echo PHP_EOL . 'The CTRL+C signal received, click the [X] to close the window.' . PHP_EOL;
    uv_signal_stop($signal1);
}, 2);

$signal2 = uv_signal_init();

uv_signal_start($signal2, function ($signal2) {
    echo PHP_EOL . 'The SIGHUP signal received, the OS will close this session window!' . PHP_EOL;
    uv_signal_stop($signal2);
}, 1);

echo "Hello, ";

$stdio = array();
$stdio[] = uv_stdio_new($in, UV::CREATE_PIPE | UV::READABLE_PIPE);
$stdio[] = uv_stdio_new($out, UV::CREATE_PIPE | UV::WRITABLE_PIPE);

$flags = 0;
$process = uv_spawn(
    uv_default_loop(),
    "php",
    array('-r', 'echo "World!" . PHP_EOL; sleep(100);'),
    $stdio,
    __DIR__,
    [],
    function ($process, $stat, $signal) use ($signal1, $signal2) {
        if ($signal == 9) {
            echo "The process was terminated with 'SIGKILL' or '9' signal!" . PHP_EOL;
        }

        uv_close($process, function () {
        });
        uv_signal_stop($signal1);
        uv_signal_stop($signal2);
    },
    $flags
);

uv_read_start($out, function ($out, $nread, $buffer) use ($process) {
    echo $buffer;

    uv_close($out, function () {
    });
    uv_process_kill($process, 9);
    $pid = uv_process_get_pid($process);
    print (uv_strerror(uv_kill($pid, -1))) . EOL;
});

uv_run();
--EXPECTF--
Hello, World!
invalid argument
The process was terminated with 'SIGKILL' or '9' signal!
