--TEST--
Check for phpinfo as uv-ffi extension
--FILE--
<?php

use FFI\CData;

require 'vendor/autoload.php';

$loop = \uv_loop_init();
ob_start();
phpinfo(8);
$value = ob_get_clean();

preg_match('/libuv Support => enabled/', $value, $matches);
var_dump($matches[0]);
var_dump(ext_uv::get_name());
var_dump(uv_g() instanceof CData);
--EXPECTF--
string(%d) "libuv Support => enabled"
string(%d) "uv"
bool(true)
