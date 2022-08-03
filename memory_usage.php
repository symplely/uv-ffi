<?php

echo 'Memory usage before autoload: ' . memory_get_usage() . "\n";
echo 'Memory usage before autoload: ' . round(memory_get_usage() / 1048576) . 'MB' . "\n";
require 'vendor/autoload.php';
echo 'Memory usage after autoload: ' . round(memory_get_usage() / 1048576) . 'MB' . "\n\n";

class TestCase
{
  public $counter = 0;

  public function run()
  {
    $loop = uv_loop_new();

    $handler = uv_pipe_init($loop, false);
    uv_pipe_open($handler, STDOUT);
    $a = 0;
    while (++$a <= 1000) {
      uv_write($handler, '', function () {
        $this->counter++;
      });
    }

    uv_close($handler);
    echo 'Memory usage before loop run: ' . round(memory_get_usage() / 1048576) . 'MB';
    uv_run($loop);
    echo "\n" . 'Memory usage after loop run, and before loop destroy: ' . round(memory_get_usage() / 1048576) . 'MB' . "\n\n";
  }
}

$t = new TestCase;
$memory = memory_get_usage();

$t->run();

$memory = memory_get_usage() - $memory;

echo "Memory Leak/Usage:\n$t->counter\n" . $memory . "\n";
echo "\n$t->counter\n" . round($memory / 1048576) . "MB\n\n";
echo 'Memory usage after loop destroy: ' . round(memory_get_usage() / 1048576) . 'MB';
