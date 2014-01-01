<?php
/*
 * With that plugin you can implement your GitHub information or list your repos easily in Kirby.
 * Copyright (c) 2012 by Arne Bahlo
 *
 */
class github {
    // Constructor
    function __construct($username = null, $count = 0) {
        if(is_null($username)) return false;
        $this->username = $username;
        $this->count = $count;
    }

    // Get repos and sort them
    public function repos($sorted = false) {
        if($this->count <= 0) return false;
        $url = 'https://api.github.com/users/' . $this->username . '/repos';
        $data = $this->get_data($url);

        $this->repos = array();

        foreach ($data as $key => $obj) {
            if($key >= $this->count) break;

            $this->repos[$key] = new stdClass;

            $this->repos[$key]->name = $obj->name;
            $this->repos[$key]->description = $obj->description;
            $this->repos[$key]->url = $obj->html_url;
            $this->repos[$key]->last_update = strtotime($obj->updated_at);
            $this->repos[$key]->forkount = $obj->forks_count;
            $this->repos[$key]->watchers = $obj->watchers_count;
        }

        if($sorted) {
            function cmp($a, $b) {
                if ($b->last_update == $a->last_update) return 0;
                return ($b->last_update < $a->last_update) ? -1 : 1;
            }
            usort($this->repos, 'cmp');
        }

        return $this->repos;
    }

    // Get user
    public function user() {
        $url = 'https://api.github.com/users/' . $this->username;
        $data = $this->get_data($url);

        $this->user = new stdClass;

        $this->user->username = $data->login;
        $this->user->name = $data->name;
        $this->user->email = $data->email;
        $this->user->followers = $data->followers;
        $this->user->following = $data->following;
        $this->user->url = 'https://github.com/' . $data->login;
        $this->user->gravatar_id = $data->gravatar_id;
        $this->user->repos_url = $data->repos_url;

        return $this->user;
    }

    // Fetch data
    protected function get_data($url) {
        if($url == '') return false;
        $handler = curl_init();

        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($handler, CURLOPT_USERAGENT, $this->username);

        $data = curl_exec($handler);
        curl_close($handler);

        return json_decode($data);
    }
}
?>
