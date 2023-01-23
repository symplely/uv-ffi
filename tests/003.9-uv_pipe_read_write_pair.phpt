--TEST--
Check for uv_get_osfhandle can get/open OS-dependent handle file descriptor
--SKIPIF--
<?php if (('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_loop_init();

$handler = uv_pipe_init($loop, 0);
$handler2 = uv_pipe_init($loop, 0);

$fdd = \uv_pipe();
uv_pipe_open($handler, $fdd[1]);
uv_pipe_open($handler2, $fdd[0]);

var_dump(uv_is_readable($handler2));
\uv_read_start($handler2, function (object $stream, int $read, $data) {
    if ($read > 0)
        \printf($data);

    \uv_read_stop($stream);
    \uv_close($stream);
});

var_dump(uv_is_writable($handler));
uv_write($handler, 'A', function ($handle, int $status) {
    echo 'A';
});

var_dump($fdd);
uv_run($loop);
uv_close($handler);
uv_loop_close($loop);

--EXPECTF--
bool(true)
bool(true)
array(2) {
  [0]=>
  resource(%d) of type (uv_pipe)
  [1]=>
  resource(%d) of type (uv_pipe)
}
AA
