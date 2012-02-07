<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

// start a session if not started yet
s::start();

// initialize a new visitor
$site->visitor = new visitor();

// login helper function
// this will be used in the login template
function login($params=array()) {
  global $site;
  return $site->visitor->login($params);
}

// the visitor class
class visitor extends obj {

  function __construct() {

    global $site;
    
    // check for an authenticated user
    // first and assign it to this object
    // if logged in. 
    if($user = $this->checkAuth()) {
      $this->_ = $user;
    } 
    
    // implement the logout logic
    if($site->uri->path() == c::get('visitor.logout', 'logout')) {
      $this->logout();  
      go(url());
    }

  }
  
	function checkAuth() {
    
    // try to find the token in the cookie
    $token = cookie::get(c::get('visitor.cookie', 'visitor'));
    
    // no cookie, no logged in user
    if(empty($token)) return false;
    
    // get the matching user data from the session array
    // for the token from the cookie. 
		$user = s::get($token, false);
    
    // again: no data, no logged in user
    if(empty($user)) return false;
    
    // get the account setup
    $accounts = c::get('visitor.accounts', array());
    
    // no account setup or no valid user return
    if(empty($accounts) || empty($user['username'])) return false;
    
    // fetch the user data from the accounts setup
    $data = a::get($accounts, $user['username']);
    
    // no data for this user, return    
    if(empty($data)) return false;
    
    // don't store the password anywhere    
    unset($data['password']);
    
    // add the username and token to the 
    // user data from the account setup    
    $data['username'] = $user['username'];
    $data['token']    = $token;
    
    // return the entire user set
    return $data;

	}

	function checkPassword($user, $password) {
    
    // check for empty passwords    
    if(empty($password) || empty($user['password'])) return false;

    // get the encryption
    $encryption = a::get($user, 'encryption', false);
        
    // handle the different 
    // encryption types        
    switch($encryption) {
      // sha1 encoded
      case 'sha1':
        return (sha1($password) == $user['password']) ? true : false;
        break;
      // md5 encoded
      case 'md5':
        return (md5($password) == $user['password']) ? true : false;
        break;
      // plain passwords
      default:
        return ($password == $user['password']) ? true : false;
        break;    
    }    
    
    // we should never get here
    // but let's make sure
    return false;

	}
		
	function login($params=array()) {

    $defaults = array(
      'error'   => 'Invalid username or password',
      'go'      =>  false,
      'trigger' => 'login',
      'fields'  =>  array(
        'username' => 'username',
        'password' => 'password'
      )
    );
    
    $options = array_merge($defaults, $params);    

    // check if the user is already logged in
    if($this->isLoggedIn()) go(url($options['go']));
  
    // only login if all fields have been passed
    // and the trigger has been sent
    if(
      !isset($_REQUEST[ $options['fields']['password'] ]) || 
      !isset($_REQUEST[ $options['fields']['username'] ]) || 
      !isset($_REQUEST[ $options['trigger'] ])) {
      return false;
    }
    
    // restart the session to make sure 
    // that any old values have been killed before    							
    s::restart();

    // get all sent values from the request
		$password = get( $options['fields']['password'] );
		$username = get( $options['fields']['username'] );

    // fetch the accounts setup
    $accounts = c::get('visitor.accounts', array());
    
    // react on a missing account setup
    if(empty($accounts)) return array(
      'status' => 'error',
      'msg'    => $options['error']
    );
    
    // check for a user 
		if(!array_key_exists($username, $accounts)) return array(
			'status' => 'error',
			'msg'    => $options['error']
		);
    
    // get the user data
    $user = $accounts[$username];
    
    // check for a proper password
    if(!$this->checkPassword($user, $password)) return array(
			'status' => 'error',
			'msg'    => $options['error']    
    );

    // generate a random token
    $token = str::random();
    
    // don't store the password anywhere    
    unset($user['password']);
        
    // add the username. 
    // It's only the key of the array so far. 
    $user['username'] = $username;
    $user['token']    = $token;
    
    // store the token in the cookie
    // and the user data in the session    
    cookie::set(c::get('visitor.cookie', 'visitor'), $token);    		
		s::set($token, $user);
    
    // assign the user data to this obj
    $this->_ = $user;
    
    // go to the homepage		
    go(url($options['go']));

	}
	
	function logout() {
    
    // overwrite the token      
    $token = str::random();
    cookie::set(c::get('visitor.cookie', 'visitor'), $token);
    
    // restart the session    
    s::restart();
    
    // go to the homepage
    go(url());

	}

  function isLoggedIn() {
    return ($this->token && cookie::get(c::get('visitor.cookie', 'visitor')) && $this->token == cookie::get(c::get('visitor.cookie', 'visitor'))) ? true : false;
  }

  function isInGroup() {
    return (in_array($this->group(), func_get_args())) ? true : false;    
  }

  function firewall($setup=array()) {

    if(empty($setup)) return true;

    $allowGroups = str::split(@$setup['allow']['group']);
    $allowUsers  = str::split(@$setup['allow']['user']);
    $denyGroups  = str::split(@$setup['deny']['group']);
    $denyUsers   = str::split(@$setup['deny']['user']);

    if(empty($allowGroups) && empty($allowUsers) && empty($denyGroups) && empty($denyUsers)) return true;

    if(
      in_array($this->username(), $denyUsers)  && 
     !in_array($this->username(), $allowUsers) && 
     !in_array($this->group(),    $allowGroups)) {
      go(url());
    }

    if(
      in_array($this->group(),    $denyGroups)  && 
     !in_array($this->group(),    $allowGroups) && 
     !in_array($this->username(), $allowUsers)) {
      go(url());
    }
    
    // apply the allowed usernames and groups
    if(!empty($allowUsers)  && !in_array($this->username(), $allowUsers))  go(url());            
    if(!empty($allowGroups) && !in_array($this->group(),    $allowGroups)) go(url());

  }
  
}

?>