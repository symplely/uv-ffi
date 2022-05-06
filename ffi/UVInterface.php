<?php

declare(strict_types=1);

interface UVInterface
{
  public function __invoke(): \FFI\CData;
  public function free(): void;
  public static function init(?UVLoop $loop, string $typedef, ...$arguments): self;
}
