--TEST--
Check for uv_prepare
--SKIPIF--
<?php if (extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_default_loop();
$prepare = uv_prepare_init($loop);

uv_prepare_start($prepare, function($rsc) {
    echo "Hello";
    uv_unref($rsc);
});

uv_run();
--EXPECT--
Hello
