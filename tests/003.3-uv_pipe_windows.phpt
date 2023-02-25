--TEST--
Check for uv_pipe - Windows only
--SKIPIF--
<?php if (!extension_loaded("ffi") || ('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php

require 'vendor/autoload.php';

$fds = uv_pipe();
var_dump($fds);
remove_fd_resource(...$fds);

--EXPECTF--
array(2) {
  [0]=>
  resource(%d) of type (uv_pipe)
  [1]=>
  resource(%d) of type (uv_pipe)
}
