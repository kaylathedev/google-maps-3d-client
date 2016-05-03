<?php
set_time_limit(60);
ini_set('memory_limit', '256M'); //TODO: Analyze memory usage instead of increasing limit

require 'config.php';
require 'lib/definitions.php';

if (file_exists('res/temp-script-original.js')) {
  $content = file_get_contents('res/temp-script-original.js');
} else {
  $base = 'https://www.google.com';
  $scriptFilename = "$base/maps/_/js/k=maps.m.en.{$variable[0]}.O/m=b/d=1/rs={$variable[1]}";
  $content = file_get_contents($scriptFilename);
  file_put_contents('res/temp-script-original.js', $content);
}


// Simple replacments
$replacements = [
  ["\r\n", "\n"], // Removes other line endings
  ["\r",   "\n"], // Removes other line endings
  ["<br>",   ""], // Removes other line endings

  ["\ntry{\n",                          "\n"], // Removes top-level try blocks
  ["\n}catch(e){_._DumpException(e)}\n", "\n"], // Removes top-level catch blocks

  ["this._=this._||{};(function(_){var window=this;\n", "this._=this._||{};var window=this;"], // Removes first function definition
  ["\n})(this._);\n", "\n"], // Removes last function definition
  ["'use strict';", "\n"], // Removes strict calls

  ["this._ = this._ || {};\nvar window = this;", ""],
];
$content = replaceManyByBasic($replacements, $content);


// CSS Replacements
$properties = [
  'background',
  'border(-(top|left|right|bottom))?',
  'display',
  '-webkit(-[A-z])+',
];
$regex = '/(' . implode('|', $properties) . "){$c(R_CSS_COLON)}[^;\"\{\}]+;/";
$content = preg_replace($regex, '', $content);


// Regex replacments
$replacements = [
  ["(background|display|border(-(top|left|right|bottom))?)(-[a-z]+)?{$c(R_CSS_COLON)}[^;\"\{\}]+;", ""],
  //['/\n\}\)\(this\._\);\n/', "\n"], // Removes last function definition
  ["((top|left|right|bottom|width|height|z-index|margin|padding):{$c(R_CSS_VALUE)};)", ""],
  ["((top|left|right|bottom|width|height|z-index|margin|padding):{$c(R_CSS_VALUE)}\}\")", "\}\""],
  ["(((background|border)-)?color){$c(R_CSS_COLON)}(#[0-9a-f]{3,6}|[a-z]+);?", ""],
  ["((text|vertical)-align){$c(R_CSS_COLON)}(center|left|right|top|bottom);?", ""],
  ["#[A-Fa-f0-9]{6}", ""],
  ["", ""],
  ["", ""],
];
$content = replaceManyByRegex($replacements, $content);


// Automatic Constant Replacement (slowest so far)
$strings = [];
$matches = [];
$count = preg_match_all('/[^A-z_](_\.[_A-z]+)="([^"]{1,12})"/', $content, $matches);
if ($count === false) {
  print("Error! Invalid regex!");
  exit;
}
for ($i = 0; $i < $count; $i++) {
  $variableName = $matches[1][$i];
  $stringValue  = $matches[2][$i];
  $strings[] = [$variableName, $stringValue];
}
foreach ($strings as $replacement) {
  $regex = '/([^A-z0-9])(' . preg_quote($replacement[0]) . ')([^=A-z0-9-])/';
  $substitute = '$1"' . preg_quote($replacement[1]) . '"$3';

  $matches = [];
  $content = preg_replace($regex, $substitute, $content);
  if ($content === null) {
    print(get_last_preg_error());
    exit;
  }
}


// Beautification
$content = beautify($content);


// Moving definitions away from function calls
$functionContent = '';

$regexCodeBlock = '\{[^{}]*\}';
$regexCodeBlockLevel1 = "\{(?:[^{}]|(?:${regexCodeBlock}))*\}";
$regexCodeBlockLevel2 = "\{(?:[^{}]|(?:${regexCodeBlockLevel1}))*\}";
$regexCodeBlockLevel3 = "\{(?:[^{}]|(?:${regexCodeBlockLevel2}))*\}";
$regexVariable = '[A-z_]+[0-9A-z_]*';
$regexAnonymousFunction = "function\s*\([^()]*\)\s*${regexCodeBlockLevel3}";

$regex = "/\\n(var ${regexVariable}(?:\.${regexVariable})*)\s*=\s*(${regexAnonymousFunction}|${regexVariable});/";
preg_match_all($regex, $content, $matches);
foreach ($matches[0] as $index => $match) {
  $name = $matches[1][$index];
  $value = $matches[2][$index];
  $functionContent .= "//New Value\n${name} = ${value};\n\n";
}

$content = preg_replace($regex, "", $content);


// Output
header('Content-type: text/javascript');
ob_start();
?>

this._ = this._ || {};
var window = this;

<?= $functionContent ?>
<?= $content ?>

<?php
$data = ob_get_contents();
file_put_contents('res/temp-script-processed.js', $data);
