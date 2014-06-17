<?php
/**
 * Node class file
 */
class Node extends GraphElt {
	public $key;
	public $related_nodes;

	public function __construct($key, $x, $y, $related_nodes) {
		$this->key = $key;
		$this->related_nodes = $related_nodes;
		$this->setCoordinates($x, $y);
	}
}