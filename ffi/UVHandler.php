<?php

declare(strict_types=1);

abstract class UVHandler implements UVInterface
{
  private ?\FFI\CData $uv_struct;
  private ?string $struct_cb;

  public function __destruct()
  {
    if (!\is_null_ptr($this->uv_struct))
      $this->free();

    $this->uv_struct = null;
  }

  protected function __construct(string $typedef, string $callback_type = null)
  {
    $this->uv_struct = \uv_ptr(\uv_struct($typedef));

    if (!empty($callback))
      $this->struct_cb = $callback_type;
  }

  public function __invoke(): \FFI\CData
  {
    return $this->uv_struct;
  }

  public function free(): void
  {
    \uv_free($this->uv_struct);
  }

  public function setClose(callable $callback = null): void
  {
    $this->uv_struct->close_cb = $callback;
  }

  public function setCallback(callable $callback = null): void
  {
    $this->uv_struct->{$this->struct_cb} = $callback;
  }

  public function getClose()
  {
    return $this->uv_struct->close_cb;
  }

  public function getCallback()
  {
    return $this->uv_struct->{$this->struct_cb};
  }

  public static function init(?UVLoop $loop, ...$arguments): ?self
  {
    return new self('struct uv_handle_s', 'close_cb');
  }
}
