<?php
require 'vendor/autoload.php';

uv_fs_utime(uv_default_loop(), __FILE__, time(), time(), function () {
    echo "Finished\n";
});

uv_run();
