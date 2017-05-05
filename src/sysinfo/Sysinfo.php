<?php
namespace sysinfo;

use http\HttpContext;

class Sysinfo {
	public $os;
	public $phpversion;
	public $xdebug;
	public $gd;
	public $docroot;
	public $ip;
	
	public static function create() {
		$model = new Sysinfo ();
		$model->os = PHP_OS;
		$model->phpversion = phpversion ();
		$model->xdebug = function_exists ( 'xdebug_is_enabled' ) && xdebug_is_enabled ();
		$model->gd = function_exists ( 'gd_info' );
		$model->docroot = $_SERVER ["DOCUMENT_ROOT"];
		$model->ip = HttpContext::get()->getRequest()->getUserIP();
		
		return $model;
	}
}