<?php

/**
 *
 * Global controller
 *
 * @author Daniel Raftery
 * @version 1.0.0
*/
class Ratchet {

  public $REQUEST, $models;

  // Set variables
  function __construct() {

    global $config;

    $this->config = $config;

    $this->REQUEST['method'] = $_SERVER['REQUEST_METHOD'];
    
    $this->REQUEST['time'] = $_SERVER['REQUEST_TIME'];

    $this->REQUEST['uri'] = parse_url($_SERVER['REQUEST_URI']);

    // Load all models
    foreach (glob("models/*.php", GLOB_NOSORT) as $filename) {

      include_once($filename);

      $this->models = $this->load_models($filename);
    }

    $this->handle_request();

  }

  /**
   *
   * Handles the current request. Fails if unmatched path or Method/Function not found
  */
  private function handle_request() {

    global $paths, $redirects, $config;

    $req_array = explode("/", $this->REQUEST['uri']['path']);

    // Top level routes (/something)
    if( $req_array[1] && empty($req_array[2]) ) {

      // Considering the Index class to be the top dog
      $index_methods = get_class_methods("Index");

      // Full function to test against
      $_function = strtolower($this->REQUEST['method']).'_'.$req_array[1];
      
      // Cycle through index methods, seeking a match
      foreach($index_methods as $route) {
        
        // There's a match for this method_function in the Index model
        if($route==$_function) {

          $class = "Index";
          $function = $req_array[1];
        }

      }

      // There wasn't a match, but there is a route set that matches
      if( !$class && !empty($paths[$req_array[1]]) ) {

        $class = $paths[$req_array[1]]['class'];
        $function = "index";
      
      // Still no matching class
      } elseif(!$class) {

        // If a model exists, it must be the index of that model
        if(class_exists("$req_array[1]")) {

          $class = $req_array[1];
          $function = "index";
        } else {

          // Otherwise it's a function of the global Index model
          $class = "Index";
          $function = $req_array[1];
        }
      }
    
    // Nothing is set, so homepage we go
    } elseif(empty($req_array[1])) {

      $class = "Index";
      $function = "index";

    // All secondary routes should be pretty straightforward (/something/else = Something::method_else)
    } else {

      $class = $req_array[1];
      $function = (!empty($req_array[2]) ? $req_array[2] : "index");

    }
    
    // Path matching for secondary routes
    if(isset($paths[$class]) && $function !== "index") {

      $match = $paths[$class];

      $class = $match['class'];
      $function = $match['function'];

      foreach($req_array as $i => $req) {

        if($i>1) {

          $spot = (isset($match['vars'][$i]) ? $match['vars'][$i] : $i);

          if($this->REQUEST['method']=="GET") {
            
            $_GET[$spot] = $req;
          
          }

          $this->REQUEST['params'][$spot] = $req;
        }
      }
    }

    // Grab the uri to check for set redirects
    $uri = rtrim($_SERVER['REQUEST_URI'], "/");

    // Redirect away
    if(!empty($redirects[$uri])) {

      header("Location: ".$redirects[$uri]);
    }

    // This route's function
    $function = strtolower($this->REQUEST['method']).'_'.$function;

    // Make sure the class (model) exists
    if(class_exists("$class")) {

      // Make sure the method (method_function) exists
      if(method_exists("$class", "$function")) {

        // Build request array
        $this->REQUEST['class'] = $class;

        $this->REQUEST['function'] = $function;

        $config['called'] = $function;
        
        // Call the desired route's Model:method_function
        $class::$function($this->REQUEST);
      
      } else {
        // No function for the model
        Errors::report("Function not found: $class::$function");
      }
    
    } else {
      // No model found for route (probably won't happen as we're reverting to the index function)
      Errors::report("Method not found: $class");
    }
    
  }

  /**
   *
   * Runs through all Models and fills an array with all functions
   *
   * @param [string] filename to load models and functions
  */
  private function load_models($filename) {

    $tokens = token_get_all(file_get_contents($filename));

    $file_lines = file($filename);

    foreach($tokens as $token) {

      $token_name = token_name($token[0]);

      if($token_name=="T_CLASS") {

        $token_value = trim($file_lines[($token[2]-1)]);

        $token_value = str_ireplace("class", "", $token_value);

        $token_value = str_replace("{", "", $token_value);

        $token_value = trim($token_value);

        if($token_value) {

          $methods = get_class_methods($token_value);

          foreach($methods as $method) {

            if(preg_match('/(get|post|delete|put)/', $method)) {

              $class = strtolower($token_value);

              $this->methods[$class][] = $method;
            }
          }
        }
      }
    }
  }

}

?>