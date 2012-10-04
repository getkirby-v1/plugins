<?php

/**
 * Returns an Instagram object.
 * @param string    The access token (see readme on how to obtain one) ALWAYS NEEDED!
 * @param integer   The number of photos that should be fetched.
 * @param boolean   Caching enabled.
 * @param integer   How many seconds until the cache expires.
 * @param string    The user-id of the user of whom the photos should be loaded. 'self' is the default, which means that your own user ID will be inserted automatically by Instagram.
 * @return object   Object of class Instagram, that holds all the images.
*/                    
function instagram($token = '', $count = 10, $cache = true, $cache_expire = 3600, $user = 'self') {
    return new instagram($token, $count, $cache, $cache_expire, $user);
}

/**
 * An Instagram <http://instagram.com/> plugin for Kirby <http://getkirby.com/>.
 * In order to use this plugin, you'll need to obtain an access token for API access. See the readme for more information.
 * @author Simon Albrecht <http://albrecht.me/>
 * @version 1.0
 * @copyright (c) 2012 Simon Albrecht
 */
class instagram { 
    var $images;
    var $user;

    /**
     * Constructor. Loads the data from the Instagram API.
     * @param string    The access token.
     * @param integer   The number of shots that will be loaded.
     * @param boolean   Chache enabled.
     * @param integer   How many seconds until the cache expires.
     * @param string    The user-id of the user or 'self' for your own account.
     */
    function __construct($_token = '', $_count = 10, $_cache = true, $_cache_expire = 3600, $_user = 'self') {
        // Init
        $this->images   = array();
        $this->user     = new stdClass;
    
        // Check if a token is provided
        if (trim($_token) != '') {
            
            // Construct the API url…
            // http://instagr.am/developer/endpoints/users/
            $url = "https://api.instagram.com/v1/users/{$_user}/media/recent/?access_token={$_token}&count={$_count}";
            
            // Create cache directory if it doesn't exist yet
            if ($_cache) {
                dir::make(c::get('root.cache') . '/instagram');
            }
            
            $images_cache_id    = 'instagram/images.' . md5($_token) . '.' . $_count . '.php';
            $images_cache_data  = false;
            
            // Try to fetch data from cache
            if ($_cache) {
                $images_cache_data = (cache::modified($images_cache_id) < time() - $_cache_expire) ? false : cache::get($images_cache_id);
            }
            
            // Load data from the API if the cache expired or the cache is empty
            if (empty($images_cache_data)) {
                $data   = $this->fetch_data($url);
                $photos = json_decode($data);
                
                // Set new data for the cache
                if ($_cache) {
                    cache::set($images_cache_id, $photos);
                }
            } else {
                $photos = $images_cache_data;
            }
            
            // Process the images
            for ($i = 0; $i < $_count; $i++) {
                if (isset($photos->data[$i]) && count($photos->data) > 0) {
                    
                    // Get the user's data from the first image
                    if ($i == 0) {
                       $this->user->username    = $photos->data[$i]->user->username; 
                       $this->user->full_name   = $photos->data[$i]->user->full_name;
                       $this->user->picture     = $photos->data[$i]->user->profile_picture;
                    }
                    
                    // create a new object for each image                    
                    $obj = new stdClass;
                    
                    $obj->link         =  $photos->data[$i]->link;
                    $obj->comments     = @$photos->data[$i]->comments->count;
                    $obj->likes        = @$photos->data[$i]->likes->count;
                    $obj->created      =  $photos->data[$i]->created_time;
                    $obj->thumb        = @$photos->data[$i]->images->thumbnail->url;
                    $obj->url          = @$photos->data[$i]->images->standard_resolution->url;
                    $obj->image_lowres = @$photos->data[$i]->images->low_resolution->url;
                    $obj->filter       =  $photos->data[$i]->filter;
                    $obj->location     = @$photos->data[$i]->location->name;
                    $obj->latitude     = @$photos->data[$i]->location->latitude;
                    $obj->longitude    = @$photos->data[$i]->location->longitude;
                    $obj->tags         = array();
                    
                    // attach the new object to the array                    
                    $this->images[$i] = $obj;
                                        
                    // Process tags
                    for ($j = 0; $j < count($photos->data[$i]->tags); $j++) {
                        $this->images[$i]->tags[$j] = $photos->data[$i]->tags[$j];
                    }               
                }
            }
        } else {
            throw new Exception('$_token MUST be set!');
        }
    }
    
    /**
     * Returns the images that were loaded from the API.
     * @return array    Array of objects containing all the photo's data.
     */
    function images() {
        return $this->images;
    }      
    
    /**
     * Returns information about the user.
     * @return object   Object with information about the user.
     */
    function user() {
        return $this->user;
    }
    
     /**
     * Fetches data from an url.
     * @param string    The url from where data should be fetched.
     * @return object   The data loaded from the url
     */
    protected function fetch_data($url = null) {
        if (!is_null($url)) {
            
            // Init CURL
            $handler = curl_init();        
    
            // CURL options
            curl_setopt($handler, CURLOPT_URL, $url);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);

            // Load data & close connection
            $data = curl_exec($handler);
            curl_close($handler);  
        
            return $data;
        }
    }
}