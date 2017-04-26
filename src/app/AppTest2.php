<?php
namespace app;

use handler\IHander;

class appTest extends \PHPUnit_Framework_TestCase {

    /** @var App $app */
    private $app;


    public function setUp() {
        $this->app = new App();
    }
    
    public function testXDebugEnabled() {
    	$this->assertTrue(function_exists('xdebug_is_enabled'), "XDebug is not enabled");
    	
    	$this->assertEquals(true, xdebug_is_enabled(), "XDebug is not enabled");
    }

    public function testCreate() {
        $app = App::create();
        $this->assertTrue($app instanceof \app\App);
    }

    public function testRoundtrip() {
        $expected = 'OK';
        $this->app
            ->addHandler(new MockHandler())
            ->addRoute('/', function() use ($expected) {
                return $expected;
            });

        $result = $this->app->start('/', 'GET');
        $this->assertEquals($expected, $result);
    }

    public function testRoundtripException() {
        $errorMsg = 'this is an error';

        $this->app
            ->addHandler(new MockErrorHandler())
            ->addRoute('/', function()  use ($errorMsg) {
                throw new \Exception($errorMsg);
            });

        /* @var $result \Exception */
        $result = $this->app->start('/', 'GET');
        $this->assertEquals($errorMsg, $result->getMessage());
    }
}

class MockHandler implements IHander {

    /**
     * Checks if this handler can handle the object supplied, after which the handle() method will be called
     *
     * @param
     *            mixed the controller's response
     *
     * @return boolean true if it can be handled, false if not
     */
    public function accept($object)
    {
        return true;
    }

    /**
     * Handle the object, use the accept() method to check if the object can be handled by this handler
     *
     * @param mixed $object
     *
     * @return mixed The $object that has been passed
     */
    public function handle($object)
    {
        return $object;
    }
}

class MockErrorHandler implements IHander {

    /**
     * Checks if this handler can handle the object supplied, after which the handle() method will be called
     *
     * @param
     *            mixed the controller's response
     *
     * @return boolean true if it can be handled, false if not
     */
    public function accept($object)
    {
        return $object instanceof \Exception;
    }

    /**
     * Handle the object, use the accept() method to check if the object can be handled by this handler
     *
     * @param mixed $object
     *
     * @return mixed The $object that has been passed
     */
    public function handle($object)
    {
        return $object;
    }
}