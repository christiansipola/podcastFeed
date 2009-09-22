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
		$xml = new DOMDocument('1.0', 'UTF-8');
		// we want a nice output
		$xml->formatOutput = true;
		/*
		 * <hrxml xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		 *	xsi:noNamespaceSchemaLocation="file:LonXML.xsd">
		 */
		$rssNode = $xml->appendChild( $xml->createElement('rss') );

		$rssNode->setAttribute('version',  '2.0' );
		$rssNode->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
		
		echo $xml->saveXML();
	}
}


$c = new Controller();
$c->index();