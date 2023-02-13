--TEST--
Check for fs error
--SKIPIF--
<?php if (!extension_loaded("ffi") || ('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

UVFs::init(0, 0);

--EXPECTF--
Warning: uv_fs_custom failed: no such file or directory in %S
