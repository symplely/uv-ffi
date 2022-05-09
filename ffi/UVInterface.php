<?php

declare(strict_types=1);

interface UVInterface
{
  public function __invoke(): \FFI\CData;

  public function free(): void;

  public function setClose(callable $callback): void;

  public function setCallback(callable $callback): void;

  /**
   * @return closure
   */
  public function getClose();

  /**
   * @return closure
   */
  public function getCallback();

  public static function init(?UVLoop $loop, ...$arguments): ?self;
}
