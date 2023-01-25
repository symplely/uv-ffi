--TEST--
Check for uv_pipe_open bad descriptor - STDOUT on Windows without emulation
--SKIPIF--
<?php if (('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php

require 'vendor/autoload.php';

$loop = uv_loop_init();

$handler = uv_pipe_init($loop, 0);
$status = uv_pipe_open($handler, STDOUT, false);
var_dump(uv_strerror($status));
uv_close($handler);
uv_loop_close($loop);

--EXPECTF--
string(%d) "socket operation on non-socket"
