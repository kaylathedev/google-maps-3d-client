<?php
require 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
<link href="res/styles.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
var lat = -76.7128387, lon = 39.8836807;

var map_url = ["/maps/_/js/","m","maps.m.en.<?=$variable[0]?>.O","<?=$variable[1]?>"]; // Unsure of its real purpose
window.APP_OPTIONS=[,,,[,,,,,,[[""],,[""],,,,,"/maps/vt"],,,,,,,"//kh.google.com/rt/earth"],,,,,,,,,,,,,,,,,,,,,,[1,],,,,,,,,,,,,,,map_url,,,,,,,,,,,,,,,,,,,1];
window.APP_INITIALIZATION_STATE=[[[208.0,lat,lon]],[[["m"]]],,,,[,,[3,[,lat,lon,,208.0,,20.0,46.18,55.71]]],,,[59,60,61,63,66,65]];
</script>
<base href="https://www.google.com/maps">
</head>
<body>

<script type="text/javascript">
<?php require 'res/temp-script-processed.js'; ?>

</script>

</body>
</html>
