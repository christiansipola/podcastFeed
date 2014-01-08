<?php

namespace Zippo;


class Controller
{

    public function __construct()
    {
        setlocale(LC_TIME, "sv_SE.UTF-8", "sv_SE");
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'htdocs.local') {
            Model::$filePathDir = Model::DIR_LOCAL;
        } else {
            Model::$filePathDir = Model::DIR_WEB;
            Model::$fileWebPath = '';
        }
    }
    
    public function musikguiden()
    {
        $m = new Model();
        $m->serverName = $_SERVER['SERVER_NAME'];
        $v = new View();
        $m->getShow('p3popular');
        $v->render($m);
    }
    
    public function sommar()
    {
        $m = new Model();
        $m->serverName = $_SERVER['SERVER_NAME'];
        $v = new View();
        $m->getShow('p1sommar');
        $info = Model::getInfoP1Sommar();
        $v->renderP1Sommar($m, $info);
    }
}
