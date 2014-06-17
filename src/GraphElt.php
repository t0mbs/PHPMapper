<?php
/**
 * GraphElt class file
 */
abstract class GraphElt {
	public $coordinates;
	/**
	 * Gets the Element's Coordinates object
	 * @return Coordinates
	 */
	public function getCoordinates() {
		return $coordinates;
	}
}