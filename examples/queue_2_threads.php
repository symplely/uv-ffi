<?php

$loop = uv_default_loop();
$completed = false;
uv_queue_work($loop, function () use (&$completed) {
    while (!$completed) {
        echo "[queue2]";
        sleep(1);
    }
}, function () {
    echo "[finished]";
});

uv_queue_work($loop, function () use (&$completed) {
    while (!$completed) {
        echo "[queue1]";
        $completed = true;
        sleep(1);
    }
}, function () {
    echo "[finished]";
});

while (true) {
    if ($completed) {
        echo ' --finish-- ';
        var_dump($completed);
        uv_run($loop);
        break;
    }

    uv_run($loop, UV::RUN_NOWAIT);
}
