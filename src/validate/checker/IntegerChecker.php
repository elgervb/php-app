<?php
namespace validate\checker;

use validate\checker\NumberChecker;

class IntegerChecker extends NumberChecker
{
	protected function checkType($val) {
		return !is_bool($val) && is_int($val + 0); // implicit cast to int when needed
	}
	
	public function value(\Closure $fn = null) {
		return (int) parent::value($fn);
	}
}