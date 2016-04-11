<?php

namespace Zippo\podcastFeed;

use SimpleXMLElement;

class Model
{

    const SHOW_P1SOMMAR = 'p1sommar';
    
    /**
     * @var Configuration
     */
    public $configuration;
    
    
    public $latestBuild;

    /**
     * @var Show[]
     */
    private $show;

    /**
     * @return Show[]
     */
    public function getShowList()
    {
        return $this->show;
    }
    
    /**
     * 
     * @var string
     */
    public $showName;

    /**
     * $_SERVER["SERVER_NAME"]
     */
    public $serverName = null;

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
     * @return \ArrayObject
     * @throws \Exception
     */
    private function getFileMetaCollection()
    {
        if (! is_dir($this->configuration->fullLocalPathToFiles)) {
            throw new \Exception('directory ' . $this->configuration->fullLocalPathToFiles . ' does not exist');
        }
        chdir($this->configuration->fullLocalPathToFiles);
        $dir = scandir($this->configuration->fullLocalPathToFiles);
        
        $collection = new \ArrayObject();
        
        foreach ($dir as $filename) {
            $fileMeta = $this->getFileMetaFromFilename($filename);
            if ($fileMeta instanceof FileMeta) {
                $collection->append($fileMeta);
            }
            
        }
        return $collection;
        
    }
    
    /**
     * 
     * @param \ArrayObject|FileMeta[] $metadataCollection
     * @throws \Exception
     */
    public function genShowWithMetaData(\ArrayObject $metadataCollection)
    {
        $this->show = [];
        foreach ($metadataCollection as $fileMeta) {
            $show = $this->getShowFromFileMeta($fileMeta);
            if ($show instanceof Show) {
                $this->show[] = $show;
            }
        }
        return;
    }

    /**
     * @param FileMeta $fileMeta
     * @return Show
     * @throws \Exception
     */
    private function getShowFromFileMeta(FileMeta $fileMeta)
    {
        
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
            return null;
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
        }
        return new Show(
            $title,
            "$year-$month-$day",
            "http://{$this->serverName}/" . $this->configuration->urlPath . "files/$file",
            $size,
            date_create("$year-$month-$day $hour:00:00")->format(DATE_RSS)
        );
    }

    /**
     * @return SimpleXMLElement
     */
    private static function getXmlForSommar()
    {
        /*
         * old url (redirect to below?)
        // http://api.sr.se/api/rssfeed/rssfeed.aspx?lyssnaigenfeed=2071
        // broadcast. original byut short desc
        // http://sverigesradio.se/api/rss/broadcast/2071
         */
        // pod sommar more desc
        $url = 'http://api.sr.se/api/rss/pod/4023';
        return self::getXmlForUrl($url);
    }

    /**
     * @param string $url
     * @return SimpleXMLElement
     */
    public static function getXmlForUrl($url)
    {
        $str = self::getStringForUrl($url);
        $xml = new SimpleXMLElement($str);
        return $xml;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getStringForUrl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $str = curl_exec($curl);
        curl_close($curl);
        return $str;
    }
    
    
    public static function getInfoP1Sommar()
    {
        $cache = new Cache();
        $info = $cache->getP1Cache();
        if (empty($info)) {
            $info = self::downloadInfoP1SommarVinter();
            $cache->writeToCache($info);
        }
        return $info;
    }

    /**
     * @return array
     */
    private static function downloadInfoP1SommarVinter()
    {
        $xml = self::getXmlForSommar();
        
        $return = array();
        if (! $xml->channel->item) {
            return $return;
        }
        
        $empty = array();
        
        // /broadcast parser
        foreach ($empty as $i) {
            // foreach($xml->channel->item as $i){ //no broadcast
            $title = (string) $i->title;
            $matches = array();
            $pattern = '/[Vinter|Sommar] i P1 med (.*?) \((.{4}-.{2}-.{2}).*/';
            // $i->description is not used
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
            $pattern = '/(.*?)[sommar[vinter]_i_p1_(.{4})(.{2})(.{2}).*/';
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

    /**
     * @param array $info
     * @return string
     */
    public function getDownloadCodeSommar($info)
    {
        $finished = array();
        $showList = $this->getShowList();
        foreach ($showList as $s) {
            if (isset($info[$s->getDate()])) {
                $finished[$s->getDate()] = true;
            }
        }
        
        $string = '';
        foreach ($info as $date => $i) {
            if (! isset($finished[$date])) {
                $title = $i['title'];
                $string .= "./downloadP3Popular.sh $date p #$title<br />";
            }
        }
        
        return $string;
    }

    /**
     * @param string $filename
     * @return FileMeta
     */
    private function getFileMetaFromFilename($filename)
    {
        $start = substr(strtolower($filename), 0, 7);
        if ($start != 'podcast') {
            return null;
        }
        $size = filesize($this->configuration->fullLocalPathToFiles . $filename);
        $tmp = explode("-", $filename);
        if (count($tmp) < 4) {
            return null;
        }
        $part = substr($tmp[4], 0, 1);
        $year = $tmp[1];
        $month = $tmp[2];
        $day = $tmp[3];
        if ($part == 'q' && $size < ($this->configuration->minsize / 2)) {
            return null; // second part of podcast is only 30 min
        }

        if ($part != 'q' && $size < $this->configuration->minsize) {
            return null;
        }
        $mtime = filemtime($this->configuration->fullLocalPathToFiles . $filename);

        $fileMeta = new FileMeta();
        $fileMeta->year = $year;
        $fileMeta->month = $month;
        $fileMeta->day = $day;
        $fileMeta->part = $part;
        $fileMeta->size = $size;
        $fileMeta->file = $filename;

        if ($mtime > $this->latestBuild) {
            $this->latestBuild = $mtime;
        }
        return $fileMeta;
    }
}
