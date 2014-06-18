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

		//Generating nodes linearly with a probability of 0.6
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

		//Connecting nodes based on proximity
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

			$max_count = 4;
			if ($n0->coords->x == 0 || $n0->coords->x == $this->max_x-1) 
				$max_count -= 1;
			if ($n0->coords->y == 0 || $n0->coords->y == $this->max_y-1) 
				$max_count -= 1;

			foreach($distance_array as $n1 => $distance) {
				if (count($n0->related_nodes) >= $max_count) break;
				if (!in_array($n1, $n0->related_nodes) && $this->unalignedNode($n0, $n1)) {
					$n0->related_nodes[] = $n1;
					$this->nodes[$n1]->related_nodes[] = $n0->key;
				}
			}
		}
		return $this->nodes;
	}

	/**
	 * Checks that node n1 is unaligned with any nodes related to n0
	 * @param  Node $n0 the first node
	 * @param  Node $n1 the first node
	 * @return bool 	true if n1 is unaligned
	 */
	private function unalignedNode($n0, $n1) {
		if(count($n0->related_nodes) == 0) return true;

		$n1 = $this->nodes[$n1];
		foreach ($n0->related_nodes as $n2) {
			$n2 = $this->nodes[$n2];
			$same_x_dir = (($n0->coords->x - $n2->coords->x) * ($n1->coords->x - $n0->coords->x)) < 0;
			$same_y_dir = (($n0->coords->y - $n2->coords->y) * ($n1->coords->y - $n0->coords->y)) < 0;
			
			if (($n1->coords->x == $n2->coords->x && $n2->coords->x ==$n0->coords->x && $same_y_dir)
				|| ($n1->coords->y == $n2->coords->y && $n2->coords->y ==$n0->coords->y && $same_x_dir)) {
				return false;
			}
		}
		return true;
	}
}