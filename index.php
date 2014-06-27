<?php

error_reporting(E_ERROR | E_PARSE);

// Load Spyc class for YAML interpretation
include_once("source/Spyc.php");

// Application configuration
$config = Spyc::YAMLLoad("config/config.yaml");

$template_vars['config'] = $config;

// Load master class
include_once("source/Ratchet.php");

// Load all source files
foreach (glob("source/*.php", GLOB_NOSORT) as $filename) {

  include_once($filename);
}

// Twig template engine
include_once('source/twig/lib/Twig/Autoloader.php');
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem($config['template_dir']);
$twig = new Twig_Environment($loader, array(
    'cache' => ($config['template_caching'] ? "lib/cache" : false),
));

// Run Ratchet, run
$Ratchet = new Ratchet();

?>