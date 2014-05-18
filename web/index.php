<?php 

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
// definitions
$app['debug'] = true;

require __DIR__.'/../config/app.php';
    
$app->run();
