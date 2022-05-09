--TEST--
Check for uv_ip4_addr
--SKIPIF--
<?php if (extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

var_dump(uv_ip4_addr("0.0.0.0",0));
--EXPECTF--
object(UVSockAddrIPv4)#1 (0) {
}
