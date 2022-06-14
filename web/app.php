<?php

defined('APPLICATION_DEBUG') or define('APPLICATION_DEBUG', getenv('APPLICATION_DEBUG') === '1');
defined('APPLICATION_ENV') or define('APPLICATION_ENV',  'dev');

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new AppKernel(APPLICATION_ENV, APPLICATION_DEBUG);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
if (APPLICATION_ENV == 'prod') {
    if (class_exists('AppCache')) {
        $kernel = new AppCache($kernel);

        Request::enableHttpMethodParameterOverride();
    }
}

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
