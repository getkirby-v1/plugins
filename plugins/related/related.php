<?php

/* 
 * Related Pages
 * 
 * A plugin to build relation between pages
 *
 * Sample Usage: 
 * 
 * In your content file: 
 *
 * Title: My title
 * ----
 * Text: My text
 * ----
 * Related: 
 * - projects/project-1
 * - blog/my-article
 * 
 * In your template: 
 *  
 * <?php foreach related($page->related() as $related): ?>
 * <a href="<?php echo $related->url() ?>"><?php echo html($related->title()) ?></a>
 * <?php endforeach ?>
 * 
 * Author: Bastian Allgeier <http://getkirby.com>
 *
 */

function related($field) {

  global $site;
          
  // parse the field with yaml
  $raw     = yaml($field);
  $related = array();
  $pages   = $site->pages();
  
  foreach($raw as $r) {
    // make sure to only add found related pages
    if($rel = $pages->find($r)) $related[] = $rel;
  }    
  
  return new relatedPages($related);  
  
}

// this is only needed to build a proper find method 
// the pages find method will be broken with pages from 
// various subfolders
class relatedPages extends pages {
  
  function find() {

    global $site;

    $args = func_get_args();
    
    // find multiple pages
    if(count($args) > 1) {
      $result = array();
      foreach($args as $arg) {
        $page = $this->find($arg);
        if($page) $result[] = $page;
      }      
      return (empty($result)) ? false : new relatedPages($result);
    }    
                                
    return $site->pages()->find(a::first($args));
      
  }
  
}
