<?php
set_time_limit(60);
ini_set('memory_limit', '256M'); //TODO: Analyze memory usage instead of increasing limit

define('AUTOMATIC_REPLACE_ENABLED', true);


$regexCodeBlock = '\{[^{}]*\}';
$regexCodeBlockLevel1 = "\{(?:[^{}]|(?:{$regexCodeBlock}))*\}";
$regexCodeBlockLevel2 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel1}))*\}";
$regexCodeBlockLevel3 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel2}))*\}";
$regexCodeBlockLevel4 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel3}))*\}";
$regexCodeBlockLevel5 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel4}))*\}";
$regexCodeBlockLevel6 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel5}))*\}";
$regexCodeBlockLevel7 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel6}))*\}";
$regexCodeBlockLevel8 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel7}))*\}";
$regexCodeBlockLevel9 = "\{(?:[^{}]|(?:{$regexCodeBlockLevel8}))*\}";
$regexCodeBlock = "\s*{$regexCodeBlockLevel9}\s*";
$regexAnonymousFunction = "function\s*\([^()]*\){$regexCodeBlock}";
$regexVariable = '[\$A-Za-z_0-9]+';
$regexString = '(?:\"[^\"]*\"|\'[^\']*\')';
$regexNumber = '(?:-?[0-9]+(?:\.[0-9]+)?(?:E-?[0-9]+(?:\.[0-9]+)?)?)';
$regexRegex = '\/.+\/[ig]*'; // WARNING: Creates a huge amount of false positives! Don't rely on this by itself.


$variable = [
  "HbC7FRYovGY",
  "ACT90oEkumd7ZZHZBfs7AsobzkUGs6LJNA"
]; // These seem to change constantly.

require 'lib/definitions.php';

if (file_exists('res/original.js')) {
  $content = file_get_contents('res/original.js');
} else {
  $base = 'https://www.google.com';
  $scriptFilename = "$base/maps/_/js/k=maps.m.en.{$variable[0]}.O/m=b/d=1/rs={$variable[1]}";
  $content = file_get_contents($scriptFilename);
  file_put_contents('res/original.js', $content);
}

$definitionContent = '';

require 'module-beautify.php';

if (!is_dir('tmp')) {
  mkdir('tmp');
}
if (!is_dir('res')) {
  mkdir('res');
}

file_put_contents('tmp/1-after-beautify-definitons.js', $definitionContent);
file_put_contents('tmp/1-after-beautify.js', $content);

require 'module-reduce.php';

file_put_contents('tmp/2-after-reduce-definitons.js', $definitionContent);
file_put_contents('tmp/2-after-reduce.js', $content);


// Extra code for "definition content"
$definitionContent = '
var map_url = ["/maps/_/js/","m","maps.m.en.'
. $variable[0]
. '.O","'
. $variable[1]
. '"]; // Unsure of its real purpose
window.APP_OPTIONS=[,,,[,,,,,,[[""],,[""],,,,,"/maps/vt"],,,,,,,"//kh.google.com/rt/earth"],,,,,,,,,,,,,,,,,,,,,,[1,],,,,,,,,,,,,,,map_url,,,,,,,,,,,,,,,,,,,1];
' . $definitionContent;


// Extra code for "content"
$content .= '';

echo "Finished processing.\n";

file_put_contents('res/processed-definitions.js', $definitionContent);
file_put_contents('res/processed-content.js', $content);
