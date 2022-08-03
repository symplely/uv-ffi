--TEST--
Check for uv_buf_init constructor
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php

require 'vendor/autoload.php';

$loop = uv_loop_init();
$data = 'hello';
$result = \uv_buf_init($data);
var_dump($result instanceof \UVBuffer);
var_dump($result->getString());
uv_loop_close($loop);


--EXPECTF--
bool(true)
string(5) "hello"
