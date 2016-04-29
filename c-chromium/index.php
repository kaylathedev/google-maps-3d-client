<?php
$variable = [
  "HbC7FRYovGY",
  "ACT90oEkumd7ZZHZBfs7AsobzkUGs6LJNA"
]; // These seem to change daily.

function get_last_preg_error() {
  return array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
}

$base = 'https://www.google.com';
$scriptFilename = "$base/maps/_/js/k=maps.m.en.{$variable[0]}.O/m=b/d=1/rs={$variable[1]}";
$scriptContent = file_get_contents($scriptFilename);
file_put_contents('temp-script-original.js', $scriptContent);

function processGoogleMapsScript($content) {
  $replacements = [
    ['/\ntry\{\n/',                                  "\n"], // Removes top-level try blocks
    ['/\n\}catch\(e\)\{_\._DumpException\(e\)\}\n/', "\n"], // Removes top-level catch blocks
  ];

  foreach ($replacements as $replacement) {
    $regex = $replacement[0];
    $substitute = $replacement[1];

    $content = preg_replace($regex, $substitute, $content);
    if ($content === null) {
      print(get_last_preg_error());
      exit;
    }
  }

  $strings = [
    ['raa', '-z'],
    ['qa', '.'],
    ['saa', '..'],
    ['taa', '...'],
    ['uaa', '..='],
    ['sa', '/'],
    ['sa', '/'],
    ['ua', '0'],
    ['va', '8'],
    ['wa', '9'],
    ['xa', ':'],
    ['ya', '<'],
    ['za', '<br>'],
    ['Aa', '='],
    ['Ca', '>'],
    ['Da', '?'],
    ['Ea', '@'],
  ];

  foreach ($strings as $replacement) {
    $regex = '/([^A-z0-9])(_\.' . $replacement[0] . ')([^=A-z0-9-])/';
    $substitute = '$1"' . $replacement[1] . '"$3';

    $content = preg_replace($regex, $substitute, $content);
    if ($content === null) {
      print(get_last_preg_error());
      exit;
    }
  }


  return $content;
}

$scriptContent = processGoogleMapsScript($scriptContent);
file_put_contents('temp-script-processed.js', $scriptContent);


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
#scene{position:absolute;left:0px;width:100%;bottom:0;background-color:#ede9dd;z-index:0}
</style>



<script>

<?php
echo $scriptContent;
?>

</script>



</body>
</html>
