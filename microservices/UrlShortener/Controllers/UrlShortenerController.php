<?php

namespace UrlShortener\Controllers;


use PhalconRest\Controllers\RESTController;
use UrlShortener\Helpers\Math;
use UrlShortener\Helpers\RegEx;
use UrlShortener\Models\Url;

/**
 *
 * Controller for UrlShortener Service
 *
 * Class UrlShortenerController
 * @package UrlShortener
 */
class UrlShortenerController extends RESTController {

    /**
     * UrlShortener\Helpers\RegEx object
     * @var \UrlShortener\Helpers\RegEx
     */
    private $_regEx;

    public function init(){

        $this->_regEx = new RegEx();
    }

    /**
     *
     * General method to retrieve a single URL. Might want to retrieve for websvc or redirect
     * @param $encodedid
     * @return bool
     *
     */
    public function retrieveUrl($encodedid){

        $this->init();

        // Many clients ( browsers, libs ) normalize encoding, check in case a custom script is used to submit
        if($this->_regEx->checkUtf8($encodedid) == false){

            $this->throwPreConditionException("Error in provided string", 400, "Check encoding");
        }

        // Decode submitted id
        $result = Url::findFirst(Math::to_base_10($encodedid));

        if(count($result) > 0){

            return $result;
        }

        return false;

    }

    public function get($encodedid){

        $this->init();

        $result = $this->retrieveUrl($encodedid);

        if($result != false){

            return array("url"=>$result->toHttpUrl('http', 'www.popsugar.com'));
        }

        $this->throwPreConditionException("Error", 400, "Check URL");
    }

    /**
     *
     * Proof of concept for redir.  While not recommended, a server rewrite can be used to map to this route
     *
     * @param $encodedid
     */
    public function getForRedirect($encodedid){

        $this->init();

        $result = $this->retrieveUrl($encodedid);

        if($result != false){

            header("Location: " . $result->toHttpUrl('http', 'www.popsugar.com'));
        }
    }

    public function post(){

        $this->init();

        // Get parsed request body
        $url = $this->di->getShared('requestBody');

        // Minimal precondition check before we move further into process.
        if($this->validateStructure($url['url']) == false){

            $this->throwPreConditionException("Error in URL", 400, "Check URL");
        }

        // If this fails, URL must be very malformed
        if(($parsedUrl = parse_url($url['url'])) == false){

            $this->throwPreConditionException("Error in URL", 400, "Check URL");

        }

        // If path isn't empty
        if(trim($parsedUrl['path']) == ""){

            $this->throwPreConditionException("Error in URL", 400, "Check URL");
        }

        // Passed initial marks
        // Get new model
        $model = new Url;

        // Bind to model
        $model->path = $model->pathToJson($parsedUrl['path']);

        // Use to avoid duplicate URLs
        $model->path_hash = sha1($model->path);

        // Get record with existing path hash
        $existingUrl = Url::find("path_hash = '".$model->path_hash."'");

        // Check results
        if(count($existingUrl) > 0){

            // If found, return shortened URL within exception
            $this->throwPreConditionException("Already in system", 400, array("url"=>Math::to_base($existingUrl[0]->id)));
        }


        if(trim($parsedUrl['query']) != ""){
            $model->qrystr = $model->qryStrToJson($parsedUrl['query']);
        }

        if($model->save() == true){

            return array('shortened'=> $model->encodedUrl());

        }

        return $parsedUrl;
    }

    /**
     *
     * Should go in model or validator, but lets save mem and time by
     * doing a small check here.  Why instantiate further in app if basic
     * compliance isn't met?
     * @param $url
     * @return string
     */
    public function validateStructure($url){

        return $this->_regEx->checkUrlBeginsWith($url);

    }

    public function throwPreConditionException($userMessage, $code, $devMessage){

        throw new \PhalconRest\Exceptions\HTTPException (
            $userMessage,
            $code,
            array(
                'dev' => $devMessage
            )
        );

    }

}