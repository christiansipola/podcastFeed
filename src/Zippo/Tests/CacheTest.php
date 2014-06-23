<?php

namespace Zippo\podcastFeed\Tests;

use Zippo\podcastFeed\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCache()
    {
        date_default_timezone_set('CET');
        $cache = new Cache();
        $oldInfo = $cache->getP1Cache();

        $expectedInfo = [
            '2014-06-22' => [
                'desc' => 'test'
            ]
        ];
        $cache->writeToCache($expectedInfo);

        $actualInfo = $cache->getP1Cache();
        $this->assertEquals($expectedInfo, $actualInfo);

        $cache->writeToCache(array());
        $actualInfo = $cache->getP1Cache();
        $this->assertTrue(empty($actualInfo));

        $cache->writeToCache($oldInfo);

        $actualInfo = $cache->getP1Cache();
        $this->assertEquals($oldInfo, $actualInfo);


    }
}
