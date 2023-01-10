--TEST--
Check for uv_write multiple call with different callbacks
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();

$handler = uv_pipe_init($loop, 0);
uv_pipe_open($handler, STDOUT);

uv_write($handler, 'A', function () { echo 'A'; });
uv_write($handler, 'B', function () { echo 'B'; });
uv_write($handler, 'C', function ($handler) {
    echo 'C';
    uv_close($handler);
});

var_dump(uv_handle_get_type($handler));
var_dump(uv_is_writable($handler));
uv_run($loop);
uv_loop_close($loop);
--EXPECTF--
int(7)
bool(true)
ABCABC
