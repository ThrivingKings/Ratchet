<?php

/**
 *
 * Super simple error reporting
 *
 * @author Daniel Raftery
 * @version 0.0.5
*/
class Errors {

  /**
   *
   * Report the error
   *
   * @param [string] The message to be displayed
   * @param [boolean] Whether or not to display PHP backtrace
  */
  function report($msg, $backtrace = true) {

    // Check for error reporting flag
    if($this->config['error_reporting']) {

      Template::load('error_reporting.html', array("msg" => $msg, "backtrace" => print_r(debug_backtrace(), true)));

      exit;

    } else {

      // Prepare 404 response
      $Response = new Response();

      $Response->setCode(404);

      $Response->send();
      
      Template::load('404.html');

    }

    exit;
  }
}

?>