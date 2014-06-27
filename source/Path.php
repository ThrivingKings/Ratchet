<?php

// Global variables
$paths = array();
$redirects = array();

/**
 *
 * Custom paths class
 *
 * @author Daniel Raftery
 * @version 0.0.1
*/
class Path {

  /**
   *
   * Specifying a custom path
   *
   * @param [string] Path of path to match
   * @param [string] Model to be used
   * @param [string] Function to be used
  */
  function set($path, $class, $function) {

    global $paths;

    $path_array = explode("/", $path);

    $vars = array();

    foreach($path_array as $i => $_path) {

      if( strpos($_path, ":") !== false ) {

        $vars[$i] = str_replace(":", "", $_path);
      }
    }

    $paths[$path_array[1]] = array(
      'path' => $path,
      'model' => $path_array[1],
      'class' => $class,
      'function' => $function,
      'vars' => $vars
    );
  }

  /**
   *
   * Specify a redirect on route
   *
   * @param [string] Path of path to match
   * @param [string] Path to be redirected to
  */
  function redirect($path, $to) {

    global $redirects;

    $redirects[$path] = $to;
  }
}

?>