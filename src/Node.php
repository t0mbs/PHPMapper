<?php
/**
 * Node class file
 */
class Node extends GraphElt {
	public $key;
	public $related_nodes;

	public function __construct($key, $x, $y, $related_nodes) {
		$this->key = $key;
		$this->coordinates = new Coordinates($x, $y);
		$this->related_nodes = $related_nodes;
	}
}