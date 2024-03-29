--TEST--
Check for fs rmdir
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

define("DIRECTORY_PATH", dirname(__FILE__) . "/fixtures/example_directory");

@rmdir(DIRECTORY_PATH);
mkdir(DIRECTORY_PATH, 0755);
uv_fs_rmdir(uv_default_loop(), DIRECTORY_PATH, function($result) {
    var_dump($result);
});

uv_run();

--EXPECTF--
int(0)
