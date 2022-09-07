--TEST--
uv_fs_readlink() segfaults if file not a link
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$uv = uv_loop_new();

$result = uv_fs_readlink($uv, __FILE__, function ($result) {
    var_dump($result < 0);
});

var_dump($result);
uv_run($uv);

?>
--EXPECT--
int(0)
bool(true)
