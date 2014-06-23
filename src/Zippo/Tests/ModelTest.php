<?php

namespace Zippo\podcastFeed\Tests;

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
}
