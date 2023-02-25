--TEST--
Check for uv_tty_get_winsize and uv_loop_alive
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();

$tty = uv_tty_init($loop, STDOUT, 0);
uv_tty_get_winsize($tty, $width, $height);

uv_write($tty, "A\n", function ($handle) {
    uv_close($handle);
});

var_dump($width >= 0, $height >= 0, uv_loop_alive($loop));
uv_run($loop);
--EXPECTF--
A
bool(true)
bool(true)
bool(true)
