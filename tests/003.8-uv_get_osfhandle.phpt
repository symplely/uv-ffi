--TEST--
Check for uv_get_osfhandle can get/open OS-dependent handle file descriptor
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();
$fd = \STDOUT;
$osf = \uv_get_osfhandle($fd);
var_dump($osf instanceof \FFI\Cdata);
$fds = \uv_open_osfhandle($osf);
var_dump($fds);
var_dump($fd);
uv_loop_close($loop);

--EXPECTF--
bool(true)
int(%d)
resource(%d) of type (stream)
