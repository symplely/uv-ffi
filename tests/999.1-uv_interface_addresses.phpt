--TEST--
Check for uv_interface_addresses
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$addresses = uv_interface_addresses();

$info = array_shift($addresses);

if (!isset($info["name"])) {
  echo "FAILED: key `name` does not exist" . PHP_EOL;
}
if (!isset($info["is_internal"])) {
  echo "FAILED: key `is_internal` does not exist" . PHP_EOL;
}
if (!isset($info["address"])) {
  echo "FAILED: key `address` does not exist" . PHP_EOL;
}

echo 'ok';
--EXPECTF--
ok
