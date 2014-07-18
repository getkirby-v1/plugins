<?php

function readlater($content, $tool, $label=false, $params=array()) {
  
  $tools = array(
    'instapaper' => array(
      'link'  => 'http://www.instapaper.com/hello2?url={url}&title={title}',
      'label' => 'Add to Instapaper',
    ),
    'pocket' => array(
      'link'  => 'https://getpocket.com/save?url={url}&title={title}',
      'label' => 'Save to Pocket',
    ),
    'readability' => array(
      'link'  => 'http://www.readability.com/save?url={url}',
      'label' => 'Add to Readability',
    )
  );

  if(!array_key_exists($tool, $tools)) return false;
  
  // make it possible to pass params
  // as third object instead of fourth. 
  if(is_array($label)) {
    $params = $label;
    $label  = false;
  }
  
  $defaults = array(
    'label'  => $label,
    'rel'    => false,
    'target' => false,
    'class'  => 'readlater ' . $tool,
    'url'    => url::current(),
  );

  $options = array_merge($defaults, $params);
  $url     = urlencode($options['url']);
  $title   = urlencode($content);
  $label   = ($options['label'])  ? $options['label'] : $tools[$tool]['label'];
  $rel     = ($options['rel'])    ? ' rel="' . $options['rel'] . '"' : '';
  $target  = ($options['target']) ? ' target="' . $options['target'] . '"' : '';
  $class   = ($options['class'])  ? ' class="' . $options['class'] . '"' : '';

  $link = str_replace('{url}',   $url,   $tools[$tool]['link']);
  $link = str_replace('{title}', $title, $link);

  return '<a' . $class . $rel . $target . ' href="' . $link . '">' . $label . '</a>';

}
