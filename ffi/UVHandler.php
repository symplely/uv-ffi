<?php

declare(strict_types=1);

abstract class UVHandler implements UVInterface
{
  private ?FFI\CData $uv_struct;

  protected function __construct(string $typedef)
  {
    $this->uv_struct = \uv_struct($typedef);
  }

  public function __invoke(): \FFI\CData
  {
    return \uv_ptr($this->uv_struct);
  }

  public function free(): void
  {
    \uv_free($this->uv_struct);
  }

  public static function init(?UVLoop $loop, string $typedef, ...$arguments): self
  {
    return new self($typedef);
  }
}
