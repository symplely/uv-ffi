--TEST--
Check for uv_queue_work
--SKIPIF--
<?php
ob_start();
phpinfo();
$data = ob_get_clean();
if (!preg_match("/Thread Safety.+?enabled/", $data) || (extension_loaded("ffi"))) {
  echo "skip";
} ?>
--FILE--
<?php
require 'vendor/autoload.php';

$loop = uv_default_loop();

$a = function() {
    echo "[queue]";
};

$b = function() {
    echo "[finished]";
};
$queue = uv_queue_work($loop, $a, $b);
uv_run();
--EXPECT--
[queue][finished]
