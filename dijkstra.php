<?php
foreach (glob("src/*.php") as $class_file) {
    require $class_file;
}

foreach (glob("tests/*.php") as $class_file) {
    require $class_file;
}

foreach (glob("src/landmarks/*.php") as $class_file) {
    require $class_file;
}

$graph = new Graph($_POST['graph_data']);
if (isset($_POST['dij'])) {
	$n0 = $_POST['dij']['n0'];
	$n1 = $_POST['dij']['n1'];
	echo json_encode($graph->getShortestPath($n0, $n1));
}
die();