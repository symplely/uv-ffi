--TEST--
Test uv_spawn environment
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$in  = uv_pipe_init(uv_default_loop(), ('/' == \DIRECTORY_SEPARATOR));
$out = uv_pipe_init(uv_default_loop(), ('/' == \DIRECTORY_SEPARATOR));

echo "HELLO ";

$stdio = array();
$stdio[] = uv_stdio_new($in, UV::CREATE_PIPE | UV::READABLE_PIPE);
$stdio[] = uv_stdio_new($out, UV::CREATE_PIPE | UV::WRITABLE_PIPE);

uv_spawn(
    uv_default_loop(),
    PHP_BINARY,
    array('-r', "var_dump(getenv('KEY'));"),
    $stdio,
    __DIR__,
    array("KEY" => "hello"),
    function ($process, $stat, $signal) {
        echo 'done';
        uv_close($process, function () {
        });
    }
);

uv_read_start($out, function ($out, $nRead, $buffer) {
    echo $buffer;

    uv_close($out, function () {
    });
});

uv_run();
--EXPECTF--
HELLO string(5) "hello"
done
