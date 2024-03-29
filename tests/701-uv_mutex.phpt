--TEST--
Check for uv_rwlock
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$lock = uv_mutex_init();

if (uv_mutex_trylock($lock)) {
    echo "OK" . PHP_EOL;
} else {
    echo "FAILED" . PHP_EOL;
}

uv_mutex_unlock($lock);
if (uv_mutex_trylock($lock)) {
    echo "OK" . PHP_EOL;
} else {
    echo "FAILED" . PHP_EOL;
}

uv_mutex_unlock($lock);
--EXPECT--
OK
OK
