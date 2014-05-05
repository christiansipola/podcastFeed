<?php

namespace Zippo;

class Model
{
    
    const DIR_LOCAL = '/srv/unofficial/podcastFeed/radio';
    // on web
    const DIR_WEB = '/tmp/podcast';
    const MINSIZE = '32000000'; // 20:30
    
    const SHOW_P1SOMMAR = 'p1sommar';
    
    public static $filePathDir = null;
    
    // on localhost
    public static $fileWebPath = 'podcastFeed/radio/';
    // on web
    // public static $fileWebPath = '';
    public $latestBuild;

    public $show;
    
    /**
     * 
     * @var string
     */
    public $showName;

    /**
     * $_SERVER["SERVER_NAME"]
     */
    public $serverName = null;

    public function getShowWithLS()
    {
        chdir(self::$filePathDir);
        $ls = `ls -l p3Pop*`;
        $file = array_slice(explode("\n", $ls), - 21, 20);
        foreach ($file as $f) {
            
            $matches = array();
            $pattern = '/.*(.{3} .{1} .{2}:.{2}) p3Populär-(.{10})-(.{1})\.mp3/';
            $pattern = '/.*(\S{3} \S{1} \S{2}:\S{2}).*/';
            preg_match($pattern, $f, $matches);
            $show[] = array(
                'title' => '2009-09-18 del 1',
                'url' => 'http://p3popular.sipola.se/p3Populär-2009-09-18-1.mp3',
                'length' => '83593008',
                'pubDate' => date_create('2009-09-18 13:15:00')->format(DATE_RSS)
            )
            ;
        }
    }

    public function genShowP1Sommar()
    {
        $this->showName = 'p1sommar';
        $collection = $this->getFileMetaCollection();
        $this->genShowWithMetaData($collection);
    }
    
    public function genShowP3musikguiden()
    {
        $this->showName = 'p3popular';
        $collection = $this->getFileMetaCollection();
        $this->genShowWithMetaData($collection);
    }
    
    /**
     * 
     * @param string $showName
     * @return \ArrayObject
     */
    private function getFileMetaCollection()
    {
        if (! is_dir(self::$filePathDir)) {
            throw new \Exception('directory ' . self::$filePathDir . ' does not exist');
        }
        chdir(self::$filePathDir);
        $dir = scandir(self::$filePathDir);
        
        $collection = new \ArrayObject();
        
        foreach ($dir as $file) {
            $start = substr(strtolower($file), 0, 7);
            if ($start != 'podcast') {
                continue;
            }
            $size = filesize(self::$filePathDir . '/' . $file);
            $tmp = explode("-", $file);
            if (count($tmp) < 4) {
                continue;
            }
            $part = substr($tmp[4], 0, 1);
            $year = $tmp[1];
            $month = $tmp[2];
            $day = $tmp[3];
            if ($part == 'q' && $size < (self::MINSIZE / 2)) {
                continue; // second part of podcast is only 30 min
            }
            
            if ($part != 'q' && $size < self::MINSIZE) {
                continue;
            }
            $mtime = filemtime(self::$filePathDir . '/' . $file);
            
            $fileMeta = new FileMeta();
            $fileMeta->year = $year;
            $fileMeta->month = $month;
            $fileMeta->day = $day;
            $fileMeta->part = $part;
            $fileMeta->size = $size;
            $fileMeta->file = $file;
            
            if ($mtime > $this->latestBuild) {
                $this->latestBuild = $mtime;
            }
            
            $collection->append($fileMeta);
            
        }
        return $collection;
        
    }
    
    /**
     * 
     * @param \ArrayObject $metadataCollection
     * @param string $showName
     * @throws \Exception
     */
    public function genShowWithMetaData(\ArrayObject $metadataCollection)
    {
        $show = array();
        
        /* @var $fileMeta FileMeta */
        foreach ($metadataCollection as $fileMeta) {
            
             $year = $fileMeta->year;
             $month = $fileMeta->month;
             $day = $fileMeta->day;
             $part = $fileMeta->part;
             $size = $fileMeta->size;
             $file = $fileMeta->file;
            
            if ($this->showName == 'p3popular' && ($part == '1' || $part == '2' || $part == 'm' || $part == 's')) {
                $title = "$year-$month-$day del $part ";
            } elseif ($this->showName == 'p1sommar' && ($part == 'p')) {
                // $title = "del 1"; //not many parts anymore
                $title = "";
            } elseif ($this->showName == 'p1sommar' && ($part == 'q')) {
                $title = "del 2";
            } else {
                continue;
            }
            
            switch ($part) {
                case 1:
                    $hour = 10;
                    $title .= strftime('%A', strtotime("$year-$month-$day"));
                    break;
                case 2:
                    $hour = 11;
                    $title .= strftime('%A', strtotime("$year-$month-$day"));
                    break;
                case 'm':
                    $hour = 13;
                    break;
                case 'p':
                    $hour = 13;
                    break;
                case 's':
                    $hour = 19;
                    break;
                case 'q':
                    $hour = 14;
                    break;
                
                default:
                    throw new \Exception('unknown part');
                    break;
            }
            $show[] = array(
                'title' => $title,
                'date' => "$year-$month-$day",
                'url' => "http://{$this->serverName}/" . self::$fileWebPath . "$file",
                'length' => $size,
                'pubDate' => date_create("$year-$month-$day $hour:00:00")->format(DATE_RSS)
                        );
        }
        
        $this->show = $show;
        return;
    }

    /**
     * @return \SimpleXMLElement
     */
    private static function getXmlForSommar()
    {
        $debug = false;
        
        // pod sommar more desc
        $url = 'http://api.sr.se/api/rss/pod/4023';
        
        if ($debug && isset($_SESSION['parse_str'])) { // DEBUG
            $str = $_SESSION['parse_str'];
        } else {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            $str = curl_exec($curl);
            curl_close($curl);
            $_SESSION['parse_str'] = $str;
        }
        $xml = new \SimpleXMLElement($str);
        return $xml;
    }
    
    public static function getInfoP1Sommar()
    {
        $xml = self::getXmlForSommar();
        
        $return = array();
        if (! $xml->channel->item) {
            return $return;
        }
        
        $empty = array();
        
        // /broadcast parser
        foreach ($empty as $e) {
            // foreach($xml->channel->item as $i){
            $title = (string) $i->title;
            $matches = array();
            $pattern = '/[Vinter|Sommar] i P1 med (.*?) \((.{4}-.{2}-.{2}).*/';
            $desc = (string) $i->description;
            preg_match($pattern, $title, $matches);
            if (! isset($matches[2])) {
                continue;
            }
            $date = $matches[2];
            $return[$date] = array(
                'title' => $matches[1] . ' ' . $matches[2],
                'desc' => $title
            ); // ."\n".$desc
        }
        
        // /pod parser
        foreach ($xml->channel->item as $i) {
            $title = (string) $i->title; // Karin Adelsköld
            $matches = array();
            $pattern = '/(.*?)sommar_i_p1_(.{4})(.{2})(.{2}).*/'; //@todo fix for winter
            $desc = (string) $i->description;
            
            preg_match($pattern, $i->guid, $matches);
            if (! isset($matches[2])) {
                continue;
            }
            
            $date = $matches[2] . '-' . $matches[3] . '-' . $matches[4];
            
            // add whitespace in desc
            $desc = self::lcucaddwhitespace($desc);
            $desc = self::lcucaddwhitespace($desc);
            $title .= ' ' . $date;
            $return[$date] = array(
                'title' => $title,
                'desc' => $desc
            );
        }
        
        unset($xml);
        $return = array_reverse($return);
        return $return;
    }
    
    /*
     * lower case upper case add whitespace between
     */
    public static function lcucaddwhitespace($desc)
    {
        $matches = array();
        $pattern = '/(.*?[a-z]{1})([A-Z]{1}.*)/'; // åäö not working
        preg_match($pattern, $desc, $matches);
        if (! isset($matches[1])) {
            return $desc;
        }
        $desc = $matches[1] . '. ' . $matches[2];
        
        return $desc;
    }

    public function getDownloadCodeSommar($info)
    {
        $finished = array();
        foreach ($this->show as $key => $s) {
            if (isset($info[$s['date']])) {
                $finished[$s['date']] = true;
            }
        }
        
        foreach ($info as $date => $i) {
            if (! isset($finished[$date])) {
                $title = $i['title'];
                echo "./downloadP3Popular.sh $date p #$title<br />";
            }
        }
    }
}