--TEST--
Check for fs read and close
--SKIPIF--
<?php if (!extension_loaded("ffi") || (getenv('GITHUB_ACTIONS') !== false && \PHP_OS === 'Linux' && (float) \phpversion() < 8.0)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

define("FIXTURE_PATH", dirname(__FILE__) . "/fixtures/hello.data");

uv_fs_open(uv_default_loop(), FIXTURE_PATH, UV::O_RDONLY, 0, function($r) {
    uv_fs_read(uv_default_loop(), $r, 0, 32, function($stream, $data) {
        if (is_long($data)) {
            if ($data < 0) {
                throw new Exception("read error");
            }

            uv_fs_close(uv_default_loop(), $stream, function() { });
        } else {
            echo trim($data) . "\n";
        }
    });
});

// test offset
uv_fs_open(uv_default_loop(), FIXTURE_PATH, UV::O_RDONLY, 0, function($r) {
    uv_fs_read(uv_default_loop(), $r, 1, 32, function($stream, $data) {
        if (is_long($data)) {
            if ($data < 0) {
                throw new Exception("read error");
            }

            uv_fs_close(uv_default_loop(), $stream, function() { });
        } else {
            echo "H" . trim($data) . "\n";
        }
    });
});


uv_run();
--EXPECT--
Hello
Hello
