<?php

if(!class_exists('Submission'))  require_once('lib/submission.php');
if(!class_exists('Email')) require_once('lib/email.php');

class ContactForm extends Submission {

  public function __construct($params = array()) {
  
    $this->defaults();

    // set required and wanted fields
    $this->defaults('required', array('name', 'email', 'text'));
    $this->defaults('keep',     array('name', 'email', 'text'));
        
    // set the default subject
    $this->defaults('subject', 'New contact form submission');

    // take the current URL as the default goto URL
    $this->defaults('goto', $_SERVER['REQUEST_URI']);
    
    // set a custom validation event
    $this->defaults('validate', function($self) {
      // validate the email address    
      if(!filter_var($self->value('email'), FILTER_VALIDATE_EMAIL)) $self->addInvalid('email');
    });

    // try to send the email 
    $this->defaults('submit', function($self) {

      $to   = $self->option('to');
      $from = $self->option('from');
      
      if(!$from) $self->option('from', $to);
      
      // set the email body 
      $self->option('body', $self->body());
    
      // send the email form, pass all options            
      $send = email($self->options);
  
      if(error($send)) {
        $self->addInvalid('send');
        return $self->trigger('error');
      }
      
      $self->trigger('success');
              
    });
    
    // redirect to the "goto" url on success
    $this->defaults('success', function($self) {
      // redirect to callback url
      $url = $self->option('goto');
      if (!empty($url)) {
        go($self->option('goto'));
      }
    });

    // merge the defaults with the given options
    $this->options($params);

    // trigger the request
    $this->trigger('request');
  
  }
    
  function body() {
    
    $body = $this->option('body');
    
    if(empty($body)) {

      $body = snippet('contactform.mail', array(), true);
      
      if(empty($body)) {
        $body  = 'Name: {name}' . PHP_EOL;
        $body .= '---------' . PHP_EOL;
        $body .= 'Email: {email}' . PHP_EOL;
        $body .= '---------' . PHP_EOL;
        $body .= 'Text: {text}' . PHP_EOL;
      }
    }
    
    foreach($this->data() as $key => $value) {
      $body = str_replace('{' . $key . '}', $value, $body);     
    }    
    
    return trim($body);
        
  }

  function htmlBody() {
    return nl2br(html($this->body()));
  }
  
}
