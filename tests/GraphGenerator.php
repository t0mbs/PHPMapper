<?php
/**
 * Random Graph Generator 
 */
class GraphGenerator {
	protected $max_x;
	protected $max_y;
	protected $nodes = array();

	public function __construct($max_x, $max_y) {
		$this->max_x = $max_x;
		$this->max_y = $max_y;
	}

	/**
	 * Generate a random graph made up of Nodes
	 * @return array              array of Node objects
	 */
	public function randomGraph() {
		$key = 0;
		for ($y = 0; $y < $this->max_y; $y++) {
			for ($x = 0; $x < $this->max_x; $x++) {
				if (rand(0, 4) < 3) {
					$this->nodes[] = new Node(
						$key, 
						$x, 
						$y, 
						array()
						);
					$key ++;
				}
			}
		}

		for ($i=0; $i<count($this->nodes); $i++) {
			$distance_array = array();
			$n0 = $this->nodes[$i];
			for ($j=0; $j<count($this->nodes); $j++) {
				$n1 = $this->nodes[$j];
				if ($n1 != $n0) {
				$distance_array[$n1->key] =
					GraphCalc::getDistance($n0->coords, $n1->coords);
				}
			}
			asort($distance_array);

			foreach($distance_array as $n1 => $distance) {
				$max_count = 4;
				if ($n0->coords->x == 0 || $n0->coords->x == $this->max_x-1) 
					$max_count -= 1;
				if ($n0->coords->y == 0 || $n0->coords->y == $this->max_y-1) 
					$max_count -= 1;

				if (count($n0->related_nodes) >= $max_count) break;
				if (!in_array($n1, $n0->related_nodes)) {
					$n0->related_nodes[] = $n1;
					$this->nodes[$n1]->related_nodes[] = $n0->key;
				}
			}
		}
		return $this->nodes;
	}
}