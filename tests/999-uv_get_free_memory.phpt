--TEST--
Check for uv_get_free_memory
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$free = uv_get_free_memory();

echo (int)is_int($free);
--EXPECT--
1
