<?php

namespace Zippo\podcastFeed\Tests;

use Zippo\podcastFeed\Configuration;
use Zippo\podcastFeed\Model;

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

    public function testPopular()
    {
        $this->model->genShowP3musikguiden();

        $this->assertEquals(4, count($this->model->getShowList()));
        
        $show = $this->model->getShowList()[0];
        
        $this->assertEquals('2014-05-28 del 1 Onsdag', $show->getTitle());
        $show->getLength();
        $show->getPubDate();
        $show->getUrl();
        $show->getDate();
    }
}
