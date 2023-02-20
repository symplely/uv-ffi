--TEST--
Check for uv_write has no memory leak
--SKIPIF--
<?php if (!extension_loaded("ffi")) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

uv_destruct_set();

class TestCase {
    public $counter = 0;

    public function run() {
        $loop = uv_loop_new();

        $handler = uv_pipe_init($loop, false);
        uv_pipe_open($handler, STDOUT);

        $a = 0;

        while (++$a <= 1000) {
            uv_write($handler, '', function() {
                $this->counter++;
            });
        }

        uv_close($handler);
        uv_run($loop, \UV::RUN_DEFAULT);
    }
}

$t = new TestCase;

$memory = memory_get_usage();

$t->run();

$memory = memory_get_usage() - $memory;

echo "$t->counter\n$memory\n";
--EXPECTF--
1000
%d
