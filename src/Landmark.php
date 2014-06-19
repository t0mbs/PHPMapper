<?php
/**
 * Landmark class file
 */
abstract class Landmark extends GraphElt {
	public $desirability;
	public $range;
	public $name;

	public function __construct($x, $y, $name) {
		$this->coords = new Coordinates($x, $y);
		$this->name = $name;
	}

	protected function setDesirability($desirability_str, $sign = '+') {
		switch($desirability_str) {
			case 'high':
				$this->desirability = 3;
				break;
			case 'med':
				$this->desirability = 2;
				break;
			case 'low':
				$this->desirability = 1;
				break;
		}
		if ($sign == '-') {
			$this->desirability *= -1;
		}
	}
}