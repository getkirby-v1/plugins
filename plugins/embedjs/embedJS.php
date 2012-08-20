<?php 

class kirbytextExtended extends kirbytext {
  
  function __construct($text, $markdown=true) {
    parent::__construct($text, $markdown);
    $this->addTags('javascript');
  }  

  function javascript($params) {
      
    $file = $params['javascript'];
    if(startsWith($file, 'http://') || starsWith($file, 'https://') || startsWith($file, '/')) {
      $url = $file;
    }
    else {
      $url = 'assets/javascript/'.$file;
    }
    return '<script type="text/javascript" src="' . $url . '"></script>';
  }
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

?>