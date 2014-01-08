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
    $output = $c->musikguiden();
    $response = new Response($output, Response::HTTP_OK);
    $response->headers->set('Content-Type','application/xml');
    return $response;
});

$app->get('/p3popular', function () {
    $c = new Controller();
    $output = $c->musikguiden();
    $response = new Response($output, Response::HTTP_OK);
    $response->headers->set('Content-Type','application/xml');
    return $response;
});

$app->get('/p1sommar', function () {
    $c = new Controller();
    $output = $c->sommar();
    $response = new Response($output, Response::HTTP_OK);
    $response->headers->set('Content-Type','application/xml');
    return $response;
});
    
$app->run();
