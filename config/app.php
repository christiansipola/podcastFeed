<?php

use Symfony\Component\HttpFoundation\Response;

use Zippo\podcastFeed\Controller;

$app = new Silex\Application();
// definitions
$app['debug'] = true;

date_default_timezone_set('Europe/Stockholm');

$app->get('/', function () use ($app) {
        return $app->redirect('/p3popular');
});

$app->get('/p3popular', function () {
        $c = new Controller();
        $output = $c->musikguiden();
        $response = new Response($output, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
});

$app->get('/p1sommar', function () {
        $c = new Controller();
        $output = $c->sommar();
        $response = new Response($output, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
});

$app->get('/files/{file}', function ($file) use ($app) {
    $c = new Controller();
    if (!file_exists($c->configuration->fullLocalPathToFiles . $file)) {
        $app->abort(404);
    }
    return $app->sendFile($c->configuration->fullLocalPathToFiles . $file);
});
