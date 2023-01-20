--TEST--
Check for php-uv presence
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

if (extension_loaded(ext_uv::get_name()))
  echo "uv extension is available";
--EXPECT--
uv extension is available
