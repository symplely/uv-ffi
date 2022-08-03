--TEST--
Check for uv_ip6_name
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$ip = uv_ip6_addr("::1",0);
echo uv_ip6_name($ip) . PHP_EOL;
--EXPECT--
::1
