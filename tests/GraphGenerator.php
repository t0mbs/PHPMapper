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
	 * @param  float $density determines on average how many nodes per square unit
	 * @return array              array of Node objects
	 */
	public function randomGraph($density) {
		//Cannot have overlapping nodes
		while ($density > 1) {
			$density /= 10;
		}

		$node_count = $this->max_x * $this->max_y * $density;
		$nodes = array();
		$x = rand(0, $this->max_x);
		$y = rand(0, $this->max_y);

		$taken_coords = array();
		$connected_nodes = array();
		for($i = 1; $i <= $node_count; $i++) {
			while(
				array_key_exists($x, $taken_coords) &&
				in_array($y, $taken_coords[$x])
				) {
				$x = rand(0, $this->max_x);
				$y = rand(0, $this->max_y);
			}
			$taken_coords[$x][] = $y;


			$nodes[] = new Node(
				$i, 
				rand(0, $this->max_x), 
				rand(0, $this->max_y), 
				array()
				);
		}

		for($i = 0; $i < count($nodes) - 1; $i += 1) {
			$n0 = $nodes[$i];
			do {
				for ($j = $i+1; $j < count($nodes); $j += 1) {
					$n1 = $nodes[$j];
					$x_diff = abs($n0->coordinates->x - $n1->coordinates->x);
					$y_diff = abs($n0->coordinates->y - $n1->coordinates->y);
					$prob = 1 - ($x_diff/$this->max_x + $y_diff/$this->max_y)/2;

					if (rand(1, 100) > $prob*50) {
						$n0->related_nodes[] = $n1->key;
						$n1->related_nodes[] = $n0->key;
					}
				}
			} while (count($n0->related_nodes) < 2);
		}

		$this->nodes = $nodes;
		return $nodes;
	}
}