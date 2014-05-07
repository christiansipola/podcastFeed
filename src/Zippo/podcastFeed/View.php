<?php

namespace Zippo\podcastFeed;

class View
{

    /**
     * 
     * @param Model $model
     * @return string
     */
    public function render(Model $model)
    {
        $show = $model->show;
        if (empty($model->latestBuild)) {
            $model->latestBuild = time();
        }
        $build = date_create('@' . $model->latestBuild)->format(DATE_RSS);
        $xml = new \DOMDocument('1.0', 'UTF-8');
        // we want a nice output
        $xml->formatOutput = true;
        /*
         * <hrxml xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="file:LonXML.xsd">
         */
        $rssNode = $xml->createElement('rss');
        $xml->appendChild($rssNode);
        
        $rssNode->setAttribute('version', '2.0');
        $rssNode->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
        
        $channel = $rssNode->appendChild($xml->createElement('channel'));
        
        $channel->appendChild($xml->createElement('title', 'P3 Populär'));
        $channel->appendChild($xml->createElement('description', 'P3 Populär podcast'));
        $channel->appendChild($xml->createElement('link', "http://{$model->serverName}/podcastFeed/"));
        $channel->appendChild($xml->createElement('language', 'sv-se'));
        $channel->appendChild($xml->createElement('copyright', 'Sveriges Radio'));
        $channel->appendChild($xml->createElement('lastBuildDate', $build));
        
        $channel->appendChild($xml->createElement('itunes:author', 'zippo@sovjet.sipola.se'));
        $channel->appendChild($xml->createElement('itunes:subtitle', 'Ripped podcast'));
        $channel->appendChild($xml->createElement('itunes:explicit', 'no'));
        $image = $channel->appendChild($xml->createElement('itunes:image'));
        $image->appendChild($xml->createElement('title', 'P3 Populär'));
        $image->appendChild($xml->createElement('link', 'http://www.sr.se/sida/default.aspx?ProgramId=2785'));
        $image->appendChild($xml->createElement('url', 'http://www.sr.se/diverse/images/sr_14_90_90.jpg'));
        
        $image = $xml->createElement('itunes:image');
        $channel->appendChild($image);
        $image->setAttribute('href', 'http://www.sr.se/diverse/images/sr_14_300_300.jpg');
        
        $category = $xml->createElement('itunes:category');
        $channel->appendChild($$category);
        $category->setAttribute('text', 'Technology');
        
        foreach ($show as $s) {
            $item = $channel->appendChild($xml->createElement('item'));
            $item->appendChild($xml->createElement('title', $s['title']));
            $item->appendChild($xml->createElement('link', 'http://sverigesradio.se/p3popular'));
            $item->appendChild($xml->createElement('guid', $s['url']));
            $item->appendChild($xml->createElement('description', 'P3 Populär ' . $s['title']));
            $enc = $xml->createElement('enclosure');
            $item->appendChild($enc);
            $enc->setAttribute('url', $s['url']);
            $enc->setAttribute('length', $s['length']);
            $enc->setAttribute('type', 'audio/mpeg');
            $item->appendChild($xml->createElement('category', 'Podcasts'));
            $item->appendChild($xml->createElement('pubDate', $s['pubDate']));
        }
        
        return $xml->saveXML();
    }

    /**
     *
     * @param Model $model            
     * @param array $info            
     * @return string
     */
    public function renderP1Sommar(Model $model, $info)
    {
        $show = $model->show;
        if (empty($model->latestBuild)) {
            $model->latestBuild = time();
        }
        $build = date_create('@' . $model->latestBuild)->format(DATE_RSS);
        $xml = new \DOMDocument('1.0', 'UTF-8');
        // we want a nice output
        $xml->formatOutput = true;
        /*
         * <hrxml xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         * xsi:noNamespaceSchemaLocation="file:LonXML.xsd">
         */
        $rssNode = $xml->createElement('rss');
        $xml->appendChild($rssNode);
        
        $rssNode->setAttribute('version', '2.0');
        $rssNode->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
        
        $channel = $rssNode->appendChild($xml->createElement('channel'));
        
        $channel->appendChild($xml->createElement('title', 'Sommar i P1'));
        $channel->appendChild($xml->createElement('description', 'Sommar i P1 podcast'));
        $channel->appendChild($xml->createElement('link', 'http://podcast.sipola.se/podcastFeed/p1sommar'));
        $channel->appendChild($xml->createElement('language', 'sv-se'));
        $channel->appendChild($xml->createElement('copyright', 'Sveriges Radio'));
        $channel->appendChild($xml->createElement('lastBuildDate', $build));
        
        $channel->appendChild($xml->createElement('itunes:author', 'zippo@sovjet.sipola.se'));
        $channel->appendChild($xml->createElement('itunes:subtitle', 'Ripped podcast'));
        $channel->appendChild($xml->createElement('itunes:explicit', 'no'));
        
        $image = $channel->appendChild($xml->createElement('itunes:image'));
        $image->appendChild($xml->createElement('title', 'Sommar i P1'));
        $image->appendChild($xml->createElement('link', 'http://sverigesradio.se/sida/default.aspx?programid=2071'));
        $image->appendChild($xml->createElement('url', 'http://sverigesradio.se/diverse/images/srlogo-2011.png'));
        
        foreach ($show as $s) {
            $item = $channel->appendChild($xml->createElement('item'));
            if (! isset($info[$s['date']])) {
                $info[$s['date']] = array(
                    'title' => $s['date'],
                    'desc' => $s['date']
                );
            }
            $item->appendChild($xml->createElement('title', $info[$s['date']]['title'] . ' ' . $s['title']));
            $item->appendChild($xml->createElement('link', 'http://sverigesradio.se/sida/default.aspx?programid=2071'));
            $item->appendChild($xml->createElement('guid', $s['url']));
            $item->appendChild($xml->createElement('description', $info[$s['date']]['desc'] . ' ' . $s['title']));
            $enc = $xml->createElement('enclosure');
            $item->appendChild($enc);
            $enc->setAttribute('url', $s['url']);
            $enc->setAttribute('length', $s['length']);
            $enc->setAttribute('type', 'audio/mpeg');
            $item->appendChild($xml->createElement('category', 'Podcasts'));
            $item->appendChild($xml->createElement('pubDate', $s['pubDate']));
        }
        return $xml->saveXML();
    }
}
