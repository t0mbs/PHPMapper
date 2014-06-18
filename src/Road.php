<?php
/**
 * Road class file
 */
class Road {
	public $name;
	public $nodes;

	public function __construct($name, $nodes) {
		$this->name = $name;
		$this->nodes = $nodes;
	}
}