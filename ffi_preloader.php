<?php

declare(strict_types=1);

if (!\function_exists('ffi_preloader')) {
  if (!\defined('DS'))
    \define('DS', \DIRECTORY_SEPARATOR);

  function ffi_preloader(): void
  {
    if (!\function_exists('zend_preloader') || !\class_exists('Core'))
      include_once '..' . \DS . 'vendor' . \DS . 'symplely' . \DS . 'zend-ffi' . \DS . 'preload.php';

    if (!\file_exists('.' . \DS . 'ffi_generated.json')) {
      $directories = \glob('*', \GLOB_ONLYDIR);
      $directory = $files = [];
      foreach ($directories as $ffi_dir) {
        if (\file_exists($ffi_dir . \DS . 'ffi_extension.json')) {
          $ffi_list = \json_decode(\file_get_contents($ffi_dir . \DS . 'ffi_extension.json'), true);
          if (isset($ffi_list['preload']['directory'])) {
            \array_push($directory, $ffi_list['preload']['directory']);
          } elseif (isset($ffi_list['preload']['files'])) {
            \array_push($files, $ffi_list['preload']['files']);
          }
        }
      }

      \file_put_contents(
        '.' . \DS . 'ffi_generated.json',
        \json_encode([
          "preload" => [
            "files" => $files,
            "directory" => $directory
          ]
        ], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)
      );
    }

    if (\file_exists('.' . \DS . 'ffi_generated.json')) {
      $loader = function ($iterator, bool $isDir, bool $is_opcache_cli) {
        foreach ($iterator as $fileInfo) {
          if ($isDir && !$fileInfo->isFile()) {
            continue;
          }

          $file = $isDir ? $fileInfo->getPathname() : $fileInfo;
          if ($is_opcache_cli) {
            if (!\opcache_is_script_cached($file))
              \opcache_compile_file($file);
          } else {
            include_once $file;
          }
        }
      };

      $preload_list = \json_decode(\file_get_contents('.' . \DS . 'ffi_generated.json'), true);
      $is_opcache_cli = \ini_get('opcache.enable_cli') === '1';
      if (isset($preload_list['preload']['files'])) {
        $loader($preload_list['preload']['files'], false, $is_opcache_cli);
      }

      if (isset($preload_list['preload']['directory'])) {
        foreach ($preload_list['preload']['directory'] as $directory) {
          $dir = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::KEY_AS_PATHNAME);
          $iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
          $loader($iterator, true, $is_opcache_cli);
        }
      }
    }
  }

  \ffi_preloader();
}
