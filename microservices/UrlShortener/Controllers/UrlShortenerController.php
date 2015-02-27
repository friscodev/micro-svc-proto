<?php

namespace UrlShortener\Controllers;


use PhalconRest\Controllers\RESTController;

/**
 *
 * Controller for UrlShortener Service
 *
 * Class UrlShortenerController
 * @package UrlShortener
 */
class UrlShortenerController extends RESTController {

    public function get(){

        // Stub
        return array("method", __METHOD__);
    }

    public function post(){

        // Get parsed request body
        $url = $this->di->getShared('requestBody');

        // Minimal precondition check before we move further into process
        if($this->validateStructure($url['url']) == false){

            $this->throwPreConditionException();
        }

        // If this fails preconditions not met
        if(($parsedUrl = parse_url($url['url'])) == false){

            $this->throwPreConditionException();

        }

        return $parsedUrl;
    }

    /**
     * Should go in model or validator, but lets save mem and time by doing a small check here.
     * Why instantiate further in app if basic compliance isn't met?
     * @param $url
     * @return string
     */
    public function validateStructure($url){

        return preg_match('/^https?:\/\/|^\/\//', $url);

    }

    public function throwPreConditionException(){

        throw new \PhalconRest\Exceptions\HTTPException (
            'Malformed URL',
            412,
            array(
                'dev' => 'Unable to parse malformed URL'
            )
        );

    }

}