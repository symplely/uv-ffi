--TEST--
Check for uv_rwlock
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$lock = uv_rwlock_init();

if (uv_rwlock_trywrlock($lock)) {
    echo "OK" . PHP_EOL;
} else {
    echo "FAILED" . PHP_EOL;
}

uv_rwlock_wrunlock($lock);
if (uv_rwlock_trywrlock($lock)) {
    echo "OK" . PHP_EOL;
} else {
    echo "FAILED" . PHP_EOL;
}

uv_rwlock_wrunlock($lock);
--EXPECT--
OK
OK
