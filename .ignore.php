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
echo "- Initialized .gitattributes" . PHP_EOL;
