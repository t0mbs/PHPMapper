<?php
//Would be a custom data type in OCaml
//Used as our base coordinates for all graph elements
class Coordinates {
	protected $x;
	protected $y;
	function __construct (int $x, int $y) {
		$this->x = $x;
		$this->y = $y;
	}
}