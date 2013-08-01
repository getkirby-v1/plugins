<?php 

// Version: 1.2
// Date: 31.05.2012
// Author: Thibaut Ninove <http://wooconcept.com>, with the help of Bastian Allgeier <http://getkirby.com>
// Support: provided as is, no support provided


// shortcut function to add sublime videos to templates and snippets as well.
function sublime($id, $width=false, $height=false, $uid=false, $name=false, $class=false) {
  $kirbytext = kirbytext::classname();
  $obj  = new $kirbytext();
  return $obj->sublime(array(
    'sublime'  => $id,
    'width'    => $width,
    'height'   => $height,
    'uid'      => ($uid)  ? $uid  : $id,
    'name'     => ($name) ? $name : $id,
    'class'    => $class
  ));
}


// kirbytext extension
class kirbytextExtended extends kirbytext {

  function __construct($text=false, $markdown=true) {
  
    parent::__construct($text, $markdown);
    
    $this->addTags('sublime');
    $this->addAttributes('uid', 'name');

  }

  function sublime($params) {
  
    global $site;
    
    $page   = ($this->obj) ? $this->obj : $site->pages()->active();                
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
      'width'  => 400,
      'height' => 300,
      'uid'    => $id,
      'name'   => $id,
    );
    
    $options = array_merge($defaults, $params);
    $width   = html($options['width']);
    $height  = html($options['height']);
    $uid     = html($options['uid']);
    $name    = html($options['name']);

    if(!$width)  $width  = c::get('kirbytext.video.width');
    if(!$height) $height = c::get('kirbytext.video.height');
    
    // create an additional css class if specified
    if(!empty($class)) $class = ' ' . html($class);
    
    // check for a poster
    $poster = ($poster) ? ' poster="' . $poster->url() . '"' : false;
    
    $html = '<video class="sublime' . $class . '"' . $poster . ' width="' . $width . '" height="' . $height . '" data-uid="' . $uid . '" data-name="' . $name . '" preload="none">'; 
    foreach($videos as $video) {
      // fetch video MIME-type to set it as type in the source
      $mimetype = $video->mime();
      // check for hd quality 
      $hd = ($video->hd()) ? ' data-quality="hd"' : '';
      // generate the source tag for each video
      $html .= '<source src="' . $video->url() . '"' . ' type="' .$mimetype . '"' . $hd . ' />';
    }
    $html .= '</video>';
        
    return $html;
    
  }

}
