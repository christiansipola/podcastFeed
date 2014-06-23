<?php

namespace Zippo\podcastFeed\Tests;

use Zippo\podcastFeed\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Cache
     */
    private $cache;

    public function setUp()
    {
        date_default_timezone_set('CET');
        $this->cache = new Cache();
        $this->cache->writeToCache(array());
    }

    public function testCache()
    {
        $expectedInfo = [
            '2014-06-22' => [
                'desc' => 'test'
            ]
        ];
        $this->cache->writeToCache($expectedInfo);

        $actualInfo = $this->cache->getP1Cache();
        $this->assertEquals($expectedInfo, $actualInfo);

        $this->cache->writeToCache(array());
        $actualInfo = $this->cache->getP1Cache();
        $this->assertTrue(empty($actualInfo));
    }
}
