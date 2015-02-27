<?php

namespace Test;

use PhalconRest\Controllers\BaseController;

/**
 * @backupGlobals disabled
 * Class BaseControllerTest
 * @package Test
 */
class BaseControllerTest extends \PHPUnit_Framework_Testcase{

    public function testInstance(){
        $this->assertInstanceOf('PhalconRest\Controllers\BaseController', new BaseController());
    }

} 