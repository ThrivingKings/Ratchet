<?php

/**
 *
 * Proper response sending
 *
 * @author Daniel Raftery
 * @version 0.0.1
*/
class Response {
  
  public $respond_code;
  public $respond_headers;

  // Set some generic variables
  function __construct() {

    $this->respond_code = 200;

    $this->respond_headers = array('Content-type: text/html');
  }

  /**
   *
   * Sets the header type
   *
   * @param [string|mixed] Single type or array of types of headers to send
  */
  function setType($types) {

    $headers = array("Content-type: $type");

    if(is_array($types)) {
      
      foreach($types as $type) {

        $headers[] = "Content-type: $type";
      }
    }

    $this->respond_headers = $headers;
  }

  /**
   *
   * Sets the HTTP code
   *
   * @param [int] HTTP code for response
  */
  function setCode($code) {

    $this->respond_code = $code;
  }

  /**
   *
   * Actual sending of the headers
  */
  function send() {

    header(':', true, $this->respond_code);

    foreach($this->respond_headers as $header) {

      header($header);
    }
  }

  /**
   *
   * Fast track function to send a JSON response (usually for API endpoints)
   *
   * @param [mixed] Response to be given
   * @param [boolean] Whether the response is already JSON encoded or not
   * @param [int] HTTP code to be used
  */
  function sendJSON($data, $encoded=true, $code=200) {

    $this->setCode($code);

    $this->setType('application/json');

    $this->send();

    echo ($encoded ? $data : json_encode($data));
  }
}

?>