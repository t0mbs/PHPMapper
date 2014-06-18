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
		$exclusion_array = array();
		$roads = array();

		foreach ($nodes as $node) {
			$related_nodes = $node->related_nodes;
			foreach ($related_nodes as $key => $rel) {
				unset($related_nodes[$key]);

				if (!array_key_exists($rel, $exclusion_array) || 
					!in_array($node->key, $exclusion_array[$rel])) {

					$min_angle = INF;
					foreach ($related_nodes as $rel2) {
						//Get angle $rel - $node - $rel2
						$angle = abs(180 - 
							GraphCalc::getAngle(
								$this->getCoords($rel),
								$node->coordinates,
								$this->getCoords($rel2)
								)
							);

						if ($angle < $min_angle) {
							$min_angle = $angle;
							$min_rel2 = $rel2;
						}
					}
					$rel2 = $min_rel2;

					//If abs(180-angle) <= 30, a 3 point road can be made / appended
					if ($min_angle <= 30) {

						//If rel & node are already in a road, rel2 can be appended to that road
						$road_match = false;
						foreach ($roads as $key => $road) {
							foreach($road as $skey => $road_node) {
								if ($road_node == $rel && array_key_exists($skey + 1, $road) && $node->key == $road[$skey+1]) {
									$roads[$key][] = $rel2;
									$road_match = true;
									break 2;
								}
							}
						}

						//Else make a new three point road rel-node-rel2
						if (!$road_match) {
							$roads[] = array($rel, $node->key, $rel2);
						}

					} else {
						//Two new roads 2pt roads
						$roads[] = array($rel, $node->key);
						$roads[] = array($node->key, $rel2);
					}

					//Add points to the exclusion array
					$exclusion_array[$node->key][] = $rel;
					$exclusion_array[$node->key][] = $rel2;

				} //endof exclusion_array if
			} //endof node->related_nodes foreach
		} //endof nodes foreach
		return array(
			'roads' => $roads,
			'nodes' => $this->nodes
			);
	}

	/**
	 * Helper function to get a node's coordinates from nodes based on its key
	 * @param  int|string $search_key the key
	 * @return Coordinates             the node's coordinates
	 */
	private function getCoords($search_key) {
		foreach ($this->nodes as $node) {
			if ($node->key == $search_key) {
				return $node->coordinates;
			}
		}
	}
}