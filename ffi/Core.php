<?php

declare(strict_types=1);

use FFI\CData;
use FFI\CType;

final class Core
{
	/** @var \FFI[] */
	private static $ffi = [];

	private static ?PhpStream $stream_stdout = null;
	private static ?PhpStream $stream_stderr = null;
	private static ?PhpStream $stream_stdin = null;

	private function __construct()
	{
	}

	public static function setup_stdio()
	{
		self::$stream_stdout = \php_stream_direct(1);
		self::$stream_stderr = \php_stream_direct(2);
		self::$stream_stdin = \php_stream_direct(0);
	}

	public static function clear_stdio(): void
	{
		$stdout = self::$stream_stdout;
		$stderr = self::$stream_stderr;
		$stdin = self::$stream_stdin;
		self::$stream_stdout = null;
		self::$stream_stderr = null;
		self::$stream_stdin = null;
		\ze_ffi()->_php_stream_free($stdout(), ZE::PHP_STREAM_FREE_CLOSE);
		\ze_ffi()->_php_stream_free($stderr(), ZE::PHP_STREAM_FREE_CLOSE);
		\ze_ffi()->_php_stream_free($stdin(), ZE::PHP_STREAM_FREE_CLOSE);
	}

	/**
	 * @param integer $fd
	 * @return PhpStream|null
	 */
	public static function get_stdio(int $fd): ?PhpStream
	{
		switch ($fd) {
			case 0:
				return self::$stream_stdin;
			case 1:
				return self::$stream_stdout;
			case 2:
				return self::$stream_stderr;
		}

		return null;
	}

	public static function get(string $tag): ?\FFI
	{
		return self::$ffi[$tag];
	}

	public static function set(string $tag, ?\FFI $ffi): void
	{
		self::$ffi[$tag] = $ffi;
	}

	public static function clear(string $tag): void
	{
		self::$ffi[$tag] = null;
	}

	public static function clear_ffi(): void
	{
		self::$ffi = null;
	}

	public static function init_libuv(bool $compile = true, string $library = null, string $include = null): void
	{
		if (!self::is_uv_ffi()) {
			// Try if preloaded
			try {
				self::set('uv', \FFI::scope("UV"));
			} catch (Exception $e) {
				\uv_ffi_loader($compile, $library, $include);
			}

			if (!self::is_uv_ffi()) {
				throw new \RuntimeException("FFI parse failed!");
			}
		}
	}

	public static function init_zend(): void
	{
		if (!self::is_ze_ffi()) {
			// Try if preloaded
			try {
				self::set('ze', \FFI::scope("__zend__"));
			} catch (Exception $e) {
				\ze_ffi_loader();
			}

			if (!self::is_ze_ffi()) {
				throw new \RuntimeException("FFI parse failed!");
			}
		}
	}

	public static function cast(string $tag, $type, $ptr): ?CData
	{
		return self::$ffi[$tag]->cast($type, $ptr);
	}

	public static function struct(string $tag, $typedef, bool $owned = true, bool $persistent = false): ?CData
	{
		return self::$ffi[$tag]->new($typedef, $owned, $persistent);
	}

	public static function typedef(string $tag, string $typedef): ?CType
	{
		return self::$ffi[$tag]->type($typedef);
	}

	public static function free(object $ptr): void
	{
		if ($ptr instanceof \UVInterface || $ptr instanceof \UVLoop || $ptr instanceof \UVTypes)
			$ptr->free();
		elseif (\is_cdata($ptr))
			\FFI::free($ptr);
		else
			throw new \LogicException("Unknown class/object passed to free()");
	}

	public static function sizeof(object $ptr): int
	{
		if ($ptr instanceof \UVInterface || $ptr instanceof \UVTypes) {
			return \FFI::sizeof($ptr());
		} elseif (\is_cdata($ptr) || $ptr instanceof CType) {
			return \FFI::sizeof($ptr);
		} else {
			throw new \LogicException("Unknown class/object passed to sizeof()");
		}
	}

	public static function is_null(object $ptr): bool
	{
		try {
			return \FFI::isNull(\ffi_object($ptr));
		} catch (\Throwable $e) {
			return true;
		}
	}

	public static function is_uv_ffi(): bool
	{
		return isset(self::$ffi['uv']) && self::$ffi['uv'] instanceof \FFI;
	}

	public static function is_ze_ffi(): bool
	{
		return isset(self::$ffi['ze']) && self::$ffi['ze'] instanceof \FFI;
	}

	public static function is_win_ffi(): bool
	{
		return isset(self::$ffi['win']) && self::$ffi['win'] instanceof \FFI;
	}
}
