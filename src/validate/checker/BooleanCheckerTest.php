<?php
namespace validate\checker;

use validate\checker\BooleanChecker;

class BooleanCheckerTest extends \PHPUnit_Framework_TestCase { 
	
	public function testValue() {
		$checker = new BooleanChecker(true);
		$this->assertTrue($checker->value(), true);
	}
	
	public function testIsTrue() {
		$checker = new BooleanChecker(true);
		$this->assertTrue($checker->isTrue() instanceof BooleanChecker);
	}
	
	public function testIsFalse() {
		$checker = new BooleanChecker(false);
		$this->assertTrue($checker->isFalse() instanceof BooleanChecker);
	}
	
	public function testIsTrueFail() {
		$checker = new BooleanChecker(false);
		
		try {
			$checker->isTrue();
			$this->fail('Exception expected');
		} catch( \InvalidArgumentException $e) {
			$this->assertContains('must be true', $e->getMessage());
		}	
	}
	
	public function testIsFalseFail() {
		$checker = new BooleanChecker(true);
		
		try {
			$checker->isFalse();
			$this->fail('Exception expected');
		} catch( \InvalidArgumentException $e) {
			$this->assertContains('must be false', $e->getMessage());
		}
	}
}