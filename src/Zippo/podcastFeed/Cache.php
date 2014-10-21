<?php

namespace Zippo\podcastFeed;

class Cache
{
    private $tmpFile;

    private $tmpFileTime;

    /**
     * @var \DateTime
     */
    private $break;

    public function __construct()
    {
        $this->tmpFileTime = '/tmp/p1sommardata_time';
        $this->tmpFile = '/tmp/p1sommardata';
        $this->genPeriodfromNowBreakFrom(new \DateTime());
    }

    /**
     * @return \DateTime
     */
    public function getBreak()
    {
        return $this->break;
    }
    
    public function genPeriodfromNowBreakFrom(\DateTime $now)
    {
        $todayBreak = clone $now;
        $todayBreak->setTime(16, 0);
        
        if ($now > $todayBreak) {
            $this->break = $todayBreak;
        } else {
            $yesterdaysBreak = clone $now;
            $yesterdaysBreak->modify('-1 day');
            $yesterdaysBreak->setTime(16, 0);
            $this->break = $yesterdaysBreak;
        }
    }
    
    /**
     * @return array empty array if no valid cache exist
     */
    public function getP1Cache()
    {
        //if cache is before 16:00 this day, and now is after 16, renew! (we think sr updates xml then)
        if (file_exists($this->tmpFileTime)) {
            $time = file_get_contents($this->tmpFileTime);
            $cacheDate = new \DateTime();
            $cacheDate->setTimestamp($time);
            //if data for cache is after cache should be from
            if ($cacheDate >= $this->break) {
                if (file_exists($this->tmpFile)) {
                    $info = unserialize(file_get_contents($this->tmpFile));
                    if (is_array($info) && count($info) > 0) {
                        return $info;
                    }
                }
            }
        }
        return array();
    }

    public function writeToCache($info)
    {
        //save cache
        file_put_contents($this->tmpFile, serialize($info));
        file_put_contents($this->tmpFileTime, time());
    }
}
