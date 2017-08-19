<?php
// $content = ...;


// Simple replacments
$replacements = [
  ["\r\n", "\n"], // Removes other line endings
  ["\r",   "\n"], // Removes other line endings
  ["void 0", "undefined"],

  ["\ntry{\n", "\n"], // Removes top-level try blocks
  ["\n}catch(e){_._DumpException(e)}\n", "\n"], // Removes top-level catch blocks

  ["this._=this._||{};(function(_){var window=this;\n", "this._=this._||{};var window=this;"], // Removes first function definition
  ["\n})(this._);\n", "\n"], // Removes last function definition
  ["'use strict';", "\n"], // Removes strict calls

  ["this._ = this._ || {};\nvar window = this;", ""],
];
$content = replaceManyByBasic($replacements, $content);


// Regex replacments (before beautify)
$replacements = [
  ["\s*!1", " false"],
  ["\s*!0", " true"],

  // Replacing _.Xb(something) with the actual function code
  ["_\.Xb\((_\.[A-Za-z]+)\);", "\$1.Bb = function () {\n\tif (!\$1.HI) {\n\t\t\$1.HI = new \$1;\n\t}\n\treturn \$1.HI;\n};"],

];
$content = replaceManyByRegex($replacements, $content);


// Beautification
$content = beautify($content);


// Repeatedly move empty variables (at the beginning)
$definitionContent .= "//Empty variables.\n";
$regex = "/\\n(?:var\s+)({$regexVariable}),\s*/";
do {
  preg_match_all($regex, $content, $matches);
  foreach ($matches[0] as $index => $match) {
    $definition = $matches[1][$index];
    $definitionContent .= "var ${definition};\n";
  }
  $content = preg_replace($regex, "\nvar ", $content);
} while (isset($matches[0]) && count($matches[0]) > 0);
$definitionContent .= "\n\n";


// Repeatedly move empty variables (in the middle)
$definitionContent .= "//Empty variables.\n";
$regex = "/,\n\t({$regexVariable})\s*,\s*/";
do {
  preg_match_all($regex, $content, $matches);
  foreach ($matches[0] as $index => $match) {
    $definition = $matches[1][$index];
    $definitionContent .= "var ${definition};\n";
  }
  $content = preg_replace($regex, ",\n\t", $content);
} while (isset($matches[0]) && count($matches[0]) > 0);
$definitionContent .= "\n\n";


// Regex replacments (after beautify)
$replacements = [
  [",\n\t([\$A-Za-z_0-9]+(?:\s*=\s*|;))", ";\nvar \$1"],

  // Simplifying (0, someFunction)(variables) to someFunction(variables)
  ["\(\s*0\s*,\s*({$regexVariable}(?:\.{$regexVariable})*)\s*\)\s*\(\s*", "\$1("],
];
$content = replaceManyByRegex($replacements, $content);
simplifyMultiVariableAssignments();


function separateDefinitionFromContent($regex, $content, $onMatch) {
  global $definitionContent;
  global $content;
  $matches = [];
  preg_match_all($regex, $content, $matches);
  foreach ($matches[0] as $index => $match) {
    $definition = $matches[1][$index];
    $addition = $onMatch($definition);
    $definitionContent .= $addition;
  }
  $content = preg_replace($regex, "\n", $content);
  if ($content === null) {
    print("Error! Invalid regex!");
    print(get_last_preg_error());
    throw new Exception("Error!");
    exit;
  }
}

// New functions (that may begin with var)
$definitionContent .= "//New function.\n";
$regex = "/\\n(?:var\s+)?({$regexVariable}\s*=\s*{$regexAnonymousFunction});/";
separateDefinitionFromContent($regex, $content, function($definition) {
  return "var ${definition};\n";
});
$definitionContent .= "\n\n";


// New function in the underscore object
$definitionContent .= "//New function in underscore.\n";
$regex = "/\\n_\.({$regexVariable}\s*=\s*{$regexAnonymousFunction});/";
separateDefinitionFromContent($regex, $content, function($definition) {
  return "_.${definition};\n";
});
$definitionContent .= "\n\n";

// Empty variable declaration
$definitionContent .= "//Empty variable declarations.\n";
$regex = "/\\n(var\s+{$regexVariable});/";
separateDefinitionFromContent($regex, $content, function($definition) {
  return "${definition};\n";
});
$definitionContent .= "\n\n";


// Repeatedly move constants
$definitionContent .= "//New constants.\n";
$regex = "/\\n(?:var\s+)({$regexVariable}\s*=\s*(?:{$regexString}|false|true|{$regexNumber}|{$regexRegex}|\{\}));/";
do {
  preg_match_all($regex, $content, $matches);
  foreach ($matches[0] as $index => $match) {
    $definition = $matches[1][$index];
    $definitionContent .= "var ${definition};\n";
  }
  $content = preg_replace($regex, "", $content);
} while (isset($matches[0]) && count($matches[0]) > 0);
$definitionContent .= "\n\n";

simplifyMultiVariableAssignments();

// Move strings
$definitionContent .= "//Strings\n";
$regex = "/\\n((?:var\s+|_\.)?${regexVariable}\s*=\s*${regexString});/";
separateDefinitionFromContent($regex, $content, function($definition) {
  return "${definition};\n";
});
$definitionContent .= "\n\n";


$regex = "/\\n{4,}/";
$content = preg_replace($regex, "\n", $content);


// Automatic Constant Replacement (slowest so far)
if (AUTOMATIC_REPLACE_ENABLED) {
  $strings = [];
  $matches = [];
  $count = preg_match_all('/[^A-z_](_\.[_A-z]+)="([^"]{1,2})"/', $content, $matches);
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
    $substitute = '$1"' . $replacement[1] . '"$3';

    $content = preg_replace($regex, $substitute, $content);
    if ($content === null) {
      echo "Error!\n";
      print(get_last_preg_error());
      exit;
    }
  }
}

// Remove filler functions for older browsers.
$regex = "/\\n({$regexVariable}(?:\.{$regexVariable})*)\s*=\s*({$regexVariable}\.prototype\.{$regexVariable})\s*\?\s*{$regexAnonymousFunction}\s*:\s*{$regexAnonymousFunction}\s*;/";
$items = [];
$matches = [];
$count = preg_match_all($regex, $content, $matches);
if ($count === false) {
  print("Error! Invalid regex!");
  exit;
}
for ($i = 0; $i < $count; $i++) {
  $variableName = $matches[1][$i];
  $testValue  = $matches[2][$i];
  $items[] = [$variableName, $testValue];
}

$content = preg_replace($regex, '', $content);
if ($content === null) {
  echo "Error!\n";
  print(get_last_preg_error());
  exit;
}
foreach ($items as $item) {
  $item[0] = str_replace('.prototype', '', $item[0]);
  $regex = '/([^A-z0-9\n])(' . preg_quote($item[0]) . ')([^=A-z0-9-])/';
  $substitute = '$1' . $item[1] . '.call$3';

  $matches = [];
  preg_match_all($regex, $content, $matches);

  $content = preg_replace($regex, $substitute, $content);
  if ($content === null) {
    echo "Error!\n";
    print(get_last_preg_error());
    exit;
  }

  $definitionContent = preg_replace($regex, $substitute, $definitionContent);
  if ($definitionContent === null) {
    echo "Error!\n";
    print(get_last_preg_error());
    exit;
  }
}
print_r($items);
