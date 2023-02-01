--TEST--
Check for uv_ip6_addr
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$uv_address = uv_ip6_addr("::0", 0);
var_dump($uv_address instanceof \UVSockAddrIPv6);
var_dump(ffi_str_typeof($uv_address()));
--EXPECTF--
bool(true)
string(20) "struct sockaddr_in6*"
