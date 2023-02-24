--TEST--
Check for phpinfo as uv-ffi extension
--FILE--
<?php

require 'vendor/autoload.php';

ob_start();
phpinfo(8);
$value = ob_get_clean();

preg_match('/libuv Support => enabled/', $value, $matches);
var_dump($matches[0]);
var_dump(ext_uv::get_name());
uv_loop_init();
var_dump(ext_uv::get_module()->get_default() instanceof \UVLoop);
var_dump(uv_g() instanceof \FFI\CData);
uv_run();
--EXPECTF--
string(%d) "libuv Support => enabled"
string(%d) "uv"
bool(true)
bool(true)
