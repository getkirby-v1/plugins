<?php

if(c::get('version.number') >= 1.11) {
	
	class replacer {
	  static $activated = false;
	  static $realplaceholders;
	  static $set = false;
	  
	  static function kirbytextfnct($text, $second, $third) {
	  	if(is_array($second)) {
		  	return kirbytext::init($text, true, true, $second);
	  	} else if($third != false) {
		  	return kirbytext::init($text, $second, true, $third);
	  	} else {
		  	return kirbytext::init($text, $second);
	  	}
	  }
	  
	  static function load() {
	    global $placeholders;
	    c::set('root.replacer',   c::get('root.site') . '/replacer');
	    $rootr = c::get('root.replacer');
	    if(!file_exists($rootr)) {
	      @mkdir($rootr);
	    }
	    if(!file_exists($rootr . '/replacer.php')) {
	      if($handler = @fopen($rootr . '/replacer.php', 'w')) {
		      fwrite($handler, "<?php if(!defined('KIRBY')) exit ?>\n\n# Replacer vars\n");
		      fclose($handler);
	      }
	    }
	    ob_start();
	    @include_once($rootr . '/replacer.php');
	    $content = preg_replace('{(.)(( )+|	)usage: (.*?)}', '$1' . "\n" . '$2usage: $5', ob_get_contents());
	    ob_end_clean();
	    $placeholders_general = yaml($content);
	    if(file_exists($rootr . '/replacer.' . server::get('server_name') . '.php')) {
	      ob_start();
	      require($rootr . '/replacer.' . server::get('server_name') . '.php');
	      $content = preg_replace('{(.)(( )+|(	)+)usage: (.*?)}', '$1' . "\n" . '	usage: $5', ob_get_contents());
	      ob_end_clean();
	      $placeholders_site = yaml($content);
	    } else {
	      $placeholders_site = array();
	    }
	    $placeholders = array_merge($placeholders_general, $placeholders_site);
	    foreach($placeholders as $placeholder => $options) {
		    if(isset($options["templates"]) && is_array($options["templates"])) {
		      $placeholders[$placeholder]["templates"] = array_flip($placeholders[$placeholder]["templates"]);
		    } else if(isset($options["templates"])) {
		      $exploded1 = explode(", ", $options["templates"]);
		      $exploded = array();
		      foreach($exploded1 as $element) {
		      	foreach(explode(",", $element) as $elementnow) {
			     	 $exploded[$elementnow] = 0;
			     	}
		      }
			    $placeholders[$placeholder]["templates"] = $exploded;
		    }
		    if(isset($options["sets"]) && is_array($options["sets"])) {
		      $placeholders[$placeholder]["sets"] = array_flip($placeholders[$placeholder]["sets"]);
		    } else if(isset($options["sets"])) {
		      $exploded1 = explode(", ", $options["sets"]);
		      $exploded = array();
		      foreach($exploded1 as $element) {
		      	foreach(explode(",", $element) as $elementnow) {
			     	 $exploded[$elementnow] = 0;
			     	}
		      }
			    $placeholders[$placeholder]["sets"] = $exploded;
		    }
		  }
	  }
	  
	  static function apply_global_placeholders($output, $set = false) {
	    global $placeholders, $site;
	    foreach($placeholders as $pname => $poptions) {
	      if(isset($poptions["usage"]) && $poptions["usage"] == "global" && (!isset($poptions["templates"]) || isset($poptions["templates"][$site->pages()->active()->template()])) && ($set == -1 || ($set == false && !isset($poptions["sets"])) || isset($poptions["sets"][$set]))) {
	        $output = self::replace($pname, $poptions["with"], $output);
	      }
	    }
	    return $output;
	  }
	  
	  static function apply_kirbytext_placeholders($text, $placeholdersg) {
		  global $placeholders, $currenttemplate;
	    
	    foreach($placeholdersg as $pthis => $pthat) {
	      if(is_numeric($pthis)) {
	        if(isset($placeholders[$pthat]) && isset($placeholders[$pthat]["usage"]) && $placeholders[$pthat]["usage"] == "demand" && (!isset($poptions["templates"]) || isset($poptions["templates"][$currenttemplate["existing"]]) || isset($poptions["templates"][$currenttemplate["virtual"]]))) {
	          $text = self::replace($pthat, $placeholders[$pthat]["with"], $text);
	        } else {
	          foreach($placeholders as $pname => $poptions) {
	            if(isset($poptions["usage"]) && $poptions["usage"] == "demand" && isset($poptions["alias"]) && $poptions["alias"] == $pthat && (!isset($poptions["templates"]) || isset($poptions["templates"][$currenttemplate["existing"]]) || isset($poptions["templates"][$currenttemplate["virtual"]]))) {
	              $text = self::replace($pname, $poptions["with"], $text);
	              break;
	            }
	          }
	        }
	      } else {
	        $text = self::replace($pthis, $pthat, $text);
	      }
	    }
	    
	    foreach($placeholders as $pname => $poptions) {
	      if(isset($poptions["usage"]) && $poptions["usage"] == "kirbytext" && (!isset($poptions["templates"]) || isset($poptions["templates"][$currenttemplate["existing"]]) || isset($poptions["templates"][$currenttemplate["virtual"]]))) {
	        $text = self::replace($pname, $poptions["with"], $text);
	      }
	    }
	    return $text;
	  }
	  
	  static function activate($seta = false) {
	    global $activated, $set;
	    if($seta != false) {
		    $set = $seta;
	    } else {
		    $set = false;
	    }
	    if($activated == false) {
		    ob_start();
		    $activated = true;
		    return true;
		  }
		  return false;
	  }
	  
	  static function on($set = false) {
		  return self::activate($set);
	  }
	  
	  static function apply($seta = false) {
	    global $activated, $set;
	    $givenplaceholders = false;
	    if($seta == false && $set == false) {
		    $setu = false;
	    } else if(is_array($seta) || is_array($set)) {
	      if($seta == false) {
		      $placeholders = $set;
	      } else if(is_array($seta)) {
		      $placeholders = $seta;
	      } else {
		      $placeholders = array();
	      }
	      $subject = ob_get_clean();
	      $replaced = $subject;
	      foreach($placeholders as $name => $with) {
		      $replaced = self::replace($name, $with, $replaced);
	      }
	      echo $replaced;
	      return true;
	    } else if($set != false) {
		    $setu = $set;
		    $givenplaceholders = true;
	    } else if($seta != false) {
	      $setu = $seta;
	      $givenplaceholders = true;
	    } else {
		    $setu = false;
	    }
	    $set = false;
	    if((c::get("replacer.autouse") == false || $givenplaceholders == true) && $activated == true) {
		    echo self::apply_global_placeholders(ob_get_clean(), $setu);
		    $activated = false;
		    return true;
		  } else if($activated == true) {
			  echo ob_get_clean();
		  }
		  return false;
	  }
	  
	  static function off($set = false) {
		  return self::apply($set);
	  }
	  
	  static function replace($this, $that, $what) {
		  if(c::get('replacer.regex') == 'string') {
			  return str_replace($this, $that, $what);
		  } else if(c::get('replacer.regex') == 'regex') {
			  return preg_replace($this, $that, $what);
		  } else {
			  ini_set('track_errors', 'on');
	      $php_errormsg = '';
	      @preg_match($this, '');
	      ini_set('track_errors', 'off');
	      if($php_errormsg) {
	        return str_replace($this, $that, $what);
	      } else {
		      return preg_replace($this, $that, $what);
	      }
		  }
	  }
	}
	
	replacer::load();

}

?>