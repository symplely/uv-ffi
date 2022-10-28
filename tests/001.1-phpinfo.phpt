--TEST--
Check for phpinfo
--FILE--
<?php
require 'vendor/autoload.php';

$loop = \uv_loop_init();
ob_start();
phpinfo(8);
$value = ob_get_clean();

preg_match('/libuv Support => enabled/', $value, $matches);
var_dump($matches[0]);
\ext_uv::set_module(null);
--EXPECTF--
string(24) "libuv Support => enabled"
