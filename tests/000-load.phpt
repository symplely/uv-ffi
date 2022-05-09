--TEST--
Check for php-uv presence
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

if (class_exists('uv'))
  echo "uv extension is available";
--EXPECT--
uv extension is available
