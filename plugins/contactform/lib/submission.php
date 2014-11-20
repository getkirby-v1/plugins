<?php 

if(!class_exists('Closure')) die('The Submission Class is not compatible with your PHP version. Closures must be available');

/*
 * Submission Class
 *
 * Makes it easy to handle 
 * form submission
 *
 * Usage: 
 * 
 * <code>
 *
 * // a simple contact form 
 * $submission = new Submission(array(
 *   // list of required fields
 *   'required' => array('name', 'email', 'text'),
 * 
 *   // validation event 
 *   'validate' => function() {
 *     if(!filter_var($self->value('email'), FILTER_VALIDATE_EMAIL)) $self->addInvalid('email');
 *   },  
 * 
 *   // form action event
 *   'action' => function($self) {
 *     // get all sanitized data with $self->data()
 *     // send your email here
 *   },
 *   
 *   // yay, success
 *   'success' => function() {
 *     // send the user to a thank you page or something like that
 *   },
 *
 *   // react on errors
 *   'error' => function() {
 *     // maybe log the error or do something else here
 *   }
 *
 * ));
 *
 * </code>
 *
 *
 * @version   1.0
 * @author    Bastian Allgeier <http://bastianallgeier.com>
 * @copyright Copyright 2012 Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * 
 */
class Submission {

  var $defaults  = array();
  var $options   = array();  
  var $data      = array();  
  var $input     = array();
  var $missing   = array();
  var $invalid   = array();
  var $errors    = array();
  var $error     = false;
  var $alert     = false;
  var $response  = array();
  var $triggered = array();
  var $submitted = false;

  /*
   * Initializes the class
   * It's also responsible for triggering 
   * the first event (request)
   * 
   * @param array $params An array of parameters and events
   */
  public function __construct($params = array()) {

    $this->defaults();
    $this->options($params);
    $this->trigger('request');
        
  }

  /*
   * Getter and setter for defaults
   * This also returns the initial set of defaults
   * 
   * @param  mixed $key an optional key to use this method as getter
   * @param  mixed $value an optional value to use this method as setter
   * @return mixed 
   */
  public function defaults($key = null, $value = null) {
    
    if($key !== null && $value !== null) return $this->defaults[$key] = $value;
    if($key !== null) return @$this->defaults[$key];
        
    return $this->defaults = array(
      'method'   => 'post',
      'ajax'     => false,
      'required' => array(), 
      'keep'     => array(),
	  'honeypot' => null,
            
      // on request event
      'request'  => function($self) {
        
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $formMethod    = strtoupper($self->option('method'));
        
        // check for a valid request method        
        if($requestMethod != $formMethod) return false;

        // check for ajax requests
        if($self->option('ajax') && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') return false;
      
        // assign the request data        
        $self->input = ($requestMethod == 'POST') ? $_POST : $_GET;

        // do some basic sanitizing
        $self->data = $self->input = array_map(array($self, 'sanitize'), $self->input);
		
		// Check for the honeypot field before removing unwanted input fields
        if ($self->isSpam()) {
          // Pretend everything went well, so the bot does not become suspicious
          return $self->trigger('success');
        }
		
        // check for missing fields
        foreach($self->option('required') as $required) {
          if(empty($self->data[$required])) $self->addMissing($required);
        }
        
        // remove unwanted fields from the input
        $keep = $self->option('keep');

        if(!empty($keep)) {
          $cleaned = array();
          foreach($keep as $key) {
            // only add wanted elements to the cleaned array
            $cleaned[$key] = @$self->data[$key];
          }
          $self->data = $cleaned;
        }
        
        // do more filtering
        $self->trigger('filter');
        
        // only submit the form if any data is left
        if(!empty($self->data)) {
        
          // check for invalid fields
          $self->trigger('validate');
          
          // react on missing fields      
          if(!empty($self->missing) || !empty($self->invalid)) return $self->trigger('error');
          $self->submitted = true;
          $self->trigger('submit');
        
        }

      },

      'filter'   => function($self) {},
      'validate' => function($self) {},
      'submit'   => function($self) { return $self->trigger('success'); },
      'success'  => function($self) {},
      'error'    => function($self) {},
    );
  
  }

  /*
   * Getter and setter for options
   * 
   * @param  mixed $key an optional key to use this method as getter
   * @param  mixed $value an optional value to use this method as setter
   * @return mixed 
   */
  public function option($key = null, $value = null) {
    if($key !== null && $value !== null) return $this->options[$key] = $value;
    if($key !== null) return @$this->options[$key];
    return $this->options;
  }

  /*
   * Getter and setter for all options
   * 
   * @param  mixed $key an optional key to use this method as getter
   * @param  mixed $value an optional value to use this method as setter
   * @return mixed 
   */
  public function options($key = null, $value = null) {
    if($key !== null && $value !== null) return $this->options[$key] = $value;
    if($key !== null && is_array($key))  return $this->options = array_merge($this->defaults, $key);
    if($key !== null) return @$this->options[$key];
    return $this->options;
  }
  
  /*
   * Checks if the passed field is required
   * 
   * @param  string $field the name of the field, which should be checked
   * @return boolean
   */
  public function isRequired($field = false) {
    return in_array($field, $this->option('required'));
  }
  
  /*
   * Checks if the passed field is missing
   * 
   * @param  string $field the name of the field, which should be checked
   * @return boolean
   */
  public function isMissing($field) {
    return in_array($field, $this->missing);
  }

  /*
   * Add a field to the list of missing fields
   * 
   * @param  string $field the name of the field, which should be added
   * @return void
   */
  public function addMissing($field) {
    $this->missing[] = $field;
  }
    
  /*
   * Checks if the passed field is invalid
   * 
   * @param  string $field the name of the field, which should be checked
   * @return boolean
   */
  public function isInvalid($field) {
    return in_array($field, $this->invalid);
  }

  /*
   * Add a field to the list of invalid fields
   * 
   * @param  string $field the name of the field, which should be added
   * @return void
   */
  public function addInvalid($field) {
    $this->invalid[] = $field;
  }

  /*
   * Get or set a value from the filtered data
   * 
   * @param  string $key the name of the field
   * @param  string $value optional value to use this as setter
   * @return mixed The content of the field
   */
  public function value($key, $value = null) {
    if($value !== null) return $this->data($key, $value);
    $data = $this->data;
    return @$data[$key];
  }

  /*
   * Retrieves a value from the passed data
   * and converts it to safe html
   *
   * @param  string $field the name of the field
   * @return mixed The sanitized content of the field
   */
  public function htmlValue($field) {
    return htmlentities($this->value($field), ENT_COMPAT, 'UTF-8');  
  }

  /*
   * Checks if a field or the entire form has failed
   *
   * @param  string $field optional name of the field, which should be checked. If no field is passed, it will be checked if the form submission failed or not. 
   * @return boolean
   */
  public function isError($field = false) {
    if($field) return (in_array($field, $this->errors)) ? true : false;
    return $this->error;
  }
  
  /*
   * Checks if the honeypot field has been filled.
   *
   * @return boolean
   */
  public function isSpam() {
    $field = $this->option('honeypot');

    // Spam detection is optional. The honeypot field is only checked if 
    // a valid field name is specified via the options.
    if (!empty($field)) {
      $value = $this->data($field);
      return !empty($value);
    }

    // Otherwise always return false
    return false;
  }

  /*
   * Returns an array with all failed fields
   *
   * @return array
   */
  public function errors() {
    return $this->errors;
  }
  
  /* 
   * Checks if the form has been submitted
   * 
   * @return boolean
   */
  public function isSubmitted() {
    return $this->submitted;
  }

  /*
   * Setter and getter for the form alert
   * You can use this to define an alert message, 
   * which can later be used to return an error message
   * to the user on error.
   *
   * @param  string $value optional value for the alert to use this as setter
   * @return string
   */
  public function alert($value = false) {
    if($value) return $this->alert = $value;
    return $this->alert;    
  }

  /*
   * Setter and getter for the submitted and filtered data
   *
   * @param  mixed $key optional key to use this as getter
   * @param  mixed $value optional value to use this as setter
   * @return mixed
   */
  public function data($key = null, $value = null) {
    if($key !== null && $value !== null) return $this->data[$key] = $value;  
    if($key !== null) return @$this->data[$key];
    return $this->data;
  }

  /*
   * Triggers a form event
   *
   * @param  string $event the name of the event
   * @param  array  $args optional array of arguments, which will be passed to the event
   * @return mixed
   */
  public function trigger($event, $args = array()) {
    $func = $this->option($event);
    
    // prepare special error stuff
    if($event == 'error') {
      
      // mark the submission as failed
      $this->error = true;
      
      // collect all error fields
      $this->errors = array_merge($this->missing, $this->invalid);
            
    } 
    
    // check if the event is a function at all    
    if(!is_a($func, 'Closure') && preg_match('/^default\:/', $event)) {
      $event = str_replace('default:', '', $event);      
      $func  = $this->defaults($event);
    }
    
    if(is_a($func, 'Closure')) {
  
      // track all triggered events    
      $this->triggered[$event] = array(
        'name' => $event,
        'func' => $func,
        'args' => $args, 
        'time' => microtime()
      );

      // invoke the function
      $func($this, $args);

    }
    
    return false;

  }

  /*
   * Returns an array of triggered events
   *
   * @param  string $key optional key to get a specific event from the array
   * @return array
   */
  public function triggered($key = null) {
    if($key !== null) return @$this->triggered[$key];
    return $this->triggered;
  }

  /*
   * Resets the passed data
   * This is handy to empty the form after submission
   */
  public function reset() {
    $this->data    = array();  
    $this->input   = array();
    $this->missing = array();
    $this->invalid = array();
    $this->errors  = array();
    $this->error   = false;
  }

  /*
   * Sanitizes request input
   * 
   * @param  mixed $string
   * @return mixed
   */
  public function sanitize($string) {
    if(is_array($string)) {
      return array_map(array($this, 'sanitize'), $string);
    } else {
      return (get_magic_quotes_gpc()) ? trim(stripslashes(stripslashes($string))) : trim($string);
    }
  }
  
}
