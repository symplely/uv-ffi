--TEST--
Check for udp bind
--SKIPIF--
<?php if (!extension_loaded("ffi") || (getenv('GITHUB_ACTIONS') !== false && \PHP_OS === 'Linux' && (float) \phpversion() < 8.0)) print "skip"; ?>
--FILE--
<?php
require 'vendor/autoload.php';

$udp = uv_udp_init();
uv_udp_bind6($udp, uv_ip6_addr('::1',10000));

uv_udp_recv_start($udp,function($stream, $buffer) {
    echo "recv: " .  $buffer;

    uv_close($stream);
});

$uv = uv_udp_init();
uv_udp_send($uv, "Hello", uv_ip6_addr("::1", 10000), function($uv, $s) {
    uv_close($uv);
});

uv_run();
--EXPECT--
recv: Hello
