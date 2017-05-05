<?php
namespace validate\checker;

use validate\checker\NumberChecker;

class FloatChecker extends NumberChecker {
	protected function checkType($val){
		return is_float($val + 0);
	}
	
	public function value(\Closure $fn = null){
		return parent::value($fn) + 0;
	}
}