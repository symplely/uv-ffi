--TEST--
Check uv_dlopen and uv_dlsym can be used on Windows
--SKIPIF--
<?php if (('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = \uv_loop_init();

$handler = \uv_dlopen("C:\\Windows\\System32\\msvcrt.dll");
$symbol = \uv_dlsym($handler, '_get_osfhandle');

\var_dump($symbol);

\uv_dlclose($handler);
\uv_run($loop);
\uv_loop_close($loop);

--EXPECTF--
object(FFI\CData:void**)#%d (1) {
  [0]=>
  object(FFI\CData:void*)#%d (1) {
    [0]=>
    int(%d)
  }
}
