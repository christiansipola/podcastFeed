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

    public function testNowBeforeTodaysBreak()
    {
        $expectedBreak = new \DateTime('2014-10-10 16:00');
        $this->cache->genPeriodfromNowBreakFrom(new \DateTime('2014-10-11 15:00'));
        $actualBreak = $this->cache->getBreak();
        $this->assertEquals($expectedBreak, $actualBreak);
    }
    
    public function testCache()
    {
        $expectedInfo = [
            '2014-10-21' => [
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
