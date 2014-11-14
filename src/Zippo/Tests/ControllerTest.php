<?php

namespace Zippo\podcastFeed\Tests;

use Silex\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\HttpKernel;

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
        /* @var $exceptionHandler \Silex\ExceptionHandler */
        $exceptionHandler = $app['exception_handler'];
        $exceptionHandler->disable();
        
        return $app;
    }

    public function testInitialPage()
    {
        $client = $this->createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/');
        $this->assertMusikguiden($client, $crawler);
        
    }

    public function testMusikguiden()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p3popular');
        $this->assertMusikguiden($client, $crawler);
    }

    private function assertMusikguiden(Client $client, Crawler $crawler)
    {
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($crawler->getNode(0)->nodeName, 'rss');
        $this->assertXpathExist($crawler, 'rss/channel/description');
        $this->assertXpathHasText($crawler, 'rss/channel/title', 'P3 Populär');
    }

    public function testSommar()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p1sommar');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertXpathExist($crawler, 'rss/channel/description');
        $this->assertXpathHasText($crawler, 'rss/channel/title', 'Sommar i P1');
    }

    private function assertXpathExist(Crawler $crawler, $xpath)
    {
        $node = $crawler->filterXPath($xpath)->getNode(0);
        $this->assertTrue(!empty($node->nodeName), $xpath);
    }

    private function assertXpathHasText(Crawler $crawler, $xpath, $expectedText)
    {
        $node = $crawler->filterXPath($xpath)->getNode(0);
        $this->assertTrue(!empty($node->nodeName), $xpath);
        $this->assertEquals($expectedText, $node->textContent);
    }
}
