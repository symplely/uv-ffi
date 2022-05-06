<?php

declare(strict_types=1);

final class Core
{
    protected static ?\FFI $ffi = null;

    public static function get(): ?\FFI
    {
        return self::$ffi;
    }

    public static function set(?\FFI $ffi): void
    {
        self::$ffi = $ffi;
    }

    public static function init(bool $compile = true, string $library = null, string $include = null): void
    {
        if (!self::is_ffi()) {
            // Try if preloaded
            try {
                self::set(\FFI::scope("UV"));
            } catch (\FFI\Exception $e) {
                \ffi_loader($compile, $library, $include);
            }

            if (!self::is_ffi()) {
                throw new \RuntimeException("FFI parse failed!");
            }
        }
    }

    public static function struct($typedef, bool $owned = true, bool $persistent = false): ?\FFI\CData
    {
        return self::$ffi->new($typedef, $owned, $persistent);
    }

    public static function free($ptr): void
    {
        if ($ptr instanceof \UVInterface || $ptr instanceof \UVLoop)
            $ptr->free();
        elseif ($ptr instanceof \FFI\CData)
            FFI::free($ptr);
        else
            throw new \LogicException("Unknown class/object passed to free()");
    }

    public static function typeof($ptr): FFI\CType
    {
        if ($ptr instanceof \UVInterface || $ptr instanceof \UVLoop)
            return self::$ffi->typeof($ptr());
        elseif ($ptr instanceof \FFI\CData)
            return self::$ffi->typeof($ptr);
        else
            throw new \LogicException("Unknown class/object passed to typeof()");
    }

    public static function sizeof($ptr): int
    {
        if (\is_object($ptr) && $ptr instanceof \UVInterface) {
            return self::$ffi->sizeof($ptr());
        } elseif ($ptr instanceof \FFI\CData || $ptr instanceof \FFI\CType) {
            return self::$ffi->sizeof($ptr);
        } else {
            throw new \LogicException("Unknown class/object passed to sizeof()");
        }
    }

    public static function is_null($ptr): bool
    {
        if ($ptr instanceof \UVInterface || $ptr instanceof \UVLoop)
            return \FFI::isNull($ptr());
        elseif ($ptr instanceof \FFI\CData)
            return \FFI::isNull($ptr);
        else
            throw new \LogicException("Unknown class/object passed to isNull()");
    }

    public static function is_ffi(): bool
    {
        return self::$ffi instanceof \FFI;
    }
}
