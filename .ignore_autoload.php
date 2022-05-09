<?php

declare(strict_types=1);

\define('DS', \DIRECTORY_SEPARATOR);

if (\file_exists('..' . \DS . '.gitignore')) {
  $ignore = \file_get_contents('..' . \DS . '.gitignore');
  if (\strpos($ignore, '.cdef/') === false) {
    $ignore .= '.cdef' . \DS . \PHP_EOL;
    \file_put_contents('..' . \DS . '.gitignore', $ignore);
  }
} else {
  \file_put_contents('..' . \DS . '.gitignore', '.cdef' . \DS . \PHP_EOL);
}
echo "- Initialized .gitignore" . PHP_EOL;

if (\file_exists('..' . \DS . '.gitattributes')) {
  $export = \file_get_contents('..' . \DS . '.gitattributes');
  if (\strpos($export, '/.cdef') === false) {
    $export .= '/.cdef       export-ignore' . \PHP_EOL;
    \file_put_contents('..' . \DS . '.gitattributes', $export);
  }
} else {
  \file_put_contents('..' . \DS . '.gitattributes', '/.cdef       export-ignore' . \PHP_EOL);
}
echo "- Initialized .gitattributes" . \PHP_EOL;

if (\file_exists('..' . \DS . 'composer.json'))
  $composerJson = \json_decode(\file_get_contents('..' . \DS . 'composer.json'), true);
else
  $composerJson = [];

if (isset($composerJson['autoload'])) {
  if (isset($composerJson['autoload']['files']) && !\in_array('.cdef/preload.php', $composerJson['autoload']['files']))
    \array_push($composerJson['autoload']['files'], ".cdef/preload.php", ".cdef/ffi/UVConstants.php", ".cdef/ffi/UVFunctions.php");
  elseif (!isset($composerJson['autoload']['files']))
    $composerJson = \array_merge($composerJson, ["autoload" => ["files" => [".cdef/preload.php", ".cdef/ffi/UVConstants.php",  ".cdef/ffi/UVFunctions.php"]]]);

  if (isset($composerJson['autoload']['classmap']) && !\in_array('.cdef/ffi/', $composerJson['autoload']['classmap']))
    \array_push($composerJson['autoload']['classmap'], ".cdef/ffi/");
  elseif (!isset($composerJson['autoload']['classmap']))
    $composerJson = \array_merge($composerJson, ["autoload" => ["classmap" => [".cdef/ffi/"]]]);
} else {
  $composerJson = \array_merge($composerJson, [
    "autoload" => [
      "files" => [
        ".cdef/preload.php",
        ".cdef/ffi/UVConstants.php",
        ".cdef/ffi/UVFunctions.php"
      ],
      "classmap" => [
        ".cdef/ffi/"
      ]
    ]
  ]);
}

if (isset($composerJson['require']['symplely/uv-ffi']))
  unset($composerJson['require']['symplely/uv-ffi']);

\file_put_contents(
  '..' . \DS . 'composer.json',
  \json_encode($composerJson, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)
);
echo "- Initialized `autoload` & `require` composer.json" . \PHP_EOL;

function recursiveDelete($directory, $options = array())
{
  if (!isset($options['traverseSymlinks']))
    $options['traverseSymlinks'] = false;
  $files = \array_diff(\scandir($directory), array('.', '..'));
  foreach ($files as $file) {
    $dirFile = $directory . \DS . $file;
    if (\is_dir($dirFile)) {
      if (!$options['traverseSymlinks'] && \is_link(\rtrim($file, \DS))) {
        \unlink($dirFile);
      } else {
        \recursiveDelete($dirFile, $options);
      }
    } else {
      \unlink($dirFile);
    }
  }

  return \rmdir($directory);
}


$delete = '';
if ('\\' !== \DS) {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'windows.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'Headers' . \DS . 'extra_windows.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Windows' . \DS . 'uv.dll');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Windows');
  $delete .= 'Windows ';
}

if (\PHP_OS !== 'Darwin') {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'macos.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'macOS' . \DS . 'libuv.1.0.0.dylib');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'macOS');
  $delete .= 'Apple macOS ';
}

if (\php_uname('m') !== 'aarch64') {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'pi.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'raspberry' . \DS . 'libuv.so.1.0.0');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'raspberry');
  $delete .= 'Raspberry Pi ';
}

$os = [];
$files = \glob('/etc/*-release');
foreach ($files as $file) {
  $lines = \array_filter(\array_map(function ($line) {
    // split value from key
    $parts = \explode('=', $line);
    // makes sure that "useless" lines are ignored (together with array_filter)
    if (\count($parts) !== 2)
      return false;

    // remove quotes, if the value is quoted
    $parts[1] = \str_replace(['"', "'"], '', $parts[1]);
    return $parts;
  }, \file($file)));

  foreach ($lines as $line)
    $os[$line[0]] = $line[1];
}

$id = \trim((string) $os['ID']);
$like = \trim((string) $os['ID_LIKE']);
$version = \trim((string) $os['VERSION_ID']);
if ((float)$version !== 20.04) {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'ubuntu20.04.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'ubuntu20.04' . \DS . 'libuv.so.1.0.0');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'ubuntu20.04');
  $delete .= 'Ubuntu 20.04 ';
}

if ((float)$version !==  18.04) {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'ubuntu18.04.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'ubuntu18.04' . \DS . 'libuv.so.1.0.0');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'ubuntu18.04');
  $delete .= 'Ubuntu 18.04 ';
}

if (!(float)$version >= 8) {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'centos8+.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'centos8+' . \DS . 'libuv.so.1.0.0');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'centos8+');
  $delete .= 'Centos 8+ ';
}

if (!(float)$version < 8) {
  \unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'centos7.h');
  \unlink('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'centos7' . \DS . 'libuv.so.1.0.0');
  \rmdir('..' . \DS . '.cdef' . \DS . 'lib' . \DS . 'Linux' . \DS . 'centos7');
  $delete .= 'Centos 7 ';
}

\unlink('..' . \DS . 'cdef' . \DS . 'headers' . \DS . 'original' . \DS . 'uv.h');
\recursiveDelete('..' . \DS . '.cdef' . \DS . 'headers' . \DS . 'original' . \DS . 'uv');
echo "- Removed unneeded `libuv` binary libraries and .h headers" . $delete . \PHP_EOL;

unlink(__FILE__);
