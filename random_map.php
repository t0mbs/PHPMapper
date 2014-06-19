<?php
foreach (glob("src/*.php") as $class_file) {
    require_once $class_file;
}

foreach (glob("tests/*.php") as $class_file) {
    require_once $class_file;
}

foreach (glob("src/landmarks/*.php") as $class_file) {
    require_once $class_file;
}

$rand_graphs = new MapGenerator(20, 20);
echo json_encode($rand_graphs->randomMap());
die();