<?php 

function resrc($url) {
	$name = kirbytext::classname();
	$obj = new $name;

	return $obj->resrc( array(
		'url' => $url
	) );
}

class kirbytextExtended extends kirbytext {
	function __construct($text = false, $markdown=true) {
		parent::__construct($text, $markdown);

		$this->addTags('resrc');
	} 

	function resrc($params) {
		$url = @$params['url'];

		$version_of_resrc = c::get('resrc.plan') ? c::get('resrc.plan') : 'app';

		return c::get('resrc') ? 'http://' . $version_of_resrc . '.resrc.it/' . $url : $url;
	}
}