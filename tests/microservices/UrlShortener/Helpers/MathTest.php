<?php

namespace Test;

use UrlShortener\Helpers\Math;

/**
 *
 * @backupGlobals disabled
 * Class MathTest
 * @package Test
 */
class MathTest extends \PHPUnit_Framework_Testcase{

    /**
     * Oops testing static methods.  Yayyyyy!!!!!!!
     * Being pragmatic!
     */
    public function testEncodeDecode(){

        for($i=0;$i<10000;$i++){

            // Mimic int that will be encoded and decoded
            // Encode
            $encoded = Math::to_base($i);

            // Decode and test
            $this->assertEquals($i, Math::to_base_10($encoded));

        }

    }

} 