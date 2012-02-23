<?php 

	// Version: 1
	// Date: 14.02.2012
	// Author: Thibaut Ninove <http://wooconcept.com>, with the help of Bastian Allgeier
	// Support: provided as is, no support provided

	class kirbytextExtended extends kirbytext {
	
		function __construct($text, $markdown=true) {
		
			parent::__construct($text, $markdown);
			
			$this->addTags('sublime');
			$this->addAttributes('poster', 'video1', 'video2');
	
		}
	
		function sublime($params) {
		
			global $site;
			
			$page = $site->pages()->active();                
			$file = url($page->diruri() . '/' . $params['sublime']);
			
			$defaults = array(
				'poster' => 'jpg',
				'video1' => 'mp4',
				'video2' => 'webm',
				'width' => '',
				'height' => ''
			);
			
			$options = array_merge($defaults, $params);
			
			$poster = $file . '.' . html($options['poster']);
			$video1 = $file . '.' . html($options['video1']);
			$video2 = $file . '.' . html($options['video2']);
			$width  = html($options['width']);
			$height = html($options['height']);
			
			return '<video class="sublime" poster="' . $poster . '" width="' . $width . '" height="' . $height . '"><source src="' . $video1 . '"><source src="' . $video2 . '"></video>';
			
		}
	
	}

?>