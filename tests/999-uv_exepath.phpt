--TEST--
Check for uv_exepath
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$path = uv_exepath();

echo (int)preg_match("/php/", $path, $match);
--EXPECT--
1
