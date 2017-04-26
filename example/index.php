<?php
require __DIR__ . '/../vendor/autoload.php';

use app\App;

/**
 *
 * @author Elger van Boxtel
 *
 */
$app = new App();

$app->route('/info', function() {
	phpinfo();
});

$app->start();
