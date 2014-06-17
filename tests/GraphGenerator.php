<?php
/**
 * Random Graph Generator 
 */
class GraphGenerator {
	protected $max_x;
	protected $max_y;
	protected $node_list;

	public function __construct($max_x, $max_y) {
		$this->max_x = $max_x;
		$this->max_y = $max_y;
	}

	/**
	 * Generate a random graph Node & Edge
	 * @param  float $node_density determines on average how many nodes per square unit
	 * @return array              array of Node ob
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
		$this->node_list = $node_list;
		return $node_list;
	}
}