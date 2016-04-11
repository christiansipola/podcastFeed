#!/usr/bin/env php
<?php
// http://symfony.com/doc/current/components/console/introduction.html

require __DIR__.'/vendor/autoload.php';

use Zippo\Console\Command\DownloadP1SommarVinter;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new DownloadP1SommarVinter());
$application->run();
