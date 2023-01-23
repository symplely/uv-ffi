--TEST--
Check for uv_buf_init constructor
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php

require 'vendor/autoload.php';

$data = 'hello';
$result = \uv_buf_init($data);
var_dump($result instanceof \UVBuffer);
var_dump($result->getString());

--EXPECTF--
bool(true)
string(5) "hello"
