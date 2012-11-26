<?php 

/*
 * Posterous2Kirby Exporter (Beta)
 *  
 * ATTENTION: USE THIS SCRIPT AT YOUR OWN RISK!
 * 
 * It does not delete anything, but depending
 * on the number of articles on your posterous blog
 * it creates quite a lot of folders and text files
 * 
 * MAKE A BACKUP OF YOUR CONTENT FOLDER BEFORE YOU MOVE ON
 * JUST TO MAKE SURE YOU DON'T LOSE ANY DATA
 *
 * Installation:
 *  
 * Put this in your root directory of your site (next to index.php)
 * Change the settings below to enable the connection to your blog's API. 
 * 
 * Open http://yourdomain.com/posterous.php to start the exporter
 *
 * Once you've finished the export, please remove posterous.php again
 * 
 */


// setup the path to your blog folder
// make sure the folder is writable. 
$root = dirname(__FILE__) . '/content/blog';

// Your Posterous blog 
$blog = 'yourblog';

// Your Posterous username (email)
$username = 'your@email-address.com';

// Your Posteours password
$password = 'yourpassword';

// Your Posterous API token
// Login to Posterous and go to https://posterous.com/api/
// Click on "view token" under "Working with Users" > "Users" > "api_token"
$token = 'yourtoken';

// setup the desired name for your content files
$template = 'article.txt';

// setup the date format, which will 
// be used in your content files
// default is 2012-02-09 16:00
$dateformat = 'Y-m-d H:i';













// DON'T TOUCH THE LINES BELOW
// ===========================

// Stuff to run it with kpm
if(isset($kpm)) {
  @extract($kpm['options']);
  $root = $kpm['contentdir'];
  
  function puterror($error) {
    kpmerror($error);
  }
  
  function putmessage($message) {
    kpmlog($message);
  }
} else {
  require('kirby/lib/kirby.php');
  function puterror($error) {
    dir($error);
  }
  
  function putmessage($message) {
    echo $message;
  }
}

set_time_limit(0);

if($username == 'your@email-address.com' && $password == 'yourpassword' && $blog == 'yourblog') {
  puterror('Please setup the credentials for your Posterous blog. <br />Open <strong>posterous.php</strong> in your favorite editor and follow the instructions.');
}

if(!is_dir($root)) puterror('The blog directory does not exist');
if(!is_writable($root)) puterror('The blog directory is not writable');

$posts = array();
$page  = 1;

$p = new posterous($username, $password, $token);

while($data = $p->call('http://posterous.com/api/2/sites/' . $blog . '/posts/public', array('page' => $page))) {
  
  foreach($data as $d) {
    $posts[] = $d;    
  }

  sleep(1);
  
  $page++;
  
}

if(!$posts) puterror('The posts couldn\'t be found');

$cnt = count($posts);
$len = str::length($cnt);

if($len <= 1) $len = 2;

$n = 0;
$skipped = array();
$errors = array();

foreach(array_reverse($posts) as $post) {
    
  $n++;
  $output = array();
  
  if(empty($post->title) || empty($post->slug)) {
    $errors[] = $post;
    continue;
  }
  
  // collect tags
  $tags = array();
  foreach($post->tags as $t) $tags[] = $t->name;


  $output[] = 'title: ' . $post->title;  
  $output[] = 'date: ' . date($dateformat, strtotime($post->display_date));
  $output[] = 'text: ' . "\n\n" . trim($post->body_full);
  $output[] = 'tags: ' . implode(', ', $tags);

  $name = pad($n, $len) . '-' . f::safe_name(basename($post->slug));
  $dir  = $root . '/' . $name;
    
  if(is_dir($dir)) {
    $skipped[] = basename($dir);
    continue;  
  }

  dir::make($dir);
  
  $content = implode("\n\n" . '----' . "\n\n", $output);
  $file    = $dir . '/' . $template;
  
  f::write($file, $content);
    
}

putmessage('Exported ' . $n . ' articles to ' . $root . '<br /><br />');

if(!empty($errors)) {
  putmessage(count($errors) . ' article(s) could not be imported<br /><br />');
}

if(!empty($skipped)) {
  putmessage('The following folders have been skipped, because they already existed:' . a::show($skipped, false));
}

// padding zero function 
function pad($number,$n) {
  return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
}

// Minimal Posterous API Class
class posterous {

  function __construct($username, $password, $token) {
    
    $this->username = $username;
    $this->password = $password;
    $this->token    = $token;
        
  }

  function call($url, $args = array()) {
    
    $args['api_token'] = $this->token;
        
    $url = $url . '?' . http_build_query($args);
            
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
    curl_setopt($ch, CURLOPT_POST, 0);
    
    $data = curl_exec($ch);
    curl_close($ch);
    
    return @json_decode($data);
        
  }

}
