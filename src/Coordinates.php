<?php
/**
 * Coordinates class file
 */
class Coordinates {
	public $x;
	public $y;
	public function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}
	/**
	 * Establish the distance between two points using the distance formula
	 * @param  Coordinates $coordinates the coordinates to compare with
	 * @return float              returns a float of the distance, rounded to two digis
	 */
	public function getDistance($coordinates) {
		return round(
			sqrt(
				pow(($coordinates->x - $this->x), 2) + 
				pow(($coordinates->y - $this->y), 2)
				),
			2
			);
	}
}