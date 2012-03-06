<?php 

// Version: 1.1
// Date: 06.03.2012
// Author: Thibaut Ninove <http://wooconcept.com>, with the help of Bastian Allgeier <http://getkirby.com>
// Support: provided as is, no support provided

class kirbytextExtended extends kirbytext {

  function __construct($text, $markdown=true) {
  
    parent::__construct($text, $markdown);
    
    $this->addTags('sublime');
    $this->addAttributes('uid', 'name');

  }

  function sublime($params) {
  
    global $site;
    
    $page   = $site->pages()->active();                
    $id     = @$params['sublime'];
    $class  = @$params['class'];
    $videos = array();
    $poster = false;
    
    // gather all video files which match the given id/name    
    foreach($page->videos() as $v) {

      if(preg_match('!^' . preg_quote($id) . '!i', $v->name())) {
        
        $extension = f::extension($v->name());
        $mobile    = ($extension == 'mobile') ? $v->mobile = true : $v->mobile = false;
        $hd        = ($extension == 'hd')     ? $v->hd     = true : $v->hd     = false;
                      
        $videos[] = $v;

      }

    }
    
    if(empty($videos)) return false;    

    // find the poster for this video
    foreach($page->images() as $i) {
      if(preg_match('!^' . preg_quote($id) . '!i', $i->name())) {
        $poster = $i;
        break;
      }
    }
        
    $defaults = array(
      'uid'    => $id,
      'name'   => $id,
      'width'  => c::get('kirbytext.video.width'),
      'height' => c::get('kirbytext.video.height')
    );
    
    $options = array_merge($defaults, $params);
    $width   = html($options['width']);
    $height  = html($options['height']);
    $uid     = html($options['uid']);
    $name    = html($options['name']);
    
    // create an additional css class if specified
    if(!empty($class)) $class = ' ' . html($class);
    
    $html = '<video class="sublime' . $class . '" poster="' . $poster->url() . '" width="' . $width . '" height="' . $height . '" data-uid="' . $uid . '" data-name="' . $name . '" preload="none">'; 
    foreach($videos as $video) {
      // check for hd quality 
      $hd = ($video->hd()) ? ' data-quality="hd"' : '';
      // generate the source tag for each video
      $html .= '<source src="' . $video->url() . '"' . $hd . '>';
    }
    $html .= '</video>';
        
    return $html;
    
  }

}

?>