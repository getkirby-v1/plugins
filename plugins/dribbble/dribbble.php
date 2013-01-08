<?php 

/**
 * Returns a Dribbble object.
 * @param string    $username The username of the player.
 * @param integer   $number_of_shots The number of shots that will be loaded.
 * @param boolean   $fetch_likes If likes should be fetched.
 * @param integer   $number_of_likes How many likes should be fetched?
 * @param boolean   $cache Enable/disable caching. cache is enabled by default
 * @param integer   $refresh Seconds before the Cache will be refreshed. Default is in hour (3600 seconds)
 * @return object   Object of class "dribbble"
 */
function dribbble($username = "s_albrecht", $number_of_shots = 3, $fetch_likes = false, $number_of_likes = 3, $cache = true, $refresh = 3600) {
    return new dribbble($username, $number_of_shots, $fetch_likes, $number_of_likes, $cache, $refresh);
}

/**
 * A Dribbble <http://dribbble.com/> plugin for Kirby <http://getkirby.com/>, that loads the shots of a player.
 * @author Simon Albrecht <hello@albrecht.me>, caching added by Bastian Allgeier <bastian@getkirby.com>
 * @version 1.2
 * @copyright (c) 2012 Simon Albrecht
*/
class dribbble {

    // Variables
    var $username;
    var $shots;
    var $likes;
    var $player;

    /**
     * Constructor. Loads the data from Dribbble when the object is constructed.
     * @param string    $_username The username of the player.
     * @param integer   $_number_of_shots The number of shots that will be loaded.
     * @param boolean   $_fetch_likes If the likes of the user should be fetched in a second call.
     * @param integer   If <code>$_fetch_likes</code> is <code>true</code>, then how many likes should be fetched.
     * @param boolean   $cache Enable/disable caching. Cache is enabled by default
     * @param integer   $refresh Seconds before the cache will be refreshed. Default is in hour (3600 seconds)
     */
    function __construct($_username = "s_albrecht", $_number_of_shots = 3, $_fetch_likes = false, $_number_of_likes = 3, $cache = true, $refresh = 3600) {
        // Init
        $this->username = $_username;
        $this->shots    = array();
        $this->likes    = array();
        $this->player   = null;
    
        // Build URLs    
        $base_url   = "http://api.dribbble.com/players/" . $this->username;
        $shots_url  = $base_url . "/shots";
        $likes_url  = $base_url . "/likes";
          
        // create the cache directory if not there yet
        if($cache) dir::make(c::get('root.cache') . '/dribbble');   
          
        // Process the data
        if ($_number_of_shots > 0) {

            // define a cache id
            $shots_cache_id   = 'dribbble/shots.' . md5($this->username) . '.' . $_number_of_shots . '.php';
            $shots_cache_data = false;
            
            // try to fetch the data from cache
            if($cache) {
                $shots_cache_data = (cache::modified($shots_cache_id) < time()-$refresh) ? false : cache::get($shots_cache_id);  
            }
            
            // if there's no data in the cache, load shots from the Dribbble API            
            if(empty($shots_cache_data)) {
                $all_shots = $this->fetch_data($shots_url);
                $all_shots = json_decode($all_shots);
                $all_shots = $all_shots->shots;
                
                if($cache) cache::set($shots_cache_id, $all_shots);
              
            } else {
                $all_shots = $shots_cache_data; 
            }
            
            // Only proceed if there is at least one shot.
            // If there's no shot, then player data can't be extracted from this API call
            // and must be extracted via /players/:id/ (maybe I'll implement that later)
            if (count($all_shots) > 0) {   
            
                // Load shots data 
                for ($i = 0; $i < $_number_of_shots; $i++) {
                    if (!is_null($all_shots[$i])) {
                        $this->shots[$i]->id        = $all_shots[$i]->id;
                        $this->shots[$i]->title     = $all_shots[$i]->title;
                        $this->shots[$i]->url       = $all_shots[$i]->url;
                        $this->shots[$i]->short_url = $all_shots[$i]->short_url;
                        $this->shots[$i]->image     = $all_shots[$i]->image_url;
                        $this->shots[$i]->likes     = $all_shots[$i]->likes_count;
                        $this->shots[$i]->views     = $all_shots[$i]->views_count;
                        $this->shots[$i]->rebounds  = $all_shots[$i]->rebounds_count;
                        $this->shots[$i]->comments  = $all_shots[$i]->comments_count;
                        $this->shots[$i]->created   = $all_shots[$i]->created_at;
                    }
                }
                
                // Process player data
                $this->player->id           = $all_shots[0]->player->id;
                $this->player->name         = $all_shots[0]->player->name;
                $this->player->username     = $all_shots[0]->player->username;
                $this->player->url          = $all_shots[0]->player->url;
                $this->player->avatar_url   = $all_shots[0]->player->avatar_url;
                $this->player->twitter      = $all_shots[0]->player->twitter_screen_name;
                $this->player->location     = $all_shots[0]->player->location;
                $this->player->followers    = $all_shots[0]->player->followers_count;
                $this->player->following    = $all_shots[0]->player->following_count;
                $this->player->likes        = $all_shots[0]->player->likes_count;                
            }
        }
            
        // Fetch all likes of the user (needs another API call).
        // If you only want to fetch the likes, not the shots, then set <code>$_number_of_shots</code> to <code>0</code>.
        if ($_fetch_likes && $_number_of_likes > 0) {

            // define a cache id
            $likes_cache_id   = 'dribbble/likes.' . md5($this->username) . '.' . $_number_of_likes . '.php';
            $likes_cache_data = false;
            
            // try to fetch the data from cache
            if($cache) {
                $likes_cache_data = (cache::modified($likes_cache_id) < time()-$refresh) ? false : cache::get($likes_cache_id);  
            }

            // if there's no data in the cache, load likes from the Dribbble API                                    
            if(empty($likes_cache_data)) {
                $all_likes = $this->fetch_data($likes_url);
                $all_likes = json_decode($all_likes);
                $all_likes = $all_likes->shots;

                if($cache) cache::set($likes_cache_id, $all_likes);

            } else {
                $all_likes = $likes_cache_data;
            }

        
            // Process likes
            for ($i = 0; $i < $_number_of_likes; $i++) {
                if (!is_null($all_likes[$i])) {
                    $this->likes[$i]->id        = $all_likes[$i]->id;
                    $this->likes[$i]->title     = $all_likes[$i]->title;
                    $this->likes[$i]->url       = $all_likes[$i]->url;
                    $this->likes[$i]->short_url = $all_likes[$i]->short_url;
                    $this->likes[$i]->image     = $all_likes[$i]->image_url;
                    $this->likes[$i]->likes     = $all_likes[$i]->likes_count;
                    $this->likes[$i]->views     = $all_likes[$i]->views_count;
                    $this->likes[$i]->rebounds  = $all_likes[$i]->rebounds_count;
                    $this->likes[$i]->comments  = $all_likes[$i]->comments_count;
                    $this->likes[$i]->created   = $all_likes[$i]->created_at;
                    
                    // Process the user the like belongs to
                    $this->likes[$i]->player->id            = $all_likes[$i]->player->id;
                    $this->likes[$i]->player->name          = $all_likes[$i]->player->name;
                    $this->likes[$i]->player->username      = $all_likes[$i]->player->username;
                    $this->likes[$i]->player->url           = $all_likes[$i]->player->url;
                    $this->likes[$i]->player->avatar_url    = $all_likes[$i]->player->avatar_url;
                    $this->likes[$i]->player->twitter       = $all_likes[$i]->player->twitter_screen_name;
                    $this->likes[$i]->player->location      = $all_likes[$i]->player->location;
                    $this->likes[$i]->player->followers     = $all_likes[$i]->player->followers_count;
                    $this->likes[$i]->player->following     = $all_likes[$i]->player->following_count;
                    $this->likes[$i]->player->likes         = $all_likes[$i]->player->likes_count;   
                }
            }
        }
    }

    /**
     * Returns the shots of the player.
     * @return array    Array of objects that contain the shots data.
     */
    function shots() {
        return $this->shots;
    }
    
    /**
     * Returns the likes of the player.
     * @return array    Array of objects that contain the likes data.
     */
    function likes() {
        return $this->likes;
    }
    
    /**
     * Returns an object containing all the data of the player.
     * @return object   Object containing all the player's data.
     */
    function player() {
        return $this->player;
    }
    
    /**
     * Returns the username.
     * @return string   The username used to construct this class.
     */
    function username() {
        return $this->username;
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
