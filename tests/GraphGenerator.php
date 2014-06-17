<?php
/**
 * Random Graph Generator 
 */
class GraphGenerator {
	protected $max_x;
	protected $max_y;

	public function __construct($max_x, $max_y) {
		$this->max_x = $max_x;
		$this->max_y = $max_y;
	}

	/**
	 * Generate a random graph as a JSON file
	 * @param  float $node_density determines on average how many nodes per square unit
	 */
	public function randomGraph($node_density) {
		$node_count = $this->max_x * $this->max_y * $node_density;
		$node_list = array();
		for($i = 0; $i <= $node_count; $i++) {
			$connected_nodes = array();
			for ($j = 0; $j <= rand(2, 6); $j++) {
				$r = rand(0, $node_count);
				if($r != $i) $connected_nodes[] = $r;
			}
			$node_list[] = new Node(
				$i, 
				rand(0, $this->max_x), 
				rand(0, $this->max_y), 
				$connected_nodes
				);
		}
		file_put_contents(
			'resources/rand-' . time() . '.json', 
			json_encode($node_list, JSON_PRETTY_PRINT)
			);
	}
	/*
	Lets set max-X to 100, max-Y to 100
	What does a Graph need?
	1. Nodes
		* names
		* coordinates
		* links
	 */
}