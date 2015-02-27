<?php

/**
 *
 * RouteLoader loads a set of Phalcon Mvc\Micro\Collections from
 * the routes directory of each microservice
 *
 * Routes in return \Phalcon\Mvc\Micro\Collection object
 *
 * TODO: This process can be reversed to create even more loosely couple services.
 *
 */
return call_user_func(function(){

	$collections = array();

    $servicesLabel = 'microservices';

    // TODO: Think about coupling
    $microServices = \Phalcon\DI::getDefault()->get($servicesLabel);

    foreach($microServices as $service){

        $file = APP_ROOT_PATH .'/'.MICROSVCS.'/'.$service.'/routes/'. $service.'Routes.php';

        if(file_exists($file)){

            $collections[] = include($file);
        }


    }

	return $collections;
});