<?php
/**
 * Graph class file
 */
class Graph {
	public $node_list;

	public function __construct(string $map_name) {
		$this->loadMap($map_name);
		$this->compileEdgeLength();
	}
	/**
	 * Loads map's nodes from a specified JSON file
	 * @param  string $map_name the 
	 */
	protected function loadMap(string $map_name) {
		$raw_map = json_decode("../resources/$map_name.json");
		foreach ($raw_nodes as $node_key => $node) {
			array_push($this->node_list, 
				new Node(
					$node_key,
					$node->coordinates->x,
					$node->coordinates->y,
					$node->related_nodes
					)
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
					$node->coordinates->getDistance($node_list[$rel_node_key]);
			}
		}
	}
}