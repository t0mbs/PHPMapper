<?php
/**
 * Graph class file
 */
class Graph {
	public $roads = array();
	public $landmarks = array();
	public $nodes = array();

	public function __construct($graph_data) {
		$this->loadGraph($graph_data);
		$this->compileEdgeLength();
	}

	/**
	 * Loads graph's nodes, roads and landmarks from a specified JSON file
	 * @param  array $graph_data the 
	 */
	protected function loadGraph($graph_data) {
		foreach($graph_data['roads'] as $road) {
			$this->roads[] = new Road(
				$road['name'],
				$road['nodes']
				);
		}

		foreach($graph_data['nodes'] as $node) {
			$this->nodes[] = new Node(
				$node['key'],
				$node['coords']['x'],
				$node['coords']['y'],
				$node['related_nodes']
				);
		}
	}

	/**
	 * Compiles the length of each node's edges using this and the relevant 
	 * node's coords
	 */
	protected function compileEdgeLength() {
		foreach ($this->nodes as $node) {
			$weighed_related_nodes = [];
			foreach ($node->related_nodes as $rel_node_key) {
				$weighed_related_nodes[$rel_node_key] = 
					GraphCalc::getDistance($node->coords, $this->nodes[$rel_node_key]->coords);
			}
			$node->related_nodes = $weighed_related_nodes;
		}
	}

	/**
	 * An implementation of Dijkstra's graph traversal algorithm
	 * @param  string $start_node The node key from which to start
	 * @param  string $end_node   The node key at which we end
	 * @return array             The trace of the shortest path
	 */
	public function getShortestPath($start_node, $end_node) {	

		//Instantiate array of node keys to default distance (INF)
		foreach ($this->nodes as $key => $value){
			$node_stack[$key]['weight'] = INF;
		}

		//Set initial values
		$current_key = $start_node;
		$node_stack[$current_key]['weight'] = 0;
		$node_stack[$current_key]['trace'][$current_key] = 0;

		while (isset($node_stack[$end_node]) && $current_key != $end_node) {
			//Symlink because these paths are too damn long!
			$related_nodes =& $this->nodes[$current_key]->related_nodes;

			//set new weights
			foreach($related_nodes as $linked_key => $linked_weight) {
				$new_weight = $node_stack[$current_key]['weight'] + $linked_weight;
				if (isset($node_stack[$linked_key]) && 
					$node_stack[$linked_key]['weight'] > $new_weight) {
					$node_stack[$linked_key]['weight'] = $new_weight;
					$node_stack[$linked_key]['trace'] = $node_stack[$current_key]['trace'];
					$node_stack[$linked_key]['trace'][$linked_key] = $linked_weight;
				}
			}
			unset($node_stack[$current_key]);

			//find next lowest weighed node
			$total_distance = NULL;
			foreach($node_stack as $key => $value) {
				if($value['weight'] < $total_distance || is_null($total_distance)) {
					$current_key = $key;
					$total_distance = $value['weight'];
				}
			}
		}
		return $node_stack[$end_node];
	}
}