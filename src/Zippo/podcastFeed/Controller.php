<?php

namespace Zippo\podcastFeed;

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
        $model = new Model();
        $model->configuration = $this->configuration;
        $model->serverName = $this->genServername();
        $view = new View();
        $model->genShowP3musikguiden();
        return $view->render($model);
    }

    /**
     * @return string
     */
    private function genServername()
    {
        if (isset($_SERVER['SERVER_NAME'])) {
            $serverName = $_SERVER['SERVER_NAME'];
        } else {
            $serverName = 'dummy';
        }
        return $serverName;
    }
    
    public function sommar()
    {
        $model = new Model();
        $model->configuration = $this->configuration;
        $model->serverName = $this->genServername();
        $view = new View();
        $model->genShowP1Sommar();
        $info = Model::getInfoP1Sommar();
        return $view->renderP1Sommar($model, $info);
    }
}
