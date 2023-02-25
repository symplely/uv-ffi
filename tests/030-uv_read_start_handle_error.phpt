--TEST--
Test uv_read_start handle error
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();
$handler = uv_pipe_init($loop, 0);
var_dump(is_uv_stream($handler));
uv_pipe_open($handler, STDOUT);
uv_read_start($handler, function () {
});

--EXPECTF--
bool(true)

Notice: socket is not connected in %S

Notice: read failed in %S
