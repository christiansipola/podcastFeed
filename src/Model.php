<?php

namespace Zippo;

class Model
{
    
    // on localhost
    const DIR = '/srv/unofficial/podcastFeed/radio';

    const DIR_LOCAL = '/srv/unofficial/podcastFeed/radio';
    // on web
    const DIR_WEB = '/home/ftp/Radio/p3popular';
    // const MINSIZE = '81095911';
    // const MINSIZE = '69170187'; //22:00
    // const MINSIZE = '43000000'; //19:30
    const MINSIZE = '32000000'; // 20:30
    public static $filePathDir = null;
    
    // on localhost
    public static $fileWebPath = 'podcastFeed/radio/';
    // on web
    // public static $fileWebPath = '';
    public $latestBuild;

    public $show;

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
            echo '<pre>';
            var_dump($matches);
            echo '</pre>';
            die();
            $show[] = array(
                'title' => '2009-09-18 del 1',
                'url' => 'http://p3popular.sipola.se/p3Populär-2009-09-18-1.mp3',
                'length' => '83593008',
                'pubDate' => date_create('2009-09-18 13:15:00')->format(DATE_RSS)
            )
            ;
        }
    }

    /**
     *
     * @param string $show
     *            p1sommar or p3popular or p3musik
     */
    public function getShow($showName)
    {
        if (! is_dir(self::$filePathDir)) {
            throw new \Exception('directory ' . self::$filePathDir . ' does not exist');
        }
        chdir(self::$filePathDir);
        $dir = scandir(self::$filePathDir);
        $show = array();
        
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
            if ($mtime > $this->latestBuild) {
                $this->latestBuild = $mtime;
            }
            
            if ($showName == 'p3popular' && ($part == '1' || $part == '2' || $part == 'm' || $part == 's')) {
                $title = "$year-$month-$day del $part ";
            } elseif ($showName == 'p1sommar' && ($part == 'p')) {
                // $title = "del 1"; //not many parts anymore
                $title = "";
            } elseif ($showName == 'p1sommar' && ($part == 'q')) {
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
            // 'pubDate' => date_create("@$mtime")->format(DATE_RSS)
                        );
            // print_r($show);die();
        }
        // print_r($dir);
        
        $this->show = $show;
        return;
    }

    public static function getInfoP1Sommar()
    {
        $debug = false;
        
        ini_set('user_agent', 'Void/2.5');
        
        // list
        // $url = 'http://api.sr.se/api/rssfeed/rssfeed.aspx?lyssnaigenfeed=2071'; //old url
        // broadcast. original byut short desc
        // $url = 'http://sverigesradio.se/api/rss/broadcast/2071';
        
        // pod sommar more desc
        $url = 'http://api.sr.se/api/rss/pod/4023';
        
        // program little more desc. atom format
        // $url = 'http://api.sr.se/api/rss/program/2071';
        
        if ($debug && isset($_SESSION['parse_str'])) { // DEBUG
            $str = $_SESSION['parse_str'];
        } else {
            // echo "fetch!";
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            $str = curl_exec($curl);
            curl_close($curl);
            $_SESSION['parse_str'] = $str;
            // echo '<pre>';
            // echo htmlentities($str);
            // echo $str;
            // echo '</pre>';
            // die();
        }
        
        $return = array();
        
        // echo $str;die();
        
        $xml = new \SimpleXMLElement($str);
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
            $pattern = '/(.*?)sommar_i_p1_(.{4})(.{2})(.{2}).*/'; // todo fix for winter
            $desc = (string) $i->description;
            
            preg_match($pattern, $i->guid, $matches);
            if (! isset($matches[2])) {
                continue;
            }
            
            $date = $matches[2] . '-' . $matches[3] . '-' . $matches[4];
            
            // add whitespace in desc
            // $desc = 'blöÅr blåÄr'; //for testing
            // $desc = 'bloAr blaAr'; //for testing
            $desc = self::lcucaddwhitespace($desc);
            $desc = self::lcucaddwhitespace($desc);
            $title .= ' ' . $date;
            $return[$date] = array(
                'title' => $title,
                'desc' => $desc
            );
            // var_dump($desc);die();
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
        // $pattern = '/(.*?[[:lower:]]{1})([[:upper:]]{1}.*)/'; //åäö nåt working.
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
                // echo "./downloadP3Popular.sh $date q #$title<br />"; //not many parts anymore
            }
        }
    }
}