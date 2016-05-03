<?php

$c = create_function('$a', 'return $a;');

define('R_CSS_NAME', '(#|\.)[A-z]+(-[A-z]+)*(:{1,2}[a-z]+)?');
define('R_CSS_SELECTOR', "{$c(R_CSS_NAME)}(\s*{$c(R_CSS_NAME)})*");
define('R_CSS_MANY_SELECTORS', "{$c(R_CSS_SELECTOR)}(\s*,\s*{$c(R_CSS_SELECTOR)})*");

define('R_CSS_NUMERIC', '(-?[0-9]+(px|%|em)|0)');
define('R_CSS_SINGLE_VALUE', "({$c(R_CSS_NUMERIC)}|auto)");
define('R_CSS_MANY_VALUES', "({$c(R_CSS_SINGLE_VALUE)}(\s+{$c(R_CSS_SINGLE_VALUE)})*)");
define('R_CSS_VALUE', "({$c(R_CSS_MANY_VALUES)}(\s*!important)?)");
define('R_CSS_COLON', '\s*:\s*');


function beautify($code) {
  require 'lib/format.php';

  $jsb = new JSBeautifier();
  $opts = new BeautifierOptions();

  $opts->indent_with_tabs = true;
  return $jsb->beautify($code, $opts);
}

function get_last_preg_error() {
  return array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
}

function replaceManyByBasic($replacements, $content) {
  foreach ($replacements as $replacement) {
    $search = $replacement[0];
    $substitute = $replacement[1];

    $content = str_replace($search, $substitute, $content);
  }
  return $content;
}

function replaceManyByRegex($replacements, $content) {
  foreach ($replacements as $replacement) {
    $regex = $replacement[0];
    $substitute = $replacement[1];

    $content = preg_replace('/' . $regex . '/', $substitute, $content);
    if ($content === null) {
      print(get_last_preg_error());
      exit;
    }
  }
  return $content;
}
