--TEST--
Check for uv_now
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$time = uv_now();
usleep(1000);
uv_update_time();
$timeNow = uv_now();
var_dump($timeNow > $time);
--EXPECTF--
bool(true)
