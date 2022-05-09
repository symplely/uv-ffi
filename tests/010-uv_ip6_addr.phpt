--TEST--
Check for uv_ip6_addr
--SKIPIF--
<?php if (extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

var_dump(uv_ip6_addr("::0",0));
--EXPECTF--
object(UVSockAddrIPv6)#1 (0) {
}
