--TEST--
Test uv_close handle wrong
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();
\UV::close($loop);

--EXPECTF--
Warning: passed UV handle (struct uv_loop_s*) is not closeable in %S
