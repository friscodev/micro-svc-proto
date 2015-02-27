<?php

use Phalcon\DI\FactoryDefault as DefaultDI,
	Phalcon\Mvc\Micro\Collection,
	Phalcon\Config\Adapter\Php as Config,
	Phalcon\Loader;

// Set constants
define('APP_ROOT_PATH',__DIR__);
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


// List of MicroServices.  Returns an array of microservices
// TODO:  This can be even further decloupled as this directory can be moved and or used via composer
$di->set(MICROSVCS, function(){

    return array(
        'UrlShortener'
    );

});

// Loader for svcs
$svcsLoader = new Loader();

$svcsNamespaces = array();

// Load and register svc directories
foreach($di->get( MICROSVCS ) as $service ){

    $svcsNamespaces[$service] = APP_ROOT_PATH . '/'.MICROSVCS.'/'.$service.'/';

}
// Register in autoload
$svcsLoader->registerNamespaces($svcsNamespaces)->register();

// Array of collections ( routes ) loaded from iterating throw list of provided svcs
$di->set('collections', function(){
	return include('./helpers/routeLoader.php');
});

// Designate config
$di->setShared('config', function() {
	return new Config("config/config.php");
});

// As soon as we request the session service, it will be started.
$di->setShared('session', function(){
	$session = new \Phalcon\Session\Adapter\Files();
	$session->start();
	return $session;
});

$di->set('modelsCache', function() {

	//Cache data for one day by default
	$frontCache = new \Phalcon\Cache\Frontend\Data(array(
		'lifetime' => 3600
	));

	//File cache settings
	$cache = new \Phalcon\Cache\Backend\File($frontCache, array(
		'cacheDir' => __DIR__ . '/cache/'
	));

	return $cache;
});

/**
 * Database setup.  Here, we'll use a simple SQLite database of Disney Princesses.
 */
$di->set('db', function(){
	return new \Phalcon\Db\Adapter\Pdo\Sqlite(array(
		'data/database.sqlite'
	));
});

/**
 * If our request contains a body, it has to be valid JSON.  This parses the 
 * body into a standard Object and makes that available from the DI.  If this service
 * is called from a function, and the request body is nto valid JSON or is empty,
 * the program will throw an Exception.
 */
$di->setShared('requestBody', function() {
	$in = file_get_contents('php://input');
	$in = json_decode($in, TRUE);

	// JSON body could not be parsed, throw exception
	if($in === null){
		throw new \PhalconRest\Exceptions\HTTPException(
			'There was a problem understanding the data sent to the server by the application.',
			409,
			array(
				'dev' => 'The JSON body sent to the server was unable to be parsed.',
				'internalCode' => 'REQ1000',
				'more' => ''
			)
		);
	}

	return $in;
});

/**
 * Out application is a Micro application, so we mush explicitly define all the routes.
 * For APIs, this is ideal.  This is as opposed to the more robust MVC Application
 * @var $app
 */
$app = new Phalcon\Mvc\Micro();
$app->setDI($di);


// Mount collection of handlers/routes
foreach($di->get('collections') as $collection){
	$app->mount($collection);
}

/**
 *
 * After a route is run, usually when its Controller returns a final value,
 * the application runs the following function which actually sends the response to the client.
 *
 * The default behavior is to send the Controller's returned value to the client as JSON.
 * However, by parsing the request querystring's 'type' paramter, it is easy to install
 * different response type handlers.  Below is an alternate csv handler.
 */
$app->after(function() use ($app) {

	// OPTIONS have no body, send the headers, exit
	if($app->request->getMethod() == 'OPTIONS'){
		$app->response->setStatusCode('200', 'OK');
		$app->response->send();
		return;
	}

	// Respond by default as JSON
	if(!$app->request->get('type') || $app->request->get('type') == 'json'){

		// Results returned from the route's controller.  All Controllers should return an array
		$records = $app->getReturnedValue();

		$response = new \PhalconRest\Responses\JSONResponse();
		$response->useEnvelope(true) //this is default behavior
			->convertSnakeCase(true) //this is also default behavior
			->send($records);

		return;
	}
	else if($app->request->get('type') == 'csv'){

		$records = $app->getReturnedValue();
		$response = new \PhalconRest\Responses\CSVResponse();
		$response->useHeaderRow(true)->send($records);

		return;
	}
	else {
		throw new \PhalconRest\Exceptions\HTTPException(
			'Could not return results in specified format',
			403,
			array(
				'dev' => 'Could not understand type specified by type paramter in query string.',
				'internalCode' => 'NF1000',
				'more' => 'Type may not be implemented. Choose either "csv" or "json"'
			)
		);
	}
});

/**
 * The notFound service is the default handler function that runs when no route was matched.
 * We set a 404 here unless there's a suppress error codes.
 */
$app->notFound(function () use ($app) {
	throw new \PhalconRest\Exceptions\HTTPException(
		'Not Found.',
		404,
		array(
			'dev' => 'That route was not found on the server.',
			'internalCode' => 'NF1000',
			'more' => 'Check route for mispellings.'
		)
	);
});

/**
 * If the application throws an HTTPException, send it on to the client as json.
 * Elsewise, just log it.
 * TODO:  Improve this.
 */
set_exception_handler(function($exception) use ($app){
	//HTTPException's send method provides the correct response headers and body
	if(is_a($exception, 'PhalconRest\\Exceptions\\HTTPException')){
		$exception->send();
	}
	error_log($exception);
	error_log($exception->getTraceAsString());
});

$app->handle();

