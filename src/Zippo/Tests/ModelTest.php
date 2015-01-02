<?php

namespace Zippo\podcastFeed\Tests;

use Zippo\podcastFeed\Configuration;
use Zippo\podcastFeed\Model;
use Zippo\podcastFeed\View;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    private $model;


    public function setUp()
    {
        $this->model = new Model();
        $this->model->configuration = new Configuration();
        $this->model->configuration->fullLocalPathToFiles = __DIR__ .'/../../../testdata/';
    }
    
    public function testSommar()
    {
        
        $info = $this->model->getInfoP1Sommar();

        $this->assertTrue(count($info) > 0);
        foreach ($info as $dateStr => $row) {
            $this->assertTrue(is_string($dateStr));
            $this->assertTrue(isset($row['title']));
            $this->assertTrue(isset($row['desc']));
        }
        $this->model->genShowP1Sommar();
        $string = $this->model->getDownloadCodeSommar($info);
        $this->assertTrue(strlen($string) > 0);
        
    }

    public function testSommarView()
    {
        $info = $this->model->getInfoP1Sommar();
        $this->model->genShowP1Sommar();
        $view = new View();
        $xmlString = $view->renderP1Sommar($this->model, $info);
        $this->assertTrue(strlen($xmlString) > 0);
    }

    public function testPopular()
    {
        $this->model->genShowP3musikguiden();

        $this->assertEquals(4, count($this->model->getShowList()));
        
        $show = $this->model->getShowList()[0];
        
        $this->assertEquals('2014-05-28', $show->getDate());
        $this->assertEquals('http:///files/podcast-2014-05-28-1.mp3', $show->getUrl());
        $this->assertNotEmpty($show->getLength());
        $this->assertNotEmpty($show->getPubDate());
        $this->assertNotEmpty($show->getTitle());
        
        $view = new View();
        $output = $view->render($this->model);
        $this->assertTrue(strlen($output) > 0);
        
    }
}
