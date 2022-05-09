--TEST--
Check for uv_ip4_name
--SKIPIF--
<?php if (extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$ip = uv_ip4_addr("0.0.0.0",0);
$info = uv_ip4_name($ip);
echo $info . PHP_EOL;
--EXPECT--
0.0.0.0
