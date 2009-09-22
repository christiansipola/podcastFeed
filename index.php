<?


class Model{
	
	
	
}
class Controller{
	
	public function index(){
		
		$v = new View();
		$v->render();
	}
	
}
class View{
	
	public function render(){
		
		$show[] = array(
			'title' => '2009-09-18 del 1',
			'url'	=> 'http://p3popular.sipola.se/p3Populär-2009-09-18-1.mp3',
			'length' => '83593008',
			'pubDate' => date_create('2009-09-18 13:15:00')->format(DATE_RSS)
			
		);
		
		$build = date_create('now')->format(DATE_RSS);
		$pub = date_create('now')->format(DATE_RSS);
		$xml = new DOMDocument('1.0', 'UTF-8');
		// we want a nice output
		//$xml->formatOutput = true;
		/*
		 * <hrxml xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		 *	xsi:noNamespaceSchemaLocation="file:LonXML.xsd">
		 */
		$rssNode = $xml->appendChild( $xml->createElement('rss') );

		$rssNode->setAttribute('version',  '2.0' );
		$rssNode->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
		
		$channel = $xml->appendChild( $xml->createElement('channel') );
		
		$channel->appendChild( $xml->createElement('title','P3 Populär') );
		$channel->appendChild( $xml->createElement('description','P3 Populär podcast') );
		$channel->appendChild( $xml->createElement('link','http://localhost:8888/podcastFeed/') );
		$channel->appendChild( $xml->createElement('language','sv-se') );
		$channel->appendChild( $xml->createElement('copyright','Sveriges Radio') );
		$channel->appendChild( $xml->createElement('lastBuildDate',$build) );
		$channel->appendChild( $xml->createElement('pubDate',$pub) );
		
		$channel->appendChild( $xml->createElement('itunes:author','zippo@sovjet.sipola.se') );
		$channel->appendChild( $xml->createElement('itunes:subtitle','Ripped podcast') );
		$channel->appendChild( $xml->createElement('itunes:explicit','No') );
		$channel->appendChild( $xml->createElement('itunes:image','http://www.sr.se/Diverse/AppData/isidor/images/News_images/2785/526933_760_117.jpg') );
		
		foreach($show as $s){
			$item = $channel->appendChild( $xml->createElement('item') );
			$item->appendChild( $xml->createElement('title',$s['title']) );
			$item->appendChild( $xml->createElement('link','http://sr.se/p3popular') );
			$item->appendChild( $xml->createElement('guid',$s['url']) );
			$item->appendChild( $xml->createElement('description','P3 Populär '.$s['title']) );
			$enc = $item->appendChild( $xml->createElement('enclosure') );
			$enc->setAttribute('url',$s['url']);
			$enc->setAttribute('length',$s['length']);
			$enc->setAttribute('type','audio/mpeg');
			$item->appendChild( $xml->createElement('category','Podcasts') );
			$item->appendChild( $xml->createElement('pubDate',$s['pubDate']) );
			
		}
		header('Content-Type: application/octet-stream');
		echo $xml->saveXML();
		#error_log($xml->saveXML());
	}
}


$c = new Controller();
$c->index();