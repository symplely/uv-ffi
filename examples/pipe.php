<?php

$pipe = uv_pipe_init(uv_default_loop(), 0);
uv_pipe_open($pipe, STDOUT);

uv_write($pipe, "Hello", function ($pipe, $status) {
    echo 1;
    uv_close($pipe);
});

uv_run();
