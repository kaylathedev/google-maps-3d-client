<?php
set_time_limit(60);

$variable = [
  "HbC7FRYovGY",
  "ACT90oEkumd7ZZHZBfs7AsobzkUGs6LJNA"
]; // These seem to change daily.

function get_last_preg_error() {
  return array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
}

if (isset($_GET['cache']) && $_GET['cache']) {
  $content = file_get_contents('temp-script-processed.js');
} else {
  if (file_exists('temp-script-original.js')) {
    $content = file_get_contents('temp-script-original.js');
  } else {
    $base = 'https://www.google.com';
    $scriptFilename = "$base/maps/_/js/k=maps.m.en.{$variable[0]}.O/m=b/d=1/rs={$variable[1]}";
    $content = file_get_contents($scriptFilename);
    file_put_contents('temp-script-original.js', $content);
  }
  $content = processGoogleMapsScript($content);
  file_put_contents('temp-script-processed.js', $content);
}

function generateSpecificCSSRegex() {
  $numeric = '([0-9]+(px|%|em)|0)';
  $numericRules = "((top|left|right|bottom|width|height|z-index|margin|padding):$numeric(\s+$numeric)*);";
  return $numericRules;
}

function generateCSSRegex() {
  $className = '(?<class>\.[a-z]+(?:-[a-z]+)*)';
  $idName = '(?<id>#[a-z]+(?:-[a-z]+)*)';
  $tagName = '(?<tag>[a-z]+(?:-[a-z]+)*)';
  $name = "($className|$idName|$tagName)";
  $manyNames = "($name(\s*$name)*)";

  $ruleName = "[a-z]+(?:-[a-z]+)*";
  $ruleValue = "[-0-9A-z]+";
  $rule = "($ruleName\s*:\s*$ruleValue)";
  $manyRules = "($rule((\s*;\s*$rule)+)?;?)";

  $definition = "$manyNames\s*\{$manyRules\}";
  return $definition;// .name {test:test;}
}

function processGoogleMapsScript($content) {

  $numeric = '([0-9]+(px|%|em)|0)';
  $singleValue = "($numeric|auto)";
  $manyValues = "($singleValue(\s+$singleValue)*)";
  $value = "($manyValues(\s*!important)?)";
  $colon = '\s*:\s*';

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
  ];
  foreach ($replacements as $replacement) {
    $search = $replacement[0];
    $substitute = $replacement[1];

    $content = str_replace($search, $substitute, $content);
  }


  // Regex replacments
  $replacements = [
    ["border(-(top|left|right|bottom))?(-[a-z]+)?${colon}[^;\"\{\}]+;", ""],
    //['/\n\}\)\(this\._\);\n/', "\n"], // Removes last function definition
    ["((top|left|right|bottom|width|height|z-index|margin|padding):$value;)", ""],
    ["((top|left|right|bottom|width|height|z-index|margin|padding):$value\}\")", "\}\""],
    ["(((background|border)-)?color)${colon}(#[0-9a-f]{3,6}|[a-z]+);?", ""],
    ["((text|vertical)-align)${colon}(center|left|right|top|bottom);?", ""],
    ["#[A-Fa-f0-9]{6}", ""],
    ["", ""],
    ["", ""],
  ];
  foreach ($replacements as $replacement) {
    $regex = $replacement[0];
    $substitute = $replacement[1];

    $content = preg_replace('/' . $regex . '/', $substitute, $content);
    if ($content === null) {
      print(get_last_preg_error());
      exit;
    }
  }


  /*$strings = [];
  $matches = [];
  $count = preg_match_all('/[^A-z](_\.[_A-z]+)="([^"]{1,12})"/', $content, $matches);
  if ($count === false) {
    print("Error! Invalid regex!");
    exit;
  }
  for ($i = 0; $i < $count; $i++) {
    $variableName = $matches[1][$i];
    $stringValue  = $matches[2][$i];
    $strings[] = [$variableName, $stringValue];
  }
  //print_r($strings);exit;
  foreach ($strings as $replacement) {
    $regex = '/([^A-z0-9])(_\.' . preg_quote($replacement[0]) . ')([^=A-z0-9-])/';
    $substitute = '$1"' . preg_quote($replacement[1]) . '"$3';

    $content = preg_replace($regex, $substitute, $content);
    if ($content === null) {
      print(get_last_preg_error());
      exit;
    }
  }*/

  require 'format.php';

  $jsb = new JSBeautifier();
  $opts = new BeautifierOptions();

  $opts->indent_with_tabs = true;
  $content = $jsb->beautify($content, $opts);
  return $content;
}


?>
<!DOCTYPE html>
<html>
<head>
<base href="https://www.google.com/maps">
<script type="text/javascript">
var lat = -76.7128387, lon = 39.8836807;

var map_url = ["/maps/_/js/","m","maps.m.en.<?=$variable[0]?>.O","<?=$variable[1]?>"]; // Unsure of its real purpose
window.APP_OPTIONS=[,,,[,,,,,,[[""],,[""],,,,,"/maps/vt"],,,,,,,"//kh.google.com/rt/earth"],,,,,,,,,,,,,,,,,,,,,,[1,],,,,,,,,,,,,,,map_url,,,,,,,,,,,,,,,,,,,1];
window.APP_INITIALIZATION_STATE=[[[208.0,lat,lon]],[[["m"]]],,,,[,,[3,[,lat,lon,,208.0,,20.0,46.18,55.71]]],,,[59,60,61,63,66,65]];
</script>
</head>
<body>
<style type="text/css">
html, body {
  margin: 0;
  padding: 0;

  height: 100%;
  width: 100%;
}
jsl, #app-container, #content-container, #scene, .widget-scene {
  height: 100%;
  width: 100%;
}
#scene {
  position:absolute;
  left:0px;
  bottom:0;
  background-color: #ede9dd;
  z-index:0;
}
</style>



<script>

<?php
echo $content;
?>

</script>



</body>
</html>
