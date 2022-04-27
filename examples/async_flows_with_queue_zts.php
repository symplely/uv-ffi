<?php
if (\ZEND_THREAD_SAFE) {
    $loop = uv_default_loop();
    $count = 1;
    $counter = 1;
    $callable = function ($async) use (&$count) {
        global $counter;
        echo 'Main counter at: ' . $counter . \PHP_EOL;
        var_dump($count);
        $count++;
        echo '-';
        uv_async_send($async);
        echo ' -> ';
    };

    $async = uv_async_init($loop, $callable);

    $callableOther = function ($asyncOther) use (&$count) {
        global $counter;
        echo 'Other counter at: ' . $counter . \PHP_EOL;
        $count++;
        uv_async_send($asyncOther);
    };

    $asyncOther = uv_async_init($loop, $callableOther);
    uv_async_send($async);
    uv_async_send($asyncOther);

    $prepare = uv_prepare_init($loop);

    uv_prepare_start($prepare, function ($prepare) {
        echo " Still preparing... ";
    });

    $check = uv_check_init($loop);

    $idle = uv_idle_init();

    $i = 0;
    uv_idle_start($idle, function ($status) use (&$i, $idle, $loop) {
        echo "idle count: {$i}" . PHP_EOL;
        $i++;

        if ($i > 25) {
            uv_idle_stop($idle);
        }
        usleep(50000);
    });

    uv_check_start($check, function ($check) {
        echo "Checking...\n";
    });

    $in  = uv_pipe_init($loop, ('/' == \DIRECTORY_SEPARATOR));
    $out = uv_pipe_init($loop, ('/' == \DIRECTORY_SEPARATOR));

    $signal = uv_signal_init();

    uv_signal_start($signal, function ($signal) use ($out) {
        echo PHP_EOL . 'The CTRL+C signal received, click the [X] to close the window.' . PHP_EOL;
        uv_signal_stop($signal);
        uv_close($out, function () {
            print 'Stopped reading' . PHP_EOL;
        });
    }, 2);

    $signal = uv_signal_init();

    uv_signal_start($signal, function ($signal) {
        echo PHP_EOL . 'The SIGHUP signal received, the OS will close this session window!' . PHP_EOL;
    }, 1);

    $stdio = array();
    $stdio[] = uv_stdio_new($in, UV::CREATE_PIPE | UV::READABLE_PIPE);
    $stdio[] = uv_stdio_new($out, UV::CREATE_PIPE | UV::WRITABLE_PIPE);

    $flags = 0;
    $pid = uv_spawn(
        $loop,
        "php",
        array('-r', 'echo "Now spawning! " . PHP_EOL;do {echo "*";usleep(500);} while (true);'),
        $stdio,
        __DIR__,
        [],
        function ($process, $stat, $signal) {
            if ($signal == 9) {
                echo "The process was terminated with 'SIGKILL' or '9' signal!" . PHP_EOL;
            }

            uv_close($process, function () {
            });
        },
        $flags
    );

    uv_read_start($out, function ($out, $nread, $buffer) {
        if ($nread > 0)
            print $buffer;
    });

    uv_queue_work($loop, function () use (&$counter) {
        while ($counter) {
            echo " [queue] " . $counter;
            usleep(10000);
            $counter++;
        }
    }, function () {
        echo "[finished]";
    });

    while (true) {
        echo 'Waiting... ';
        if ($counter > 100) {
            echo 'finish';
            uv_close($async, function (UV $handle) {
                print ' with first';
            });

            uv_close($asyncOther, function (UV $handle) {
                print ', with second,';
            });

            uv_unref($prepare);
            uv_check_stop($check);
            uv_run($loop);
            break;
        } else {
            $counter++;
            uv_run($loop, UV::RUN_NOWAIT);
        }
    }
}
