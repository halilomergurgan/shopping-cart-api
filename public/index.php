<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Get All Product
require __DIR__ . '/../app/Product/Controllers/Get.php';

// Get User Cart Controller
require __DIR__ . '/../app/Product/Controllers/Order.php';

// Get User Cart Controller
require __DIR__ . '/../app/Product/Controllers/Cart.php';

//User Store Cart Controller
require __DIR__ . '/../app/Product/Controllers/Store.php';

//User Store Purchase
require __DIR__ . '/../app/Product/Controllers/Purchase.php';

//Cart Model
require __DIR__ . '/../app/Models/Cart.php';

//Category Model
require __DIR__ . '/../app/Models/Category.php';

//Response Helper
require __DIR__ . '/../Helper/ResponseHelper.php';

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// Run app
$app->run();
