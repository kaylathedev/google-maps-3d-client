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
  $opts->eval_code = true;
  $opts->jslint_happy = true;

  return $jsb->beautify($code, $opts);
}

function get_last_preg_error() {
  return array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
}

function simplifyMultiVariableAssignments() {
  global $content;
  global $definitionContent;

  // Repeatedly change multi-variable assignments to single-variable assignments.
  $replacements = [
    ["/\n\t{1}var\s+([^,;{}]+),\n\t\t{1}/", "\n\tvar \$1;\n\tvar "],
    ["/\n\t{2}var\s+([^,;{}]+),\n\t\t{2}/", "\n\t\tvar \$1;\n\t\tvar "],
    ["/\n\t{3}var\s+([^,;{}]+),\n\t\t{3}/", "\n\t\t\tvar \$1;\n\t\t\tvar "],
    ["/\n\t{4}var\s+([^,;{}]+),\n\t\t{4}/", "\n\t\t\t\tvar \$1;\n\t\t\t\tvar "],
    ["/\n\t{5}var\s+([^,;{}]+),\n\t\t{5}/", "\n\t\t\t\t\tvar \$1;\n\t\t\t\t\tvar "],
  ];
  foreach ($replacements as $item) {
    $regex = $item[0];
    $replacement = $item[1];

    do {
      $matches = [];
      preg_match_all($regex, $content, $matches);
      $count = isset($matches[0]) && count($matches[0]) > 0 ? 1 : 0;
      $content = preg_replace($regex, $replacement, $content);

      $matches = [];
      preg_match_all($regex, $definitionContent, $matches);
      $count += isset($matches[0]) && count($matches[0]) > 0 ? 1 : 0;
      $definitionContent = preg_replace($regex, $replacement, $definitionContent);
    } while ($count > 0);
  };
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
			echo "Error!\n";
      print(get_last_preg_error());
      exit;
    }
  }
  return $content;
}
