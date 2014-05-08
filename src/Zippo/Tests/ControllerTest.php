<?php

namespace Zippo\podcastFeed\Tests;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernel;

class ControllerTest extends WebTestCase

    public function createApplication()
    {
        $app = new HttpKernel();
        $app = require __DIR__.'../../web/index.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();
        
        return $app;
    }
} 