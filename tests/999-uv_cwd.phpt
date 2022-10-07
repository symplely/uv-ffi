--TEST--
Check for uv_cwd
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$cwd = uv_cwd();

$expected = getcwd();

if ($cwd == $expected) {
  echo "OK" . PHP_EOL;
} else {
  echo "FAILED: expected {$expected}, but {$cwd}" . PHP_EOL;

}
--EXPECT--
OK
