<?php
require 'vendor/autoload.php';

uv_fs_scandir(uv_default_loop(), ".", 0, function ($contents) {
    var_dump($contents);
});

uv_run();
