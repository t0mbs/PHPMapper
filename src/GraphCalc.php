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

	/**
	 * Takes three sets of coordinates and returns the dot product
	 * ==0: straight line
	 * > 0: left turn
	 * < 0: right turn
	 * @param  Coordinates $p0 first set of Coordinates
	 * @param  Coordinates $p1 second set of Coordinates
	 * @param  Coordinates $p2 third set of Coordinates
	 * @return int    returns the dot product of the three point's coordinates
	 */
	public static function getDotProduct($p0, $p1, $p2) {
		return (($p1->x - $p0->x)*($p2->y - $p0->y) - ($p1->y - $p0->y)*($p2->x - $p0->x));
	}

	/**
	 * Checks if dot product is equal to 0 and thus, if the line is straight
	 * @param  Coordinates  $p0 first set of Coordinates
	 * @param  Coordinates  $p1 second set of Coordinates
	 * @param  Coordinates  $p2 third set of Coordinates
	 * @return bool     true if the line is straight
	 */
	public static function isOnLine($p0, $p1, $p2) {
		return  self::getDotProduct($p0, $p1, $p2) == 0;
	}

	/**
	 * Checks if dot product is smaller than 0 and thus, if the line is "turns right"
	 * @param  Coordinates  $p0 first set of Coordinates
	 * @param  Coordinates  $p1 second set of Coordinates
	 * @param  Coordinates  $p2 third set of Coordinates
	 * @return bool     true if the line "turns right"
	 */
	public static function isRight($p0, $p1, $p2) {
		return self::getDotProduct($p0, $p1, $p2) < 0;
	}
}