<?php

// Initialize session
session_start();

/**
 *
 * Super basic session storage controller
 *
 * @author Daniel Raftery
 * @version 0.0.5
*/
class Session {
  
  /**
   *
   * Saves a key and value in the session
   *
   * @param [string] Key of storage
   * @param [string/mixed] Data to be assigned to key
   */
  function save($key, $value) {

    $current_session = self::confirm();

    if(!$current_session) {

      $current_session = self::create();
    }

    $_SESSION[$current_session['key']][$key] = $value;

  }

  /**
   *
   * Fetches data for supplied key from session
   *
   * @param [string] Key to retrieve
   * @return [string/mixed | boolean] String or array if found, false if not 
   */
  function fetch($key) {

    $current_session = self::confirm();

    return ($current_session ? $current_session[$key] : false);
  }

  /**
   *
   * Erases an entry in the session
   *
   * @param [string] Key to erase
   */
  function erase($key) {

    $current_session = self::confirm();

    $_SESSION[$current_session['key']][$key] = null;

    unset($_SESSION[$current_session['key']][$key]);
  }

  /**
   *
   * Confirms session is correct and hasn't expired
   *
   * @return [mixed | boolean] Current session or false if not found
   */
  private function confirm() {

    foreach($_SESSION as $key => $session) {

      // Validating session key to prevent spoofing
      if(strlen($key) === 40 && !empty($session['Ratchet']) && $_SERVER['HTTP_USER_AGENT'] === $session['user_agent']) {
        
        // Check expiration
        if(time() > $session['expires']) {

          // Clear if expired
          self::clear();
          return false;
        }

        // Good session
        return $session;
      }
    }

    return false;
  }

  /**
   *
   * Creates a spoof proof session instance
   *
   * @return [mixed] The new session
   */
  function create() {

    global $config;

    // Session key hash
    $key = sha1(microtime().substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5));

    // Base session data
    $current_session = array('Ratchet' => true, 'key' => $key, 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'established' => time(), 'expires' => strtotime($config['session_expiration']));

    $_SESSION[$key] = $current_session;

    return $current_session;
  }

  /**
   *
   * Clears all session data
   *
   */
  function clear() {

    session_unset();
    
    session_destroy();
  }
}

?>