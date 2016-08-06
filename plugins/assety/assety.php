<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

if(c::get('version.number') >= 1.11) {

	// collect compressed assets
	if(isset($_GET["asset"])) {
		$url = c::get('root.cache') . '/' . $_GET["asset"];
		if(pathinfo($url, PATHINFO_EXTENSION) == "css") {
			$contenttype = "text/css";
		} else {
			$contenttype = "text/javascript";
		}
	  header("Content-Type: " . $contenttype);
	  die(file_get_contents($url));
	} else if(isset($_GET["assets"])) {
		foreach(explode(",", $_GET["assets"]) as $url) {
			$data = explode(":", $url);
			$url = $data[0];
			$media = $data[1];
			if(pathinfo($url, PATHINFO_EXTENSION) == "css") {
				$contenttype = "text/css";
			} else {
				$contenttype = "text/javascript";
			}
			if(!isset($first)) header("Content-Type: " . $contenttype);
			if($media != "") {
				echo "@media $media {" . file_get_contents($url) . "}";
			} else {
		  	echo file_get_contents($url);
		  }
		  $first = false;		
	  }
	  die();
	}
	
	// functions have been disabled in helpers.php before, now add them again and let them call Assety functions
	function css($url=false, $media=-1, $queue=-1, $parser=0) {
	  return assety::css($url, $media, $queue, $parser);
	}
	function less($url=false, $media=-1, $queue=-1) {
	  return assety::less($url, $media, $queue);
	}
	// SASS parser, not working yet! Looking for a easy-to-use PHP class for compiling.
	function sass($url=false, $media=-1, $queue=-1) {
		return assety::sass($url, $media, $queue);
	}
	function scss($url=false, $media=-1, $queue=-1) {
		return assety::scss($url, $media, $queue);
	}
	function js($url=false, $queue=-1) {
	  return assety::js($url, $queue);
	}
	
	class assety {
	  // contains if assety::get() has been called to output the tag automatically if not
	  public static $not = true;
	  // queue for combining and caching
	  static $queue = array(
	  	"css" => array(),
	  	"js" => array(
	  		false => array()
	  	)
	  );
	  
	 // ================
	 // Adding functions
	 // ================
	  
	  // embed a stylesheet tag & add to queue
	  static function css($url=false, $media=-1, $queue=true, $parser=0) {
	  	// set queuing to false if caching of CSS is disabled
	  	if(!c::get('assety.css')) {
		  	$queue = false;
	  	}
	  	
	    // alias for assety::getcss() is assety::css() without params
	    if($url == false) {
	      return assety::getcss();
	    }
	    if($queue == true) {
	  	  // add this file to the queue
	  	  if(!isset($queue["css"][$media])) {
		  	  self::$queue["css"][$media] = array();
	  	  }
	  	  self::$queue["css"][$media][$url] = $parser;
	  	  return "";
	    } 
	    // use if $queue = false
	    $url = (str::contains($url, 'http://') || str::contains($url, 'https://')) ? $url : url(ltrim($url, '/'));
	    if(!empty($media)) {
	      return '<link rel="stylesheet" media="' . $media . '" href="' . $url . '" />' . "\n";
	    } else {
	      return '<link rel="stylesheet" href="' . $url . '" />' . "\n";
	    }
	  }
	  
	  // embed a stylesheet tag with LESS and cache the compiled CSS
	  static function less($url=false, $media=-1, $queue=true) {
	  	// set queuing to false if caching of CSS is disabled
	  	if(!c::get('assety.css')) {
		  	$queue = false;
	  	}
	  	
		  // does only work if cache is enabled (LESS will be compiled, will take too long if made without caching)
	    if(!c::get('cache')) {
	      return false;
	    }
	    
	    // parse the LESS directly if the user does not want to add it to queue
	    if($queue == false) {
		    $parsed = self::parse($url, 1);
		    $url = '/?asset=' . self::cachefile($url, false, "css");
		    if(!empty($media)) {
		      return '<link rel="stylesheet" media="' . $media . '" href="' . $url . '" />' . "\n";
		    } else {
		      return '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		    }
	    }
	    
	    return self::css($url, $media, $queue, 1);
	  }
	  
	  // not working yet! Looking for a easy-to-use PHP class for compiling.
	  static function sass($url=false, $media=-1, $queue=true) {
	  	// set queuing to false if caching of CSS is disabled
	  	if(!c::get('assety.css')) {
		  	$queue = false;
	  	}
	  	
		  // does only work if cache is enabled (SASS will be compiled, will take too long if made without caching)
	    if(!c::get('cache')) {
	      return false;
	    }
	    
	    // parse the SASS directly if the user does not want to add it to queue
	    if($queue == false) {
		    $parsed = self::parse($url, 2);
		    $url = '/?asset=' . self::cachefile($url, false, "css");
		    if(!empty($media)) {
		      return '<link rel="stylesheet" media="' . $media . '" href="' . $url . '" />' . "\n";
		    } else {
		      return '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		    }
	    }
	    
	    return self::css($url, $media, $queue, 2);
	  }
	  static function scss($url=false, $media=-1, $queue=true) {
	  	// set queuing to false if caching of CSS is disabled
	  	if(!c::get('assety.css')) {
		  	$queue = false;
	  	}
	  	
		  // does only work if cache is enabled (SCSS will be compiled, will take too long if made without caching)
	    if(!c::get('cache')) {
	      return false;
	    }
	    
	    // parse the SCSS directly if the user does not want to add it to queue
	    if($queue == false) {
		    $parsed = self::parse($url, 3);
		    $url = '/?asset=' . self::cachefile($url, false, "css");
		    if(!empty($media)) {
		      return '<link rel="stylesheet" media="' . $media . '" href="' . $url . '" />' . "\n";
		    } else {
		      return '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		    }
	    }
	    
	    return self::css($url, $media, $queue, 3);
	  }
	  	  
	  // embed a js tag & add to queue
	  static function js($url=false, $queue=true) {
	  	// set queuing to false if caching of JS is disabled
	  	if(!c::get('assety.js')) {
		  	$queue = false;
	  	}
	  	
	    // alias for assety::getjs() is assety::js() without params
	    if($url == false) {
	      return assety::getjs();
	    }
	    
	    if($queue == true) {
	      // add this file to the queue
	      self::$queue["js"][false][$url] = 0;
	      return "";
	    } 
	    $url = (str::contains($url, 'http://') || str::contains($url, 'https://')) ? $url : url(ltrim($url, '/'));
	    return '<script src="' . $url . '"></script>' . "\n";
	  }
	 
	 // =================
	 // Getting functions
	 // =================
	 
	  static function get($indent='', $media=array()) {
	    return self::getcss($indent, $media) . self::getjs($indent);
	  }
	
	  // get collective links
	  private function getcss($indent='', $media=array()) {
	  	self::$not = false;
	  
	  	$url = self::create("css", $media);
	  	if($url == false) return "";
	  	if(is_array($url)) {
	  		$buffer = "";
	  		foreach($url as $media => $formedia) {
		  		foreach($formedia["files"] as $urlnow => $data) {
		  			if($buffer != "") {
			  			$buffer .= ",";
		  			}
		  			if($media != -1) {
		  				$buffer .= $urlnow . ':' . $media;
		  			} else {
			  			$buffer .= $urlnow . ':';
		  			}
			 		}
			 	}
		  	return $indent . '<link rel="stylesheet" href="/?assets=' . $buffer . '" />' . "\n";
	  	}
	  	
	    return $indent . '<link rel="stylesheet" href="' . $url . '" />' . "\n";
	  }
	  
	  private function getjs($indent="") {
	  	self::$not = false;
	  
	  	$url = self::create("js");
	  	if($url == false) return "";
	  	if(is_array($url)) {
	  		$buffer = "";
		  	foreach($url[false]["files"] as $urlnow => $data) {
	  			if($buffer != "") {
		  			$buffer .= ",";
	  			}
	  			$buffer .= $urlnow . ':';
		  	}
		  	return $indent . '<script src="/?assets=' . $buffer . '"></script>' . "\n";
	  	}
	  	
	    return $indent . '<script src="' . $url . '"></script>' . "\n";
	  }
	  
	 // =========================
	 // Static minifier functions
	 // =========================

	  static function cssminify($content) {
	    return self::compress($content, 'css', true);
	  }
	  static function jsminify($content) {
	    return self::compress($content, 'js', true);
	  }
	  static function htmlminify($content) {
	    return self::compress($content, 'html', true);
	  }
	  
	 // ====
	 // Core
	 // ==== 
	  
	  // create asset bundles, minify them, cache them
	  static function create($what, $wantedmedia=array()) {
	  	// get the current queue to process
	    $queue = self::$queue[$what];
	    
	    // later containing all files to cache, needed to check if something has changed
		  $contents = array();
	    
	    // put all the files together
	    foreach($queue as $media => $formedia) {
	    	// check if user wanted this media
	    	if(!in_array($media, $wantedmedia) && $wantedmedia != array()) {
		    	continue;
	    	}
	    	
	    	// add the media selector to the output
		    if($what == "css" && $media != -1) {
			    $bufferbefore = "@media $media {";
			    $bufferafter = "}";
		    } else {
			    $bufferafter = "";
			    $bufferbefore = "";
		    }
		    
		    // create data array
		    $contents[$media] = array();
		    $contents[$media]["files"] = array();
	    	
	    	// loop through files
		    foreach($formedia as $url => $parser) {
					$contents[$media]["files"][$url] = array("media" => $media, "parser" => $parser);
		    }
		    
		    // set media queries
		    $contents[$media]["bufferbefore"] = $bufferbefore;
		    $contents[$media]["bufferafter"] = $bufferafter;
		  }
		  
		  // loop through them again, but only if something has changed or the file does not exist
	  	if(!file_exists(c::get('root.cache') . '/' . md5(serialize($contents)) . '.' . $what)) {
	  		$buffer = "";
	  		foreach($contents as $media => $dataformedia) {
	  			// echo the media query
	  			$buffer .= $dataformedia["bufferbefore"];
	  			
	  			// parse the files
		    	foreach($dataformedia["files"] as $url => $urlinfo) {
			  	  $buffer .= self::parse($url, $urlinfo["parser"]);
			  	}
			  	
			  	// echo the closing bracket
			  	$buffer .= $contents[$media]["bufferafter"];
			  }
	    } else {
	    	// file exists already, no need to parse all again
		    return '/?asset=' . md5(serialize($contents)) . '.' . $what;
	    }
		  
		  // possible errors
		  if($buffer == "") {
			  return false;
		  }
		  if(c::get('cache') != true) {
			  return $contents;
		  }
		  
		  // compress the buffer
		  $buffer = self::compress($buffer, $what, true);
		  
		  // save it and return the url to the compressed file
		  $result = self::cachefile($buffer, true, $what, $contents);
		  
		  return '/?asset=' . $result;
	  }
	  
	  // parse some LESS/SASS
	  private function parse($url, $parser) {
		  switch($parser) {
			  case 0: // no parser
			  	return file_get_contents($url);
			  case 1: // LESS
			  	$lessc = new lessc();
			  	return $lessc->parse(file_get_contents($url));
			  case 2: // SASS
			  	return -1;
			  case 3: // SCSS
			  	return -1;
			  default:
			  	return false;
		  }
	  }
	  
	  // compress assets
	  private function compress($url, $what, $data=false) {
	  	if($data == false) {
	  		$buffer = file_get_contents($url);
	  	} else {
		  	$buffer = $url;
	  	}
		  if(c::get("assety.$what") == true) {
	      if($what == "css") {
	        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
	        $buffer = preg_replace('/\s\s+/', ' ', $buffer);
	        $buffer = str_replace(array(' { ', ' }', '; ', ', ', ': '), array('{', '}', ';', ',', ':'), $buffer);
	      } else if($what == "js") {
	        $buffer = JSMin::minify($buffer);
	      } else {
	        $buffer = Minify_HTML::minify($buffer, array('cssMinifier' => 'assety::cssminify', 'jsMinifier' => 'assety::jsminify'));
	      }
	      return $buffer;
	    } else {
	      return $buffer;
	    }
	  }
	  
	  // cache a file
	  private function cachefile($url, $data=false, $type=false, $contents=array()) {
	  	if($type == false) {
		  	$type = pathinfo($url, PATHINFO_EXTENSION);
	  	}
	  	
		  if($data == true) {
		  	$name = md5(serialize($contents)) . '.' . $type;
		  	$path = c::get('root.cache') . '/' . $name;
		  	
			  $handle = fopen($path, "w");
			  fwrite($handle, $url);
			  fclose($handle);
		  } else {
		  	$name = sha1($url) . '.' . $type;
		  	$path = c::get('root.cache') . '/' . $name;
		  	@copy($url, $path);
		  }
		  
		  return $name;
	  }
	}

}