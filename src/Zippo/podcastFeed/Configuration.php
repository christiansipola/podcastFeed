<?php

namespace Zippo\podcastFeed;


class Configuration
{

    public $minsize;

    /**
     * often /tmp/
     * @var string
     */
    public $fullLocalPathToFiles;

    /**
     * often '' or 'podcast/'
     * @var string
     */
    public $urlPath;
}
