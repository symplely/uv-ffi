--TEST--
Check to make sure uv_loop_new can be used
--SKIPIF--
<?php if(!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_new();
$async = uv_async_init($loop, function($async) {
    echo "Hello";
    uv_close($async);
});
uv_async_send($async);
uv_run($loop);
--EXPECT--
Hello
