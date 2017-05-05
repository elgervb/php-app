<?php
namespace validate\checker;

abstract class ArgumentChecker
{
	protected $val;
	private $name;
	
	public function __construct($val, $name = 'arg') {
		if ($val !== null){
			if( !$this->checkType($val) ){
				$this->throwException("Value is not of the correct type");
			}
		}
		$this->val = $val;
		$this->name = $name;
	}
	
	protected abstract function checkType($val);
	
	public function required()
	{
		if (null === $this->val) {
			$this->throwException('is required');
		}
		
		return $this;
	}
	
	/**
	 * Internal function to throw a new exception
	 *
	 * @param string $msg The message to show; will be prepended with the name of the variable under check
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function throwException($msg){
		throw new \InvalidArgumentException($this->name . ' ' . $msg);
	}
	
	/**
	 * returns the value of the checker
	 *
	 * @param \Closure a optional transformation closure. This will get the value as a parameter
	 *
	 * @return mixed the optionally transformed value
	 */
	public function value(\Closure $fn = null){
		if ($fn){
			return $fn($this->val);
		}
		return  $this->val;
	}
}