<?php
/**
 * MapGenerator class file
 */
class MapGenerator extends GraphGenerator {
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
			foreach ($n0->related_nodes as $k1) {
				$n1 = $this->getNode($k1);
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
		$landmark_points = array();

		for ($y = 0; $y < $this->max_y; $y++) {
			for ($x = 0; $x < $this->max_x; $x++) {
				if (!$this->nodeOnCoords($x, $y) && !$this->edgeOnCoords(new Coordinates($x, $y))) {
					$landmark_points[] = new Coordinates($x, $y);

				}
			}
		}

		for ($i=count($landmark_points)-1; $i>=0; $i--) { 
			$point =& $landmark_points[$i];
			array_splice($landmark_points, $i);
			foreach ($this->linkLandmarkPoints($point, $landmark_points) as $point) {
				$this->landmarks[] = new Park($point->x, $point->y);
			}
		}

		return array(
			'roads' => $this->roads,
			'nodes' => $this->nodes,
			'landmarks' => $this->landmarks
			);
	}

	
	private function linkLandmarkPoints($p0, $landmark_points) {
		for ($i=count($landmark_points)-1; $i>=0; $i--) { 
			$p1 =& $landmark_points[$i];
			if (GraphCalc::areAdjacent($p0, $p1)) {
				array_splice($landmark_points, $i);
				return array_merge(array($p0), $this->linkLandmarkPoints($p1, $landmark_points));
			}
		}
		return array($p0);
	}

	/**
	 * Checks whether or not a node exists on a certain set of coordinates
	 * @param  int $x x coordinate
	 * @param  int $y y coordinate
	 * @return bool|Node    Node if node was found, false if not
	 */
	private function nodeOnCoords($x, $y) {
		foreach ($this->nodes as $n0) {
			if ($n0->coords->x == $x && $n0->coords->y == $y)
				return $n0;
		}
		return false;
	}

	/**
	 * Checks if there is an edge on specified coords
	 * @param  Coordinates $p1 the coordinates of the point to be tested
	 * @return bool    true if there is an edge on coords
	 */
	private function edgeOnCoords($p1) {
		$node_stack = array();
		for ($x=$p1->x-1; $x<=$p1->x+1; $x++) {
			for ($y=$p1->y-1; $y<=$p1->y+1; $y++) {
				$n0 = $this->nodeOnCoords($x, $y);
				if ($n0) {
					$node_stack['nodes'][] = $n0;
					$node_stack['keys'][] = $n0->key;
				}
			}
		}
		foreach ($node_stack['nodes'] as $n0) {
			foreach($n0->related_nodes as $k2) {
				$n2 = $this->getNode($k2);
				if (in_array($n2->key, $node_stack['keys'])) {
					$p2 = $n2->coords;
					if (GraphCalc::isOnLine($n0->coords, $p1, $p2)) return true;
				}
 			}
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
		foreach ($n1->related_nodes as $k2)  {
			$n2 = $this->getNode($k2);
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

	/**
	 * Generates a random alliterated road name
	 * @param  int $count the Node count, used to determine the road's type
	 * @return string        the name of the road
	 */
	private function generateName($count) {
		$type_array = array(
			'l' => array(
				'lane',
				'alley',
				'drive'
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
				'aardvark',
				'avuncular',
				'anthology',
				'aggregate'
				),
			'b' => array(
				'bowden',
				'balrog',
				'bacon',
				'boolean',
				'belligerent',
				'buck'
				),
			'd' => array(
				'differential',
				'dalek'
				),
			'e' => array(
				'entropy',
				'einstein',
				'eleonore'
				),
			'h' => array(
				'hogwarts',
				'hedonist'
				),
			'l' => array(
				'leitner',
				'lemington'
				),
			'r' => array(
				'ragtime',
				'ramen',
				'rubic',
				'robin',
				'radial'
				),
			's' => array(
				'spock',
				'shatner',
				'string',
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