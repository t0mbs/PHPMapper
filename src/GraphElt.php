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

	/**
	 * Establish the distance between two points using the distance formula
	 * @param  Coordinates $coordinates the coordinates to compare with
	 * @return float              returns a float of the distance, rounded to two digis
	 */
	public function getDistance($coordinates) {
		return round(
			sqrt(
				pow(($coordinates->x - $this->coordinates->x), 2) + 
				pow(($coordinates->y - $this->coordinates->y), 2)
				),
			2
			);
	}
}