--TEST--
Check for fs_send_file
--SKIPIF--
<?php if (!extension_loaded("ffi") || (getenv('GITHUB_ACTIONS') !== false && \PHP_OS === 'Linux' && (float) \phpversion() < 8.0)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

define("FIXTURE_PATH", dirname(__FILE__) . "/fixtures/hello.data");

uv_fs_open(uv_default_loop(), FIXTURE_PATH, UV::O_RDONLY, 0, function($read_fd) {
    $std_err = STDOUT; // phpt doesn't catch stdout as uv_fs_sendfile uses stdout directly.
    uv_fs_sendfile(uv_default_loop(), $std_err, $read_fd, 0, 32, function($result) { });
});

uv_run();
--EXPECT--
Hello
