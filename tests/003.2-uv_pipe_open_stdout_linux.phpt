--TEST--
Check for uv_pipe_open good file descriptor - STDOUT on Linux
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php

require 'vendor/autoload.php';

$loop = uv_loop_init();

$handler = uv_pipe_init($loop, 0);
$status = uv_pipe_open($handler, STDOUT);
var_dump($status);
uv_close($handler);
uv_loop_close($loop);

--EXPECTF--
int(0)
