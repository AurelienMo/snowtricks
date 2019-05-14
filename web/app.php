<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

$env = getenv('APP_ENV');

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

if (!$env) {
    $env = 'prod';
}
if (($useDebug = getenv('SYMFONY_DEBUG')) === false || '' === $useDebug) {
    $useDebug = $env === 'dev';
}
if ('dev' === $env) {
    Debug::enable();
}

$kernel = new AppKernel($env, $useDebug);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
