<?php

namespace Zippo\podcastFeed\Tests;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Client;

class ControllerTest extends WebTestCase
{

    /**
     * @return HttpKernel
     */
    public function createApplication()
    {
        /* @var $app HttpKernel */
        require __DIR__.'/../../../config/app.php';
        $app['debug'] = true;
        $app['session.test'] = true;
        $app['exception_handler']->disable();
        
        return $app;
    }

    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($crawler->getNode(0)->nodeName, 'rss');
    }

    public function testMusikguiden()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p3popular');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($crawler->getNode(0)->nodeName, 'rss');
    }

    public function testSommar()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p1sommar');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($crawler->getNode(0)->nodeName, 'rss');
    }
}
