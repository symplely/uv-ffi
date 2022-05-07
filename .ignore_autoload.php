<?php

declare(strict_types=1);

if (\file_exists('../' . '.gitignore')) {
  $ignore = \file_get_contents('../' . '.gitignore');
  if (\strpos($ignore, '.cdef/') === false) {
    $ignore .= '.cdef/' . \PHP_EOL;
    \file_put_contents('../' . '.gitignore', $ignore);
  }
} else {
  \file_put_contents('../' . '.gitignore', '.cdef/' . \PHP_EOL);
}
echo "- Initialized .gitignore" . PHP_EOL;

if (\file_exists('../' . '.gitattributes')) {
  $export = \file_get_contents('../' . '.gitattributes');
  if (\strpos($export, '/.cdef') === false) {
    $export .= '/.cdef       export-ignore' . \PHP_EOL;
    \file_put_contents('../' . '.gitattributes', $export);
  }
} else {
  \file_put_contents('../' . '.gitattributes', '/.cdef       export-ignore' . \PHP_EOL);
}
echo "- Initialized .gitattributes" . \PHP_EOL;

if (\file_exists('../' . 'composer.json'))
  $composerJson = \json_decode(\file_get_contents('../' . 'composer.json'), true);
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
  '../' . 'composer.json',
  \json_encode($composerJson, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)
);
echo "- Initialized `autoload` & `require` composer.json" . \PHP_EOL;
