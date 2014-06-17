<?php
/**
 * Graph class file
 */
class Graph {
	public $node_list = array();

	public function __construct($map_name) {
		$this->loadMap($map_name);
		$this->compileEdgeLength();
	}
	/**
	 * Loads map's nodes from a specified JSON file
	 * @param  string $map_name the 
	 */
	protected function loadMap($map_name) {
		$raw_nodes = json_decode(file_get_contents("resources/$map_name.json"));
		foreach ($raw_nodes as $node_key => $node) {
			$this->node_list[$node_key] = 
				new Node(
					$node_key,
					$node->coordinates[0],
					$node->coordinates[1],
					$node->related_nodes
					);
		}
	}
	/**
	 * Compiles the length of each node's edges using this and the relevant 
	 * node's coordinates
	 */
	protected function compileEdgeLength() {
		foreach ($this->node_list as $node) {
			foreach ($node->related_nodes as $rel_node_key) {
				$node->related_nodes[$rel_node_key] = 
					$node->coordinates->getDistance(
						$this->node_list[$rel_node_key]->coordinates
						);
			}
		}
	}

	public function getShortestPath($start_node, $end_node) {
		$shortestPath = 'Implement';
		return $shortestPath;
	}
}