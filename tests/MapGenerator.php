<?php
/**
 * MapGenerator class file
 */
class MapGenerator extends GraphGenerator {
	private $roads = array();
	public function __construct($x, $y) {
		parent::__construct($x, $y);
	}

	/**
	 * Generate a random Map made up of Nodes and Roads
	 * @return array          Array of Nodes and Roads from which to build a graph
	 */
	public function randomMap() {
		$nodes = $this->randomGraph();

		//run through each node
		foreach ($nodes as $n0) {
			//run through each node's related
			foreach ($n0->related_nodes as $n1) {
				$n1 = $this->getNode($n1);
				if ($this->roadUnpaved($n0->key, $n1->key)) {
					$this->roads[] = $this->buildRoad($n0, $n1);
				}
			}
		}
		return array(
			'roads' => $this->roads,
			'nodes' => $this->nodes
			);
	}

	/**
	 * Recursive function that builds a 2 or more point road array from two nodes
	 * @param  Node $n0 first Node
	 * @param  Node $n1 second Node
	 * @return array     an array of node keys which
	 */
	private function buildRoad($n0, $n1) {
		$fin_angle = INF;
		foreach ($n1->related_nodes as $n2)  {
			$n2 = $this->getNode($n2);
			$angle = abs(180 - GraphCalc::getAngle(
						$n0->coords,
						$n1->coords,
						$n2->coords
						));

			if ($angle < $fin_angle) {
				$fin_angle = $angle;
				$fin_n2 = $n2;
			}
		}
		$angle = $fin_angle;
		$n2 = $fin_n2;

		if ($angle == 0) {
			if ($this->roadUnpaved($n0->key, $n1->key, $n2->key)) {
				$x = $this->buildRoad($n1, $n2);
				array_unshift($x, $n0->key);
				return $x;
			} else {
				$road_key = $this->getRoad($n1->key, $n2->key);
				$road = $this->roads[$road_key];
				unset($this->roads[$road_key]);

				$road[] = $n0->key;
				return $road;
			}
		} else {
			return array($n0->key, $n1->key);
		}
	}

	/**
	 * Helper function to get a node from nodes based on its key
	 * @param  int|string $search_key the key
	 * @return Node             the node Object
	 */
	private function getNode($search_key) {
		foreach ($this->nodes as $n0) {
			if ($n0->key == $search_key) {
				return $n0;
			}
		}
	}

	/**
	 * Looks for a road containing the two nodes
	 * @param  int $n0 key of the first node
	 * @param  int $n1 key of the second node
	 * @return int|null     either the key of the road or null
	 */
	private function getRoad($n0, $n1) {
		if (count($this->roads) == 0) return NULL;

		foreach ($this->roads as $key => $road) {
			if (in_array($n0, $road) && in_array($n1, $road)) {
				return $key;
			}
		}
		return NULL;
	}

	/**
	 * Checks if 2 or 3 node road already exists
	 * @param  int $n0 key of the first node
	 * @param  int $n1 key of the second node
	 * @param  int $n2 optional key of the third node
	 * @return bool     true if road exists, false if not
	 */
	private function roadUnpaved ($n0, $n1, $n2 = NULL) {
		if ($n2 === NULL) {
			return ($this->getRoad($n0, $n1) === NULL);
		} else {
			return ($this->getRoad($n0, $n1) === NULL 
				&& $this->getRoad($n1, $n2) === NULL
				);
		}
	}
}