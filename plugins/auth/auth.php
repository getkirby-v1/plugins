<?php

s::start();

class Auth {

	static protected $user = null;

	static public function login($redirect = '/') {

		if(self::user()) go(url($redirect));

 		self::kill();

		$password = get('password');
		$username = get('username');
    
    if(empty($username) || empty($password)) return false;

    // try to find the user
    $account = self::load($username);
    
    if(!$account) return array(
			'status' => 'error',
			'msg'    => l::get('auth.error', 'Invalid username or password')
		);    
    
    // check for matching usernames
    if(str::lower($account->username()) != str::lower($username)) return array(
			'status' => 'error',
			'msg'    => l::get('auth.error', 'Invalid username or password')
		);    
    
    // check for a matching password
    if(!self::checkPassword($account, $password)) return array(
			'status' => 'error',
			'msg'    => l::get('auth.error', 'Invalid username or password')
		);

    // generate a random token
    $token = str::random();
        
    // add the username. 
    $account->token = $token;
    
    // store the token in the cookie
    // and the user data in the session    
    cookie::set('authFrontend', $token, 60*60*24, '/');  		
		s::set('authFrontend.' . $token, $account->username());
    		
		go(url($redirect));

	}

  static function user() {

  	if(!is_null(self::$user)) return self::$user;

    $token = cookie::get('authFrontend');
    
    if(empty($token)) return self::$user = false;

		$username = s::get('authFrontend.' . $token, false);

    if(empty($username)) return self::$user = false;

    $account = self::load($username);

    // make sure to remove the password
    // because this should never be visible to anybody
    unset($account->_['password']);

    if(empty($account) || $account->username() != $username) return self::$user = false;
            
    $account->token = $token;
    return self::$user = $account;

	}

	static protected function kill() {

		self::$user = null;

    // overwrite the token      
    $token = str::random();
    // the cookie is valid for 24 hours
    cookie::set('authFrontend', $token, 60*60*24, '/');
    
    // restart the session    
    s::restart();

	}

	static public function logout($redirect = 'login') {

		self::kill();

    // go to the homepage
    go(url($redirect));

	}

	static public function firewall($params = array()) {

		global $site;

		$defaults = array(
			'ignore'   => array('login', 'logout'),
			'redirect' => 'login',
			'allow'    => array(),
			'deny'     => array(),
			'logout'   => true,
		);

		$options = array_merge($defaults, $params);
		$page    = $site->pages()->active(); 

		if(in_array($page->uid(), $options['ignore'])) return true;

		// get the current user
		$user = self::user();

		if(!$user) {
			if($options['logout']) self::kill();
			go(url($options['redirect']));
		}

		$allowed = false;

		if(is_string($options['allow'])) $options['allow'] = array($options['allow']);
		if(is_string($options['deny']))  $options['deny']  = array($options['deny']);
		
		if(empty($options['allow'])) {
			$allowed = true;
		} else {

			foreach($options['allow'] as $allow) {

				// user 
				if(preg_match('!^user:!', $allow)) {
					$username = str_replace('user:', '', $allow);
					
					if(str::lower($username) == str::lower($user->username())) {
						$allowed = true;
						break;
					}

				} else if(preg_match('!^group:!', $allow)) {
					$group = str_replace('group:', '', $allow);

					if($user->group() != '' && str::lower($group) == str::lower($user->group())) {
						$allowed = true;
						break;
					}

				}

			}

		}

		// deny loop

		foreach($options['deny'] as $allow) {

			// user 
			if(preg_match('!^user:!', $allow)) {
				$username = str_replace('user:', '', $allow);
				
				if(str::lower($username) == str::lower($user->username())) {
					$allowed = false;
					break;
				}

			} else if(preg_match('!^group:!', $allow)) {
				$group = str_replace('group:', '', $allow);

				if($user->group() != '' && str::lower($group) == str::lower($user->group())) {
					$allowed = false;
					break;
				}

			}

		}


		if(!$allowed) {
			if($options['logout']) self::kill();
			go(url($options['redirect']));
		}

		return true;

	}

	static protected function load($username) {

		$username = str::lower($username);

		$dir  = c::get('root.site') . '/accounts';		
		$file = $dir . '/' . $username . '.php';

		if(!is_dir($dir) || !file_exists($file)) return false;

		$content = file_get_contents($file); 
		$yaml    = yaml($content);

		// remove the php direct access protection line
		unset($yaml[0]);

		return new AuthUser($yaml);

	}

	static protected function checkPassword($user, $password) {
    
   	// check for empty passwords    
    if(empty($password) || $user->password() == '') return false;

    // get the encryption
    $encryption = $user->encrypt();
        
    // handle the different 
    // encryption types        
    switch($encryption) {
      // sha1 encoded
      case 'sha1':
        return (sha1($password) == $user->password()) ? true : false;
        break;
      // md5 encoded
      case 'md5':
        return (md5($password) == $user->password()) ? true : false;
        break;
      // plain passwords
      default:
        return ($password == $user->password()) ? true : false;
        break;    
    }    
    
    // we should never get here
    // but let's make sure
    return false;

	}

}

class AuthUser extends obj {


}
