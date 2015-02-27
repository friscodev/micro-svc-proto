<?php

// PHPUnit bootstrap

use Phalcon\DI\FactoryDefault as DefaultDI,
    Phalcon\Mvc\Micro\Collection,
    Phalcon\Config\Adapter\Php as Config,
    Phalcon\Loader;

ini_set('display_errors',1);
error_reporting(E_ALL);

// Set constants
$rootPath = dirname(__DIR__);
define('APP_ROOT_PATH',$rootPath);

define('MICROSVCS','microservices');

// Load PhalconRest framework/app files first
$frameWorkLoader = new Loader();

$frameWorkLoader->registerNamespaces(array(
    'PhalconRest\Controllers' => APP_ROOT_PATH . '/controllers/',
    'PhalconRest\Exceptions' => APP_ROOT_PATH . '/exceptions/',
    'PhalconRest\Responses' => APP_ROOT_PATH . '/responses/'
))->register();

// Create dependancy injection container
$di = new DefaultDI();

$di->set(MICROSVCS, function(){

    return array(
        'UrlShortener'
    );

});

$svcsLoader = new Loader();

$svcsNamespaces = array();

// Load and register svc directories
foreach($di->get( MICROSVCS ) as $service ){

    $svcsNamespaces[$service] = APP_ROOT_PATH . '/'.MICROSVCS.'/'.$service.'/';

}
// Register in autoload
$svcsLoader->registerNamespaces($svcsNamespaces)->register();

$app = new Phalcon\Mvc\Micro();
$app->setDI($di);