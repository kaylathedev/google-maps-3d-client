<?php
// $content = ...;


// Simple replacments
$replacements = [
  ["content:\"\";",   ""],
  ["content:'';",   ""],
];
$definitionContent = replaceManyByBasic($replacements, $definitionContent);
$content = replaceManyByBasic($replacements, $content);


// CSS Replacements
$properties = [
  'animation(-[a-z]+)*',
  'background(-[a-z]+)*',
  'border(-(top|left|right|bottom))?(-(color|radius|style|width))?',
  'box(-(shadow|sizing))',
  'clear',
  'color',
  'cursor',
  'display',
  'flex',
  'float',
  'font(-[a-z]+)*',
  '((max|min|line)-)height',
  'margin(-(top|left|right|bottom))?',
  'opacity',
  'outline(-[a-z]+)?',
  'overflow(-(x|y))?',
  'pointer-events',
  'padding(-(top|left|right|bottom))?',
  'position',
  'text-[a-z]+',
  'transform',
  'translate',
  'transition(-[a-z]+)*',
  'user-select',
  'vertical-align',
  'visibility',
  'white-space',
  'z-index',
  '-moz(-[A-z]+)+',
  '-ms(-[A-z]+)+',
  '-webkit(-[A-z]+)+',
];


// Regex replacments (before beautify)
$replacements = [

  // More CSS
  ['(min|max)?(' . implode('|', $properties) . "){$c(R_CSS_COLON)}[^;\"\{\}]+;", ""],
  ['(min|max)?(' . implode('|', $properties) . "){$c(R_CSS_COLON)}[^;\"\{\}]+\}", "}"],


  ["(background|display|border(-(top|left|right|bottom))?)(-[a-z]+)?{$c(R_CSS_COLON)}[^;\"\{\}]+;", ""],
  ["((top|left|right|bottom|width|height|z-index|margin|padding):{$c(R_CSS_VALUE)};)", ""],
  ["((top|left|right|bottom|width|height|z-index|margin|padding):{$c(R_CSS_VALUE)}\}\")", "}\""],
  ["(((background|border)-)?color){$c(R_CSS_COLON)}(#[0-9a-f]{3,6}|[a-z]+);?", ""],
  ["((text|vertical)-align){$c(R_CSS_COLON)}(center|left|right|top|bottom);?", ""],
  ["#[A-Fa-f0-9]{6}", ""],
	//["\"[a-z]+[A-Z][a-z]+\"", "\"\""],


  ["\"{$c(R_CSS_MANY_SELECTORS)}\{\}", "\""],
  ["'{$c(R_CSS_MANY_SELECTORS)}\{\}", "'"],
  ["background(-image)?:url\(\"[^;\"\{\}]+\"\)[^;\"\{\}]*;", ""],
  ["background(-image)?:url\(\"[^;\"\{\}]+\"\)[^;\"\{\}]*\}", "}"],
  ["\"[-.,:+a-z\s()]+\{\}\"", "\"\""], // All empty CSS selectors
  ["'[-.,:+a-z\s()]+\{\}'", "''"], // All empty CSS selectors


  ["_\.[A-z]+\(\"about:blank\"\);", ""],
  ["_\.[A-z]+\(\"<!DOCTYPE html>\", 0\);", ""],
  ["_\.C\s*\([^()]*\);\n?", ""], // Removing all calls to _.C()
  ["_\.A\s*\(\"[^()\"]+\"\);\n?", ""], // Removing all calls to _.A(string)
  ["_\.z\s*\([^()]*\);\n?", ""], // Removing all calls to _.C()


  ["\s*\<\/?([A-Za-z]+)\>\s*", ""], // Any XML tag. For example, <hr> <h1> </h1> </someTHINGcrazy>


  ["\s*=\s*\"(\.|\/|#|@)[^\"]+\";", " = \"\";"], // Clearing all strings that begin with ./#@
  ["\s*=\s*'(\.|\/|#|@)[^']+';", " = '';"], // Clearing all strings that begin with ./#@

];
$definitionContent = replaceManyByRegex($replacements, $definitionContent);
$content = replaceManyByRegex($replacements, $content);



$functionsToClear = [
  "Iba[A-Za-z\.]*",
  "[KJPQTX]da",
	"[moq]Ea",
	"[be-il-puw-zA-COP]Xa",
  "[gjkMO]ea",
  "[a-xzA-Z]Ea",
	"[yABF]ka",

	"[nCK]la",
  "wpa",
  "[deh]nb",
  "Nmb",
  "[\$adhpvF-HZ]0a",
  ".Ob",
  ".[vyz]a",
  "[\$gij-ps-yBCFH-LY]wc",
  "[glrv]xc",
  "[cegptT]zc",
  ".[LW]a",
  "[a-gijlx-z]1a",
  "oc",
  "[fpvABIW]ca",

  "[nyG-KMP-U]ba",
  //"[B]ba",
  "DJa",

];
$functionsToRemove = [
  "_\.z",
  "oc\.prototype\.(V)",
  "qM\.prototype\.(ka)",
  "(.f)\.prototype\.[\$a-zA-z]+",
  "(?:_\.)?([m]c|[\$flnpwxLV-Z]e|[deghjm-su-wyzA]f|.s|[f-hklA]t|[Q-V]G|nM|gea|[sGJN]da|[foCH]zc|Wyc|.[bf-hMR]a|[o-u]Hb)\.prototype\.([\$a-zA-z]+)",
	"_\.(?:LXa|Sda|hKb|Nqc)",
  "_\.[JOQR]r",
  "_\.uLa",

  "_\.(Jd)",
  //"_\.(Rda)",
	"[K]Ja",
	"[a]Xa",
  "[f]ea",

  "(?:_\.)?[jr-xz]j",
  "(?:_\.)?[vIL]na",
  "(?:_\.)?[t-v]Ac",
  "[fg]nb",
  "pD\.prototype\.Va",
  "(?:_\.)?ila",
  "(?:_\.)?Ejb",
  "[c-eik]Ac",

  "_\.Z",
  "flb",
  "[abk]nb",
  "yca",
  "[\$x]mb",
  "trc",
  "_\.Ejb",
  "_\.xD\.prototype\.nb",
  "_\.AD",
  "_\.r\.we",

  "_\.hla",
  "_\.[F]c",
  "Czc",
  "qM\.prototype\.initialize",
  "lAc",

];
// Regex replacments (after beautify)
$replacements = [
  // Emptying functions
  ["var\s+(" . implode("|", $functionsToClear) . ")\s*=\s*{$regexAnonymousFunction};", "if (!\$1) var \$1 = function () {};"],
  ["\\n(" . implode("|", $functionsToClear) . ")\s*=\s*{$regexAnonymousFunction};", ""],
  ["var\s+(" . implode("|", $functionsToRemove) . ")\s*=\s*{$regexAnonymousFunction};", ""],
  ["\\n(" . implode("|", $functionsToRemove) . ")\s*=\s*{$regexAnonymousFunction};", ""],

	//["\s+\(0,\s*([A-Za-z0-9_]+(?:\.[A-Za-z0-9_]+)*)\)\(", " \$2("], // TODO: not working

  // Removes the if block at line 51.
  ["if \(\"undefined\" == typeof window.globals \|\| !_\.Tb\(window\.globals\.ErrorHandler\)\)${regexCodeBlock};", ""],

  ["\s*=\s*\"(\.|\/|#|@)[^\"]+\";", " = \"\";"], // Clearing all strings that begin with ./#@
  ["\s*=\s*'(\.|\/|#|@)[^']+';", " = '';"], // Clearing all strings that begin with ./#@

  ["if\s*\(_\.uG\(\)\)${regexCodeBlock};", ""],
];
$definitionContent = replaceManyByRegex($replacements, $definitionContent);
$content = replaceManyByRegex($replacements, $content);


