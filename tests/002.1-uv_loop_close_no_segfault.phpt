--TEST--
No Segmentation fault after uv_loop_close
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_new();
uv_loop_close($loop);
--EXPECTF--
