<?php
/**
 * Random Graph Generator 
 */
class GraphGenerator {
	protected $max_x;
	protected $max_y;
	protected $nodes;

	public function __construct($max_x, $max_y) {
		$this->max_x = $max_x;
		$this->max_y = $max_y;
	}

	/**
	 * Generate a random graph made up of Nodes
	 * @return array              array of Node objects
	 */
	public function randomGraph() {
		//Cannot have overlapping nodes
		$nodes = array();

		$key = 0;
		for ($y = 0; $y < $this->max_y; $y++) {
			for ($x = 0; $x < $this->max_x; $x++) {
				if (rand(0, 1) == 1) {
					$x += 1;
					$nodes[] = new Node(
						$key, 
						$x, 
						$y, 
						array()
						);
					$key ++;
				}
			}
		}

		foreach ($nodes as $n0) {
			$distance_array = array();
			foreach($nodes as $n1) {
				$distance_array[$n1->key] =
					GraphCalc::getDistance($n0->coordinates, $n1->coordinates);
			}
			asort($distance_array);

			$i = 0;
			foreach($distance_array as $n1 => $distance) {
				if (count($n0->related_nodes) >= 4) break;
				if ($i != 0 && !in_array($n1, $n0->related_nodes)) {
					$n0->related_nodes[] = $n1;
					$nodes[$n1]->related_nodes[] = $n0->key;
				}
				$i++;
			}
		}

		$this->nodes = $nodes;
		return $nodes;
	}
}