<?php

namespace Zippo\Console\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zippo\podcastFeed\Cache;
use Zippo\podcastFeed\Model;

class DownloadP1SommarVinter extends Command
{
    protected function configure()
    {
        $this
            ->setName('download:p1sommar')
            ->setDescription('ladda ner p1sommar')
            ->addArgument('date', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getArgument('date');
        if ($date) {
            $dateTime = new DateTime($date);
        } else {
            $dateTime = new DateTime('today');
        }

        $cache = new Cache();
        $data = $cache->getP1Cache();
        if (count($data) == 0) {
            $url = 'http://sverigesradio.se/sida/avsnitt?programid=2071';
            #$url = 'http://sverigesradio.se/sida/ajax/getplayerinfo?url=http%3A%2F%2Fsverigesradio.se%2Fsida%2Favsnitt%2F562300%3Fprogramid%3D2071%26playepisode%3D562300';
            $str = Model::getStringForUrl($url);
            $data = explode("\n", $str);
            $cache->writeToCache($data);
        } else {
            $output->writeln('found cache');
        }
        //http://lyssnaigen.sr.se/isidor/ereg/webb_rh_sthlm/2015/06/47_sommar_i_p1_med_david_bat_34b3756_a192.m4a
        $pattern = "/^47_sommar_i_p1(.*)\.a96$/";
        $nameList = preg_grep($pattern, $data);
        
        $output->writeln(var_export($data));
    }
}
