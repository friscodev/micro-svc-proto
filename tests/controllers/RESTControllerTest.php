<?php

namespace Test;


use PhalconRest\Controllers\RESTController;


/**
 *
 * @backupGlobals disabled
 * Class RESTControllerTest
 * @package Test
 *
 */
class RESTControllerTest extends \PHPUnit_Framework_Testcase{

    public function testInstance(){
        $this->assertInstanceOf('PhalconRest\Controllers\RESTController', new RESTController());
    }

}