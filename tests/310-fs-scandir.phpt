--TEST--
Basic scandir functionality
--SKIPIF--
<?php if (extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

uv_fs_scandir(uv_default_loop(), ".", function($result) {
	var_dump(count($result) > 1);
});

uv_run();
?>
--EXPECT--
bool(true)
