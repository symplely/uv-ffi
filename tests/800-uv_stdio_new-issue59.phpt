--TEST--
Test uv_stdio_new doesn't cause segfault #56
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$ioRead = uv_stdio_new("foo", UV::CREATE_PIPE | UV::INHERIT_STREAM);

--EXPECTF--
PHP Warning:  passed unexpected value, expected instance of UV, file resource or socket object in %S
