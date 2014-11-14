<?php

namespace Zippo\podcastFeed;

class Show
{

    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $date;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $length;
    /**
     * @var string
     */
    private $pubDate;

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $title
     * @param string $date
     * @param string $url
     * @param string $length
     * @param string $pubDate
     */
    function __construct($title, $date, $url, $length, $pubDate)
    {
        $this->title = $title;
        $this->date = $date;
        $this->url = $url;
        $this->length = $length;
        $this->pubDate = $pubDate;
    }
} 