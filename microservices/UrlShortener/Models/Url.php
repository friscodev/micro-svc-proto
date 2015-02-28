<?php

namespace UrlShortener\models;

use UrlShortener\Helpers\Math;

/**
 *
 * Class Url
 * @package UrlShortener
 */
class Url extends \Phalcon\Mvc\Model {

    public $id;
    public $path;
    public $path_hash;
    public $qrystr;
    public $date_created;

    public function initialize()
    {
        $this->setConnectionService("urlShortenerDb");
    }

    public function getSource()
    {
        return "url";
    }

    /**
     *
     * @param $str
     * @return array
     */
    public function pathToJson($str){

        return json_encode(explode('/',$str), true);

    }

    /**
     *
     * @param $str
     * @return string
     */
    public function qryStrToJson($str){

        parse_str($str,$array);

        return json_encode($array, true);
    }

    public function encodedUrl(){

        return Math::to_base($this->id);

    }

    /**
     *
     * @param string $scheme
     * @param string $host
     * @return string
     *
     */
    public function toHttpUrl($scheme, $host){

        return $scheme.'://' .$host.$this->toHttpPath().(($this->qrystr !== "")? '?'.$this->toHttpQueryString($this->qrystr):'');
    }

    public function toHttpPath(){

        return implode('/', json_decode($this->path));
    }

    public function toHttpQueryString($str){

        if($str != ''){

            return http_build_query(json_decode($str));

        }

    }

} 