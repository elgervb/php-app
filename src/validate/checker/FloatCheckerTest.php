<?php
namespace validate\checker;

use validate\checker\FloatChecker;

class FloatCheckerTest extends \PHPUnit_Framework_TestCase { 
	
	public function testCheckTypeFail() {
		try {
			new FloatChecker(false);
			$this->fail('Exception expected');
		} catch( \InvalidArgumentException $e) {
			$this->assertContains('not of the correct type', $e->getMessage());
		}
	}
	
	public function testValue() {
		$checker = new FloatChecker(2.1);
		$this->assertEquals($checker->value(), 2.1);
	}
}
