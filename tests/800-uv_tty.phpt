--TEST--
Check for uv_tty
--SKIPIF--
<?php if (!extension_loaded("ffi") || (getenv('GITHUB_ACTIONS') !== false && '\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

uv_fs_open(uv_default_loop(), SYS_CONSOLE, UV::O_RDONLY, 0, function($r) {
    $tty = uv_tty_init(uv_default_loop(), $r, 1);
    uv_tty_get_winsize($tty, $width, $height);
    if ($width >= 0) {
        echo "OK\n";
    }
    if ($height >= 0) {
        echo "OK\n";
    }
});

uv_run();
--EXPECT--
OK
OK
