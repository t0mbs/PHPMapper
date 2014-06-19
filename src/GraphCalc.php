<?php
/**
 * GraphCalc class file
 */
class GraphCalc {
	public $instance;

	private function __construct() {

	}

	/**
	 * Standard Singleton instantiator
	 * @return GraphCalc	returns the single instance of GraphCalc
	 */
	public static function getInstance($rounding) {
		if (!isset($this->instance))
			$this->instance = new GraphCalc($rounding);
		return $this->instance;
	}

	/**
	 * Establish the distance between two points using the distance formula
	 * @param  Coordinates $p0 the coordinates of the first point
	 * @param  Coordinates $p1 the coordinates of the second point
	 * @return float       returns a float of the distance
	 */
	public static function getDistance($p0, $p1) {
		return sqrt(pow ($p1->x - $p0->x, 2) + pow ($p1->y - $p0->y, 2));
	}

	/**
	 * Calculates the angle between three sets of coordinates in degrees
	 * @param  Coordinates $p0 the coordinates of the first point
	 * @param  Coordinates $p1 the coordinates of the second point and the angle's apex
	 * @param  Coordinates $p2 the coordinates of the third point
	 * @return float     	Returns the angle who's apex is p1 in degrees
	 */
	public static function getAngle($p0, $p1, $p2) {
		//Avoid duplicate points (i.e. division by zero)
		if ($p0 == $p1 || $p1 == $p2 || $p2 == $p0) return 90;

		$a = pow($p1->x - $p0->x, 2) + pow($p1->y - $p0->y, 2);
		$b = pow($p1->x - $p2->x, 2) + pow($p1->y - $p2->y, 2);
		$c = pow($p2->x - $p0->x, 2) + pow($p2->y - $p0->y, 2);

		return acos(($a + $b - $c) / sqrt(4 * $a * $b)) * 180/pi();
	}

	public static function isRight($p0, $p1, $p2) {
		return (($p1->x - $p0->x)*($p2->y - $p0->y) - ($p1->y - $p0->y)*($p2->x - $p0->x)) < 0;
	}
}