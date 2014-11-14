<?php

namespace Zippo\podcastFeed\Tests;

use Zippo\podcastFeed\Configuration;
use Zippo\podcastFeed\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testSommar()
    {
        $model = new Model();
        $info = $model->getInfoP1Sommar();

        $this->assertTrue(count($info) > 0);
        foreach ($info as $dateStr => $row) {
            $this->assertTrue(is_string($dateStr));
            $this->assertTrue(isset($row['title']));
            $this->assertTrue(isset($row['desc']));
        }
    }

    public function testPopular()
    {
        $model = new Model();
        $model->configuration = new Configuration();
        $model->configuration->fullLocalPathToFiles = __DIR__ .'/../../../radio/';
        $model->genShowP3musikguiden();

        $this->assertTrue(count($model->show) == 1);
        
        $show = $model->show[0];
        
        $this->assertEquals('2014-05-28', $show->getDate());
    }
}
