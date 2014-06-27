<?php

/**
 *
 * Twig template wrapper class
 *
 * @author Daniel Raftery
 * @version 0.0.1
*/
class Template extends Ratchet {
  
  /**
   *
   * Checks for existence, then loads template
   *
   * @param [string] Filename of the template to be loaded
   * @param [mixed] Variables to be passed to the template
  */
  function load($template, $vars = array()) {

    global $twig, $template_vars, $config;

    $template_vars['stylsheets'] = self::fetch_extras("css");
    $template_vars['javascript'] = self::fetch_extras("js");

    if(!is_array($vars)) {

      Errors::report("Template vars passed were not in array format");
    }

    if(!file_exists($config['template_dir'].'/'.$template)) {

      Errors::report("Template file $template not found in template directory: ".$config['template_dir']);
    }

    $vars = array_merge($template_vars, $vars);

    echo $twig->render($template, $vars);
  }

  /**
   *
   * Fetches the stylesheets and/or javascript files to be included in the template
   *
   * @param [string] Type of extras to fetch ("css" or "js")
   * @return [mixed] Array of specified files for the current 'called' function
  */
  function fetch_extras($type) {

    global $config;

    $return = array();

    if(file_exists("config/css-js.yaml")) {

      $css_js = Spyc::YAMLLoad("config/css-js.yaml");

      foreach($css_js as $path => $files) {

        if($path=="base" || $path==$config['called']) {
         
          if($type=="css" && !empty($files['css'])) {

            $return = array_merge($files['css'], $return);
          
          } elseif($type=="js" && !empty($files['js'])) {

            $return = array_merge($files['js'], $return);

          }
        }
      }

    }

    return $return;
  }
}

?>