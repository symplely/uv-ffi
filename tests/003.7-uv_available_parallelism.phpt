--TEST--
Check for uv_available_parallelism the hardware platform can use
--SKIPIF--
<?php if (('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();

var_dump(\uv_available_parallelism());
uv_run($loop, UV::RUN_DEFAULT);
uv_loop_close($loop);


--EXPECTF--
int(%d)
