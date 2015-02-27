<?php

namespace UrlShortener\Validators;

use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\RegEx;

/**
 *
 * Class UrlValidator
 * @package UrlShortener\Validators
 */
class UrlValidator extends \Phalcon\Validation {

    public function initialize(){

        $this->add('scheme', new PresenceOf(array(
            'message' => 'The scheme is required'
        )));

        $this->add('path', new PresenceOf(array(
            'message' => 'The path is required'
        )));

        $this->add('qry', new PresenceOf(array(
            'message' => 'The qry is required'
        )));


    }
} 