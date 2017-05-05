<?php
require __DIR__ . '/../vendor/autoload.php';

ini_set('html_errors', 'off');

use app\App;
use validate\Args;
use handler\json\Json;
use handler\json\JsonHandler;
use sysinfo\Sysinfo;

/**
 *
 * @author Elger van Boxtel
 *
 */
$app = App::create();
$app->addHandler(new JsonHandler());

$app->route('/info/?([0-9]+)?', function($what = null) {
	$what = Args::int($what)->value();
	$what ? phpinfo($what) : phpinfo();
});

$app->route('/sysinfo', function() {
	return new Json(Sysinfo::create());
});

$app->start();
