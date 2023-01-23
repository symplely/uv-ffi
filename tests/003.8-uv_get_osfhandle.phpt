--TEST--
Check for uv_get_osfhandle can get/open OS-dependent handle file descriptor
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$fd = \STDOUT;
$osf = \uv_get_osfhandle($fd);
var_dump(\IS_WINDOWS ? $osf instanceof \FFI\CData : \is_int($osf));
$fds = \uv_open_osfhandle($osf);
var_dump($fds);
--EXPECTF--
bool(true)
int(%d)
