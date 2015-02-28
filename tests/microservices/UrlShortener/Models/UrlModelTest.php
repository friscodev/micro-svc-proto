<?php

namespace Test;


use UrlShortener\Models\Url;

/**
 * @backupGlobals disabled
 * Class UrlModelTest
 * @package Test
 *
 */
class UrlModelTest extends \PHPUnit_Framework_Testcase{

    /**
     * UrlShortener\models\Url object
     * @var \UrlShortener\models\Url
     */
    private $_model;

    private $_urls;

    private $_scheme;

    private $_host;

    public function setUp(){

        $this->_model = new Url;

        $this->_urls = array(
            "http://www.popsugar.com/home/90s-Home-Decor-36853394?utm_source=living_newsletter&utm_medium=email&utm_campaign=living_newsletter_v3_02182015&em_recid=5020233&utm_content=placement_1_title",
            "http://www.popsugar.com/food/Basic-French-Macaron-Recipe-21651110?utm_source=living_newsletter&utm_medium=email&utm_campaign=living_newsletter_v3_02182015&em_recid=5020233&utm_content=placement_3_title",
            "http://www.popsugar.com/smart-living/How-Remove-Sweat-Stains-31003483?utm_source=living_newsletter&utm_medium=email&utm_campaign=living_newsletter_v3_02182015&em_recid=5020233&utm_content=placement_6_title"
        );

        $this->_scheme = "http";
        $this->_host   = "www.popsugar.com";

    }

    public function testInstance(){
        $this->assertInstanceOf('UrlShortener\Models\Url', $this->_model);
    }


    public function testSerializedBackToUrl(){

        // Urls provided
        $parsedUrl1 = parse_url($this->_urls[0]);
        $parsedUrl2 = parse_url($this->_urls[1]);
        $parsedUrl3 = parse_url($this->_urls[2]);


        // URL 1
        // Serialize path
        $path1 = $this->_model->pathToJson($parsedUrl1['path']);
        // Serialilze qrystring
        $qryStr1 = $this->_model->qryStrToJson($parsedUrl1['query']);
        // Assert serializing to json
        $this->assertTrue(is_string($path1));
        $this->assertTrue(is_string($qryStr1));
        // Stub as if from database
        $this->_model->path = $path1;
        $this->_model->qrystr = $qryStr1;
        // Assert
        $this->assertEquals($parsedUrl1['query'], $this->_model->toHttpQueryString($qryStr1));
        $this->assertEquals($this->_urls[0], $this->_model->toHttpUrl($this->_scheme, $this->_host));

        // URL 2
        // Serialize path
        $path2 = $this->_model->pathToJson($parsedUrl2['path']);
        // Serialilze qrystring
        $qryStr2 = $this->_model->qryStrToJson($parsedUrl2['query']);
        // Assert serializing to json
        $this->assertTrue(is_string($path2));
        $this->assertTrue(is_string($qryStr2));
        // Stub as if from database
        $this->_model->path = $path2;
        $this->_model->qrystr = $qryStr2;
        // Assert
        $this->assertEquals($parsedUrl2['query'], $this->_model->toHttpQueryString($qryStr2));
        $this->assertEquals($this->_urls[1], $this->_model->toHttpUrl($this->_scheme, $this->_host));

        // URL 3
        // Serialize path
        $path3 = $this->_model->pathToJson($parsedUrl3['path']);
        // Serialilze qrystring
        $qryStr3 = $this->_model->qryStrToJson($parsedUrl3['query']);

        // Assert serializing to json
        $this->assertTrue(is_string($path3));
        $this->assertTrue(is_string($qryStr3));
        // Stub as if from database
        $this->_model->path = $path3;
        $this->_model->qrystr = $qryStr3;
        // Assert
        $this->assertEquals($parsedUrl3['query'], $this->_model->toHttpQueryString($qryStr3));
        $this->assertEquals($this->_urls[2], $this->_model->toHttpUrl($this->_scheme, $this->_host));


    }

} 