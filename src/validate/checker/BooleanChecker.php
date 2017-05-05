<?php
namespace validate\checker;

use validate\checker\ArgumentChecker;

class BooleanChecker extends ArgumentChecker {
	protected function checkType($val){
		return is_bool(!!$val);
	}
	public function isTrue(){
		if ($this->val === false){
			$this->throwException('must be true');
		}
		return $this;
	}
	public function isFalse(){
		if ($this->val === true){
			$this->throwException('must be false');
		}
		return $this;
	}
	
	public function value(\Closure $fn = null){
		return !!parent::value($fn);
	}
}