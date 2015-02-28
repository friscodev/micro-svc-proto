<?php

namespace UrlShortener\Helpers;


/**
 *
 * Class RegEx
 * @package UrlShortener
 */
class RegEx {

    /**
     * RegEx to check for valid utf-8
     * @var string
     */
    private $_utf8;

    /**
     *
     * @var
     */
    private $_urlBeginsWith;


    public function __construct(){

        $this->_utf8 = '/^./us';

        $this->_urlBeginsWith = '/^https?:\/\/|^\/\//';
    }

    /**
     * Checks if string is valid utf-8
     * @param $string
     * @return bool
     */
    public function checkUtf8($string){

        return preg_match($this->_utf8, $string);
    }


    public function checkUrlBeginsWith($str){

        return preg_match($this->_urlBeginsWith, $str);
    }
} 