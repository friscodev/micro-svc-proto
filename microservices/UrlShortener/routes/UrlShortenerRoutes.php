<?php

/**
 * Collections let us define groups of routes that will all use the same controller.
 * We can also set the handler to be lazy loaded.  Collections can share a common prefix.
 * @var $routeCollection
 */

return call_user_func(function(){

    $routeCollection = new \Phalcon\Mvc\Micro\Collection();

    $routeCollection
        // VERSION NUMBER SHOULD BE FIRST URL PARAMETER, ALWAYS
        ->setPrefix('/v1/urlshortener')
        // Must be a string in order to support lazy loading
        ->setHandler('\UrlShortener\Controllers\UrlShortenerController')
        ->setLazy(true);

    $routeCollection->get('/', 'get');
    $routeCollection->post('/', 'post');

    return $routeCollection;
});