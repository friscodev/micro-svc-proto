<?php

namespace Test;

use UrlShortener\Helpers\RegEx;

/**
 *
 * @backupGlobals disabled
 * Class RegExTest
 * @package Test
 */
class RegExTest extends \PHPUnit_Framework_Testcase{

    /**
     *
     * UrlShortener\Helpers\RegEx object
     * @var \UrlShortener\Helpers\RegEx
     */
    private $_regEx;

    public function setUp(){

        $this->_regEx = new RegEx;

    }

    public function testInstance(){

        $this->assertInstanceOf('UrlShortener\Helpers\RegEx', $this->_regEx);

    }

    public function testValidUTF8(){

        // Invalid utf8
        $badUTF8 = "\xC1\xBFHello";
        $this->assertFalse($this->_regEx->checkUtf8($badUTF8));

        $badUTF8 = "\xC0\xAFHello";
        $this->assertFalse($this->_regEx->checkUtf8($badUTF8));

        $badUTF8 = "\xE0\x80\xAFHello";
        $this->assertFalse($this->_regEx->checkUtf8($badUTF8));

        $badUTF8 = "\xFC\x80\x80\x80\x80\x80\xAFHello";
        $this->assertFalse($this->_regEx->checkUtf8($badUTF8));

        // Valid utf8
        $validUTF8 = 'Ĥēĺļŏ, Ŵőřļď!';
        $this->assertEquals(true, $this->_regEx->checkUtf8($validUTF8));

        $validUTF8 = 'ⲐⲒⲔⲖⲘⲚⲜⲞⲠⲲⲴⲶⲸⲺⲼⲾⳀⳂⳄⳆⳈⳊⳌⳎⳐⳒⳔⳖⳘⳚⳜⳞⳠⳢ';
        $this->assertEquals(true, $this->_regEx->checkUtf8($validUTF8));


    }
} 