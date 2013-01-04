<?php

function tweets($username, $params=array()) {

  $defaults = array(
    'limit'   => 10,
    'cache'   => true,
    'hiderep' => false, //Parameter to hide replies in your feed - for accounts with high reply volumes
    'refresh' => 60*20 // refresh every 20 minutes
  );
  
  // add the username to the defaults array  
  $defaults['username'] = $username;

  $options = array_merge($defaults, $params);

  // check the cache dir 
  $cacheDir = c::get('root.cache') . '/tweets';
  dir::make($cacheDir);

  // disable the cache if adding the cache dir failed  
  if(!is_dir($cacheDir) || !is_writable($cacheDir)) $options['cache'] = false;
    
  // sanitize the limit
  if($options['limit'] > 200) $options['limit'] = 200;
  
  // generate a unique cache ID    
  $cacheID = 'tweets/tweets.' . md5($options['username']) . '.' . $options['limit'] . '.php';
  
  if($options['cache']) {
    $cache = (cache::modified($cacheID) < time()-$options['refresh']) ? false : cache::get($cacheID);  
  } else {
    $cache = false;
  }
  
  if(!empty($cache)) return $cache;

  $url  = 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $options['username'] . '&count=' . $options['limit'] . '&include_rts=true' . '&exclude_replies=' . $options['hiderep'];
  $json = @file_get_contents($url);
  $data = str::parse($json);  

  if(!$data) return false;

  $result = array();
   
  foreach($data as $tweet) {
    
    $user = $tweet['user'];
                                            
    $result[] = new tweet(array(
      'url'    => 'http://twitter.com/' . $options['username'] . '/status/' . $tweet['id_str'],
      'text'   => $tweet['text'],
      'date'   => strtotime($tweet['created_at']),
      'source' => $tweet['source'],
      'user'   => new obj(array(
        'name'      => $user['name'],
        'bio'       => $user['description'], 
        'username'  => $user['screen_name'],
        'url'       => 'http://twitter.com/' . $user['screen_name'],
        'image'     => 'http://twitter.com/api/users/profile_image/' . $user['screen_name'],
        'following' => $user['friends_count'],
        'followers' => $user['followers_count'],
      ))
    ));
        
  }
    
  $result = new obj($result);
  
  if($options['cache']) cache::set($cacheID, $result);
    
  return $result;
  
}

class tweet extends obj {
  
  function date($format=false) {
    return ($format) ? date($format, $this->date) : $this->date;  
  }

  function text($link=false) {
    return ($link) ? self::link(html($this->text)) : $this->text;      
  }

  static function link($text) {
    $text = preg_replace('/(http|https):\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="$1://$2">$1://$2</a>', $text);
    $text = preg_replace('/@([A-Za-z0-9_]+)/is', '<a href="https://twitter.com/#!/$1">@$1</a>', $text);
    $text = preg_replace('/#([A-Aa-z0-9_-]+)/is', '<a href="https://twitter.com/#!/search/%23$1">#$1</a>', $text);
    return $text; 
  }
  
}
