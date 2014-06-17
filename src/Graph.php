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
			$weighed_related_nodes = [];
			foreach ($node->related_nodes as $rel_node_key) {
				$weighed_related_nodes[$rel_node_key] = 
					$node->coordinates->getDistance(
						$this->node_list[$rel_node_key]->coordinates
						);
			}
			$node->related_nodes = $weighed_related_nodes;
		}
	}

	/**
	 * An implementation of Dijkstra's graph traversal algorithm
	 * @param  string $start_node The node key from which to start
	 * @param  string $end_node   The node key at which we end
	 * @return array             The trace of the paths we took
	 */
	public function getShortestPath($start_node, $end_node) {		
		//Instantiate array of node keys to default distance (NULL)
		foreach ($this->node_list as $key => $value){
			$dijkstras_nodes[$key] = INF;
		}

		//Start Node search at the specified start node
		$current_key = $start_node;
		$dijkstras_nodes[$current_key] = 0;

		while (isset($dijkstras_nodes[$end_node]) && $current_key != $end_node) {
			//Symlink because these paths are too damn long!
			$related_nodes =& $this->node_list[$current_key]->related_nodes;

			//set new weights
			foreach($related_nodes as $linked_key => $linked_weight) {
				$new_weight = $dijkstras_nodes[$current_key] + $linked_weight;
				if (isset($dijkstras_nodes[$linked_key]) && 
					$dijkstras_nodes[$linked_key] > $new_weight)
					$dijkstras_nodes[$linked_key] = $new_weight;
			}
			unset($dijkstras_nodes[$current_key]);

			//find next lowest weighed node
			$total_distance = NULL;
			foreach($dijkstras_nodes as $key => $value) {
				if($value < $total_distance || is_null($total_distance)) {
					$current_key = $key;
					$total_distance = $value;
				}
			}
		}
		echo "Total Distance = $total_distance";

		$shortestPath = array('Implement');
		return $shortestPath;
	}

	/*
	REMOVE
	 */
	public function dirtyDebug($thing) {
		echo "<pre>".print_r($thing, true)."</pre>";
	}
}