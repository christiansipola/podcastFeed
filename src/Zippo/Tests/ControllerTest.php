<?php

namespace Zippo\podcastFeed\Tests;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernel;

class ControllerTest extends WebTestCase
{

    /**
     * @return HttpKernel
     */
    public function createApplication()
    {
        //$app = new HttpKernel();
        /* @var $app HttpKernel */
        $app = require __DIR__.'/../../../web/index.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();
        
        return $app;
    }
}
