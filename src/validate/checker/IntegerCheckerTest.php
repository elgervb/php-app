<?php
namespace validate\checker;

use validate\checker\IntegerChecker;
use validate\checker\NumberChecker;

class IntegerCheckerTest extends \PHPUnit_Framework_TestCase { 
	
	public function testCheckTypeFail() {
		try {
			new IntegerChecker(false);
			$this->fail('Exception expected');
		} catch( \InvalidArgumentException $e) {
			$this->assertContains('not of the correct type', $e->getMessage());
		}
	}
	
	public function testValue() {
		$checker = new IntegerChecker(2);
		$this->assertEquals($checker->value(), 2);
	}
	
	public function testMax() {
		$checker = new IntegerChecker(2);
		$this->assertTrue($checker->max(2) instanceof NumberChecker);
	}
	
	public function testMin() {
		$checker = new IntegerChecker(2);
		$this->assertTrue($checker->min(1) instanceof NumberChecker);
	}
	
	public function testMaxFail() {
		$checker = new IntegerChecker(2);
		try {
			$checker->max(1);
			$this->fail('Exception expected');
		} catch(\InvalidArgumentException $e) {
			$this->assertContains('must be smaller then', $e->getMessage());
		}
	}
	
	public function testMinFail() {
		$checker = new IntegerChecker(2);
		try {
			$checker->min(3);
			$this->fail('Exception expected');
		} catch(\InvalidArgumentException $e) {
			$this->assertContains('must be larger then', $e->getMessage());
		}
	}
}
