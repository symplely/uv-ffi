--TEST--
Check for uv_async
--SKIPIF--
<?php if (extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_default_loop();
$async = uv_async_init($loop, function($async) {
    echo "Hello";
    uv_close($async);
});

uv_async_send($async);

uv_run();
?>
--EXPECT--
Hello
