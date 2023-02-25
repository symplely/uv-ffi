--TEST--
Test uv_read_start handle not initialized
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();
$handler = uv_pipe_init($loop, 0);
UVStream::read($handler, function () {
});

--EXPECTF--
Warning: passed UV handle is not initialized yet in %S
