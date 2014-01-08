<?php 

use Symfony\Component\HttpFoundation\Response;

use Zippo\Controller;

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
// definitions
$app['debug'] = true;

$app->get('/', function () {
    $c = new Controller();
    $c->musikguiden();
    return new Response('', 200);
});

$app->get('/p3popular', function () {
    $c = new Controller();
    $c->musikguiden();
    return new Response('', 200);
});

$app->get('/p1sommar', function () {
    $c = new Controller();
    $c->sommar();
    return new Response('', 200);
});
    
$app->run();
