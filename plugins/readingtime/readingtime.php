<?php

/* 
 * Reading Time
 * 
 * A simple plugin that estimates the reading 
 * time for any text
 *
 * Sample Usage: 
 * 
 * <?php echo readingtime($page->text()) ?>
 * 
 * Author: Roy Lodder <http://roylodder.com>
 * Contributor: Bastian Allgeier <http://getkirby.com>
 *
 */

function readingtime($content, $params=array()) {
    
  $defaults = array(
    'minute'  => 'minute',
    'minutes' => 'minutes',
    'second'  => 'second',
    'seconds' => 'seconds',
    'format'  => '{minutesCount} {minutesLabel}, {secondsCount} {secondsLabel}'
  );
  
  $options = array_merge($defaults, $params);
  $words   = str_word_count(strip_tags($content));

  $minutesCount = floor($words / 200);
  $secondsCount = floor($words % 200 / (200 / 60));
  $minutesLabel = ($minutesCount <= 1) ? $options['minute'] : $options['minutes'];
  $secondsLabel = ($secondsCount <= 1) ? $options['second'] : $options['seconds'];

  $replace  = array(
    'minutesCount' => $minutesCount,
    'minutesLabel' => $minutesLabel,
    'secondsCount' => $secondsCount, 
    'secondsLabel' => $secondsLabel, 
  );

  $result = $options['format'];

  foreach($replace as $key => $value) {
    $result = str_replace('{' . $key . '}', $value, $result);
  }

  return $result;

}
