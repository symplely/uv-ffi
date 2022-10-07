--TEST--
Check for uv_hrtime
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

/* is this correct ?*/
$hrtime = uv_hrtime();
echo $hrtime;
--EXPECTF--
%d
