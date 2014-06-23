<?php

namespace Zippo\podcastFeed;

class Cache
{
    private $tmpFile;

    private $tmpFileTime;

    public function __construct()
    {
        $this->tmpFileTime = '/tmp/p1sommardata_time';
        $this->tmpFile = '/tmp/p1sommardata';
    }

    /**
     * @return array empty array if no valid cache exist
     */
    public function getP1Cache()
    {
        //if cache is before 16:00 this day, renew! (we think sr updates xml
        if (file_exists($this->tmpFileTime)) {
            $time = file_get_contents($this->tmpFileTime);
            $todayBreak = new \DateTime();
            $todayBreak->setTime(16, 0);
            $cacheDate = new \DateTime();
            $cacheDate->setTimestamp($time);
            if ($cacheDate > $todayBreak) {
                if (file_exists($this->tmpFile)) {
                    $info = unserialize(file_get_contents($this->tmpFile));
                    if (is_array($info) && count($info) > 0) {
                        error_log("returning cache");
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
        file_put_contents($this->tmpFileTime, mktime());
    }
}
