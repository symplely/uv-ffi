--TEST--
Test uv_close handle closed
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();

$handler = uv_pipe_init($loop, 0);
uv_pipe_open($handler, STDOUT);

uv_write($handler, '', function ($handle) {
    uv_close($handle);
});

uv_close($handler);
uv_run($loop);

--EXPECTF--
Warning: passed UVPipe handle is already closed in %S
