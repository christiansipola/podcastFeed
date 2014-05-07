<?php

namespace Zippo;

class Controller
{

    /**
     * @var Configuration
     */
    public $configuration;
    
    public function __construct()
    {
        setlocale(LC_TIME, "sv_SE.UTF-8", "sv_SE");
        $this->configuration = new Configuration();
        $this->configuration->fullLocalPathToFiles = '/tmp/';
        
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'htdocs.local') {
            $this->configuration->minsize = 0;
            $this->configuration->urlPath = 'podcastFeed/';
        } else {
            $this->configuration->minsize = 32000000;
            $this->configuration->urlPath = '';
        }
    }
    
    public function musikguiden()
    {
        $m = new Model();
        $m->configuration = $this->configuration;
        $m->serverName = $_SERVER['SERVER_NAME'];
        $v = new View();
        $m->genShowP3musikguiden();
        return $v->render($m);
    }
    
    public function sommar()
    {
        $m = new Model();
        $m->configuration = $this->configuration;
        $m->serverName = $_SERVER['SERVER_NAME'];
        $v = new View();
        $m->genShowP1Sommar();
        $info = Model::getInfoP1Sommar();
        return $v->renderP1Sommar($m, $info);
    }
}
