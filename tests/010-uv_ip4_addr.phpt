--TEST--
Check for uv_ip4_addr
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$uv_address = uv_ip4_addr("0.0.0.0",0);
var_dump($uv_address instanceof \UVSockAddrIPv4);
var_dump(ffi_str_typeof($uv_address()));
--EXPECTF--
bool(true)
string(19) "struct sockaddr_in*"
