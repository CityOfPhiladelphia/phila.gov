<?php


if ( class_exists( "Phila_Gov_Publish_Webhook" ) ){
  $phila_publish_webhook = new Phila_Gov_Publish_Webhook();
}

class Phila_Gov_Publish_Webhook {

  public function __construct(){
    add_action( 'publish_future_post', array($this, 'send_published_url'), 10, 3 );
    //add_action( 'post_updated', array($this, 'send_published_url'), 10, 3 );
    add_action( 'transition_post_status', array($this, 'send_published_url'), 'send_published_url', 10, 3);

  }

  //thanks to https://stackoverflow.com/questions/962915/how-do-i-make-an-asynchronous-get-request-in-php
  function curl_request_async($url, $params, $type='GET'){
    foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
      }
      $post_string = implode('&', $post_params);

      $parts = parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 30);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";

      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
  }

  // Listen for publishing of a new post
  function send_published_url($new_status, $old_status, $post) {

    if('publish' === $new_status && 'publish' !== $old_status && $post->post_type === 'post') {
      //currently edited post id
      //permalink
      $permalink = get_permalink( $post );
      error_log($permalink);
      $this->curl_request_async('https://sropxv2cze.execute-api.us-east-1.amazonaws.com/staging?', $permalink, 'GET');
    }

  }

}

?>
