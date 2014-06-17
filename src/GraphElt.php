<?php
/**
 * GraphElt class file
 */
abstract class GraphElt {
	public $coordinates;

	/**
	 * Sets an element's coordinates
	 * @param int $x the x coordinates
	 * @param int $y the y coordinates
	 */
	public function setCoordinates($x, $y) {
		$this->coordinates = new Coordinates($x, $y);
	}
}