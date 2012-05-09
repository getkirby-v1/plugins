<?php

/*
 * Simple Email Plugin for Kirby
 * 
 * It can be used to send emails quick and easy 
 * via mail() Postmark or Amazon SES
 *
 * By default mail() is used as transport method
 * But you can simply use it with Amazon or Postmark
 * By adding the following rules to your config…
 * 
 * Config for Amazon:
 * 
 * c::set('email.use', 'amazon');
 * c::set('email.amazon.key', 'Your Amazon SES Key');
 * c::set('email.amazon.secret', 'Your Amazon SES Secret');
 * 
 * Config for Postmark:
 * 
 * c::set('email.use', 'postmark');
 * c::set('email.postmark.key', 'Your Postmark Key');
 * 
 * Usage:
 * 
 * $send = email(array(
 *  'to'      => 'John Doe <john@doe.com>',
 *  'from'    => 'Your Name <your@email.com>',
 *  'subject' => 'My first Email',
 *  'body'    => 'Hello my friend! How are things going?'
 * ));
 * 
 * Author: Bastian Allgeier <bastian@getkirby.com>
 * License: MIT License
 * 
 */


function email($params=array()) {
  $email = new email($params);
  return $email->send();
}


class email {

  var $options = array();

  function __construct($params=array()) {
    
    $defaults = array(
      // global defaults
      'use'     => c::get('email.use', 'mail'),
      'from'    => c::get('email.from'),
      'replyto' => c::get('email.replyto'),
      'subject' => c::get('email.subject'),
      'to'      => c::get('email.to'),
      
      // postmark api defaults
      'postmark.key'  => c::get('email.postmark.key'),
      'postmark.test' => c::get('email.postmark.test'),

      // amazon api defaults
      'amazon.key'    => c::get('email.amazon.key'),
      'amazon.secret' => c::get('email.amazon.secret'),
    );
    
    $this->options = array_merge($defaults, $params);
    
    // set the from address as the replyto address if no replyto address is specified
    if(empty($this->options['replyto'])) $this->options['replyto'] = $this->options['from'];
              
  }
  
  public function send() {
    
    if(c::get('email.disabled')) return array(
      'status' => 'error',
      'msg'    => l::get('email.disabled', 'Email has been disabled')
    );
  
    if(!v::email($this->extractAddress($this->options['from']))) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.sender', 'Invalid sender'),
    );

    if(!v::email($this->extractAddress($this->options['to']))) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.recipient', 'Invalid recipient'),
    );

    if(!v::email($this->extractAddress($this->options['replyto']))) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.replyto', 'Invalid Reply-To Address'),
    );
    
    if(str::length($this->options['subject']) == 0) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.subject', 'The subject is missing'),
    );
        
    $method = 'sendWith' . str::ucfirst($this->options['use']);
        
    if(!method_exists(__CLASS__, $method)) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.mailer', 'This email service is not supported'),
    );

    return $this->$method();
          
  }
  
  private function sendWithPostmark() {

    if(!$this->options['postmark.key']) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.key', 'Invalid API key'),     
    );

    // reset the api key if we are in test mode
    if($this->options['postmark.test']) $this->options['postmark.key'] = 'POSTMARK_API_TEST';

    $url = 'http://api.postmarkapp.com/email';

    $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
      'X-Postmark-Server-Token: ' . $this->options['postmark.key']
    );

    $data = array(
      'From'     => $this->options['from'],
      'To'       => $this->options['to'],
      'ReplyTo'  => $this->options['replyto'],
      'Subject'  => $this->options['subject'],
      'TextBody' => $this->options['body']
    );

    $response = $this->post($url, a::json($data), array('headers' => $headers));
    $code     = @$response['http_code'];
    
    if($code != 200) return array(
      'status'   => 'error',
      'msg'      => l::get('email.error', 'The mail could not be sent!'),
      'response' => $response
    );
    
    return array(
      'status'   => 'success',
      'msg'      => l::get('email.success', 'The mail has been sent'),
      'response' => $response    
    );
      
  }

  private function sendWithAmazon() {

    if(!$this->options['amazon.key']) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.key', 'Invalid API key'),     
    );

    if(!$this->options['amazon.secret']) return array(
      'status' => 'error',
      'msg'    => l::get('email.error.invalid.secret', 'Invalid API secret'),     
    );

    $setup = array(
      'Action' => 'SendEmail',
      'Destination.ToAddresses.member.1' => $this->options['to'],
      'ReplyToAddresses.member.1' => $this->options['replyto'],
      'ReturnPath' => $this->options['replyto'],
      'Source' => $this->options['from'],
      'Message.Subject.Data' => $this->options['subject'],
      'Message.Body.Text.Data' => $this->options['body']
    );

    $params = array();

		foreach($setup as $key => $value) {
		  $params[] = $key . '=' . str_replace('%7E', '~', rawurlencode($value));
		}

		sort($params, SORT_STRING);
    
    $host      = 'email.us-east-1.amazonaws.com';
    $url       = 'https://' . $host . '/';
		$date      = gmdate('D, d M Y H:i:s e');
    $signature = base64_encode(hash_hmac('sha256', $date, $this->options['amazon.secret'], true));
    $query     = implode('&', $params);
		$headers   = array();

		$auth  = 'AWS3-HTTPS AWSAccessKeyId=' . $this->options['amazon.key'];
		$auth .= ',Algorithm=HmacSHA256,Signature=' . $signature;

		$headers[] = 'Date: ' . $date;
		$headers[] = 'Host: ' . $host;
		$headers[] = 'X-Amzn-Authorization: '. $auth;
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    
    $response = $this->post($url, $query, array('headers' => $headers));
    $code     = @$response['http_code'];
    
    if(!in_array($code, array(200, 201, 202, 204))) return array(
      'status'   => 'error',
      'msg'      => l::get('email.error', 'The mail could not be sent!'),
      'response' => $response
    );
    
    return array(
      'status'   => 'success',
      'msg'      => l::get('email.success', 'The mail has been sent'),
      'response' => $response    
    );

  }
  
  private function sendWithMail() {
    
    $headers = array();
    
    $headers[] = 'From: ' . $this->options['from'];
    $headers[] = 'Reply-To: ' . $this->options['replyto'];
    $headers[] = 'Return-Path: ' . $this->options['replyto'];
    $headers[] = 'Message-ID: <' . time() . '-' . $this->options['from'] . '>';
    $headers[] = 'X-Mailer: PHP v' . phpversion();
    $headers[] = 'Content-Type: text/plain; charset=utf-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';
                    
    ini_set('sendmail_from', $this->options['from']); 
    $send = mail($this->options['to'], str::utf8($this->options['subject']), str::utf8($this->options['body']), implode("\r\n", $headers));
    ini_restore('sendmail_from');

    if(!$send) return array(
      'status' => 'error',
      'msg'    => l::get('email.error', 'The mail could not be sent!')
    );

    return array(
      'status' => 'success',
      'msg'    => l::get('email.success', 'The mail has been sent')
    );
  
  }

  private function extractAddress($string) {
    if(v::email($string)) return $string;
    preg_match('/<(.*?)>/i', $string, $array);
    $address = @$array[1];
    return (v::email($address)) ? $address : false;
  }

  private function post($url, $data=false, $options=array()) {

    $data = (is_array($data)) ? http_build_query($data) : $data;

    $defaults = array(
      'timeout'  => 10,
      'headers'  => array(),
      'agent'    => 'email/php',
      'encoding' => 'utf-8'
    );
    
    $options = array_merge($defaults, $options);
    $ch = curl_init();

    $params = array(
      CURLOPT_URL             => $url,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_FOLLOWLOCATION  => true,
      CURLOPT_ENCODING        => $options['encoding'],
      CURLOPT_AUTOREFERER     => true,
      CURLOPT_USERAGENT       => $options['agent'],
      CURLOPT_CONNECTTIMEOUT  => $options['timeout'],
      CURLOPT_TIMEOUT         => $options['timeout'],
      CURLOPT_MAXREDIRS       => 10,
      CURLOPT_SSL_VERIFYPEER  => false,
      CURLOPT_SSL_VERIFYHOST  => false,
      CURLOPT_POST            => true,
      CURLOPT_POSTFIELDS      => $data
    );

    if(!empty($options['headers'])) $params[CURLOPT_HTTPHEADER] = $options['headers'];

    curl_setopt_array($ch, $params);

    $content  = curl_exec($ch);
    $error    = curl_errno($ch);
    $message  = curl_error($ch);
    $response = curl_getinfo($ch);

    curl_close($ch);

    $response['error']   = $error;
    $response['message'] = $message;
    $response['content'] = $content;

    if(a::get($response, 'error')) return array(
      'status'   => 'error',
      'msg'      => 'The remote request failed: ' . $response['message'],
      'response' => $response
    );

    if(a::get($response, 'http_code') >= 400) return array(
      'status'   => 'error',
      'msg'      => 'The remote request failed - code: ' . $response['http_code'],
      'code'     => $response['http_code'],
      'response' => $response
    );

    return $response;

  }

}