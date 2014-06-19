<?php
foreach (glob("src/*.php") as $class_file) {
    require $class_file;
}

foreach (glob("src/landmarks/*.php") as $class_file) {
    require $class_file;
}

foreach (glob("tests/*.php") as $class_file) {
    require $class_file;
}

function dirtyDebug($thing) {
	echo "<pre>".print_r($thing, true)."</pre>";
}

function debugLog($thing) {
	file_put_contents('debug_log', json_encode($thing, JSON_PRETTY_PRINT));
}
?>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="functions.js"></script>
</head>
<canvas id="mainCanvas" height="1000" width="1000"></canvas>
<canvas id="canvasOverlay" height="1000" width="1000"></canvas>