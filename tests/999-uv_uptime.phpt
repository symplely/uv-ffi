--TEST--
Check for uv_uptime
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$uptime = uv_uptime();

echo (int)is_float($uptime);
--EXPECT--
1
