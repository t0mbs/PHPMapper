<?php
/**
 * MapGenerator class file
 */
class MapGenerator extends GraphGenerator {
	private $roads = array();
	private $landmarks = array();
	public function __construct($x, $y) {
		parent::__construct($x, $y);
	}

	/**
	 * Generate a random Map made up of Nodes and Roads
	 * @return array          Array of Nodes and Roads from which to build a graph
	 */
	public function randomMap() {
		$nodes = $this->randomGraph();

		//Generates nameless roads
		foreach ($nodes as $n0) {
			foreach ($n0->related_nodes as $n1) {
				$n1 = $this->getNode($n1);
				if ($this->roadUnpaved($n0->key, $n1->key)) {
					$this->roads[] = new Road(
						'',
						$this->buildRoad($n0, $n1)
						);
				}
			}
		}

		//Generates road names
		foreach ($this->roads as $road) {
			$road->name = $this->generateName(count($road->nodes));
		}

		//Generates Landmarks
		for ($y = 0; $y < $this->max_y; $y++) {
			for ($x = 0; $x < $this->max_x; $x++) {
				if (!$this->nodeOnCoords($x, $y)) {
					$this->landmarks[] = new Park($x, $y); 
				}
			}
		}
		
		//Change node distance weights
		//IMPLEMENT

		return array(
			'roads' => $this->roads,
			'nodes' => $this->nodes,
			'landmarks' => $this->landmarks
			);
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

	private function nodeOnCoords($x, $y) {
		foreach ($this->nodes as $node) {
			if ($node->coords->x == $x && $node->coords->y == $y)
				return true;
		}
		return false;
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

				$road->nodes[] = $n0->key;
				return $road->nodes;
			}
		} else {
			return array($n0->key, $n1->key);
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
			if (in_array($n0, $road->nodes) && in_array($n1, $road->nodes)) {
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

	private function generateName($count) {
		$type_array = array(
			'l' => array(
				'lane',
				'alley'
				),
			'm' => array(
				'route',
				'road',
				'street'
				),
			'h' => array(
				'avenue',
				'boulevard'
				),
			'e' => array(
				'highway',
				'expressway'
				)
			);

		$name_array = array(
			'a' => array(
				'arlington'
				),
			'b' => array(
				'bowden'
				),
			'e' => array(
				'entropy'
				),
			'h' => array(
				'helmington'
				),
			'l' => array(
				'leitner'
				),
			'r' => array(
				'rascally'
				),
			's' => array(
				'spock'
				)
			);

		switch ($count) {
			case 2:
				$type = $type_array['l'][array_rand($type_array['l'])];
				break;
			case 3:
				$type = $type_array['m'][array_rand($type_array['m'])];
				break;
			case 4:
				$type = $type_array['h'][array_rand($type_array['h'])];
				break;
			default:
				$type = $type_array['e'][array_rand($type_array['e'])];
				break;
		}
		return ucwords($name_array[$type[0]][array_rand($name_array[$type[0]])] . ' ' . $type);
	}
}