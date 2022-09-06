<?php

declare(strict_types=1);

use FFI\CData;

interface UVInterface
{
    public function __invoke(?bool $by_handle = false): CData;

    public function free(): void;

    public static function close(object $handle, ?callable $callback = null);

    /** @return static|int */
    public static function init(?\UVLoop $loop, ...$arguments);
}
