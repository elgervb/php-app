<?php
namespace app;

/**
 * Application class
 *
 * @author Elger van Boxtel
 */
class App {
	
	const DEFAULT_TIMEZONE = 'UTC';
	
	/**
	 * @var \router\Router
	 */
	private $router;
	
	/**
	 * Create a new Application at a timezone
	 *
	 * @param string $timezone defaults to UTC
	 */
	public function __construct($timezone = self::DEFAULT_TIMEZONE) {
		$this->setUp($timezone);
	}
	
	/**
	 * Factory method to create a new applicaton
	 *
	 * @param string $timezone defaults to UTC
	 *
	 * @return \app\App
	 */
	public static function create($timezone = self::DEFAULT_TIMEZONE) {
		return new App($timezone);
	}
	
	/**
	 * Add a new hanler
	 *
	 * @param \handler\IHander $handler
	 *
	 * @return \app\App
	 */
	public function addHandler(\handler\IHander $handler) {
		\handler\Handlers::get()->add($handler);
		
		return $this;
	}
	
	/**
	 * Returns the router
	 * 
	 * @return \router\Router
	 */
	public function getRouter() {
		if (!$this->router) {
			$this->router = new \router\Router();
		}
		
		return $this->router;
	}

	/**
	 * Add a new route
	 *
	 * @param string $path the path to the route
	 * @param \Closure $controller the controller to handle the route
	 * @param string $requestMethod The request method, defaults to GET
	 *
	 * @return \app\App
	 */
	public function route($path, \Closure $controller, $requestMethod = 'GET') {
		$router = $this->getRouter();
		
		$router->route($path, $controller, $requestMethod);
		
		return $this;
	}
	
	/**
	 * @param string [$requestPath] the requested path, defaults to $_SERVER['REQUEST_URI']
	 * @param string [$httpMethod] the http method, defaults to $_SERVER['REQUEST_METHOD']
	 * @return mixed
	 */
	public function start($requestPath = null, $httpMethod = null) {
		if (!$requestPath) {
			$requestPath = $_SERVER['REQUEST_URI'];
		}
		if (!$httpMethod) {
			$httpMethod = $_SERVER['REQUEST_METHOD'];
		}
        $result = null;

        try {
            $result = $this->getRouter()->match($requestPath, $httpMethod);
        } catch (\Exception $e) {
            $result = $e;
        }
		
		return $this->handleActionResult($result);
	}

    /**
     * Handle the action result
     *
     * @param $result
     * @return mixed
     */
	private function handleActionResult($result) {
		
		$handlers = \handler\Handlers::get();
		$handler = $handlers->getHandler($result);
		if ($handler) {
			try {
				$handler->handle($result);
			} catch (\Exception $e) {
				$error = new HttpStatus(500, $e->getMessage());
				$handler = $handlers->getHandler($error);
				$handler->handle($error);
			}
		} else {
			$error = new \handler\http\HttpStatus(404, ' ');
			$handler = $handlers->getHandler($error);
			if ($handler) {
				$handler->handle($error);
			}
		}
		
		return $result;
	}
	
	private function setUp($timezone) {
		// initially turn on error reporting
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		
		date_default_timezone_set($timezone);
		ini_set('date.timezone', $timezone);
	}
}