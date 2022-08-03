--TEST--
Check uv_dlopen and uv_dlsym can show error on Windows
--SKIPIF--
<?php if (('\\' !== \DIRECTORY_SEPARATOR)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = \uv_loop_init();

$handler = \uv_dlopen("C:\\Windows\\System32\\msvcrt.dll");
$symbol = \uv_dlsym($handler, 'osfhandle');

\var_dump($symbol);
\var_dump(uv_dlerror($handler));

\uv_dlclose($handler);
\uv_run($loop);
\uv_loop_close($loop);

--EXPECTF--
int(-1)
string(45) "The specified procedure could not be found.
"
