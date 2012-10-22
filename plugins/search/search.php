<?php 

class search {

  // array with fields to search in 
  var $fields;

  // array with field names as keys and score values, 
  // which will be mulitplied with the hits per field to 
  // get a more valid result
  var $score;

  // search mode ('and' or 'or')
  var $mode;

  // search for entire words or tokens
  var $words;

  // uris to ignore
  var $ignore;

  // pages to include (all, visible, invisible)
  var $include;

  // a uri to search within
  var $in;
  
  // a minlength for searchwords
  var $minlength;
  
  // an array of stopwords
  var $stopwords;
  
  // the unexploded searchquery 
  var $query;
  
  // the fieldname in the searchform 
  var $searchfield;

  // an array of exploded and cleaned searchwords
  var $searchwords = array();
  
  // a limit of rows to paginate
  var $paginate;
  
  // the set of results
  var $results = false;

  function __construct($options=array()) {
  
    global $site;
  
    $this->fields      = a::get($options, 'fields', array());
    $this->score       = a::get($options, 'score', array());
    $this->words       = a::get($options, 'words');
    $this->ignore      = a::get($options, 'ignore', array());
    $this->include     = a::get($options, 'include', 'all');
    $this->in          = a::get($options, 'in');
    $this->minlength   = a::get($options, 'minlength', false);
    $this->stopwords   = a::get($options, 'stopwords', array());
    $this->query       = a::get($options, 'query', false);
    $this->searchfield = a::get($options, 'searchfield', false);
    $this->paginate    = a::get($options, 'paginate', false);
    $this->mode        = str::lower(a::get($options, 'mode')) == 'and' ? 'and' : 'or';
      
    $result = array();
  
    // if you set a searchfield instead of a query
    // the query will automatically be fetched from 
    // post or get requests
    if($this->searchfield) {
      $this->query = trim(urldecode(get($this->searchfield)));
    }
      
    // stop here if no searchword is found  
    if(empty($this->query)) return false;
        
    $this->searchwords = preg_replace('/[^\pL]/u',',', preg_quote($this->query));
    $this->searchwords = str::split($this->searchwords, ',', $this->minlength);
    
    if(!empty($this->stopwords)) {
      $this->searchwords = array_diff($this->searchwords, $this->stopwords);
    }
      
    if(empty($this->searchwords)) return false;
  
    // do this to save the count function for all loops
    $countSearchwords = count($this->searchwords);
    
    // define the set of pages to search in
    $pages = ($this->in) ? $site->pages()->find($this->in)->children()->index() : $site->pages()->index();                        
    
    foreach($pages as $page) {

      if($this->include == 'visible' && !$page->isVisible()) continue;
      if($this->include == 'invisible' && $page->isVisible()) continue;
          
      if(in_array($page->uri(), $this->ignore)) continue;
      
      if(!empty($this->fields)) {    
        $keys = array_intersect(array_keys($page->content->variables), $this->fields);
      } else if($page->content) {
        $keys = array_keys($page->content->variables);
      }
          
      $found = array();
      $matchedTotal = 0;
      $score = 0;
      
      foreach($keys as $field) {
        $value = $page->$field;
        
        $matchedPerField = 0;
        $matchedWords    = 0;
        $fieldScore      = a::get($this->score, $field, 1);
                
        foreach($this->searchwords as $s) {
          
          // only match words
          if($this->words) { 
            $m = @preg_match_all('!\b' . $s . '\b!i', $value, $array);
          } else {
            $m = @preg_match_all('!' . $s . '!i', $value, $array);
          }
          
          // track matched search words        
          if($m) $matchedWords++;
  
          // add the matches to the page      
          $matchedPerField = $matchedPerField+$m;
          
        }  
                
        if(
          $this->mode == 'and' && $countSearchwords == $matchedWords && $matchedPerField > 0 || 
          $this->mode == 'or'  && $matchedPerField > 0
        ) {
          // add the number of hits;
          $matchedTotal = $matchedTotal+$matchedPerField;
          
          // apply the score for this field
          $score = $score+($matchedPerField*$fieldScore);
        } 
        
      }
      
      // add all matched pages to the result set  
      if($matchedTotal) {            
        $result[$page->uid] = $page;      
        $result[$page->uid]->searchHits  = $matchedTotal;
        $result[$page->uid]->searchScore = $score;
      }
                
    }  
  
    if(empty($result)) return false;
              
    $pages = new pages($result);
    $pages = $pages->sortBy('searchScore','desc');
  
    // add pagination
    if($this->paginate) $pages = $pages->paginate($this->paginate, array('mode' => 'query'));
    
    $this->results = $pages;
  
  }
  
  function results() {
    return $this->results;
  }
  
  function query() {
    return $this->query; 
  }
  
}