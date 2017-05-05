<?php
namespace validate\checker;

use validate\checker\ArgumentChecker;

abstract class NumberChecker extends ArgumentChecker {
	public function max($max){
		if ($this->val > $max){
			$this->throwException('must be smaller then ' . $max);
		}
		return $this;
	}
	public function min($min){
		if ($this->val < $min){
			$this->throwException('must be larger then ' . $min);
		}
		return $this;
	}
}