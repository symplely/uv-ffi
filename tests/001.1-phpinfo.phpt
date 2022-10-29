--TEST--
Check for phpinfo as uv-ffi extension
--FILE--
<?php
require 'vendor/autoload.php';

$loop = \uv_loop_init();
ob_start();
phpinfo(8);
$value = ob_get_clean();

preg_match('/libuv Support => enabled/', $value, $matches);
var_dump($matches[0]);
var_dump(ext_uv::get_name());
var_dump(uv_g());
ext_uv::set_module(null);
uv_loop_close($loop);
--EXPECTF--
string(24) "libuv Support => enabled"
string(2) "uv"
object(FFI\CData:struct _zend_uv_globals)#%d (1) {
  ["default_loop"]=>
  NULL
}
