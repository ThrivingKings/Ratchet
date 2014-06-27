<?php

/*
 *
 * Model: Index
 * Handles top level request as well as custom "docs" route
 *
*/

class Index {

  // Homepage (url: /)
  function get_index() {

    // No need to do anything except load the template
    Template::load("index.html");
  }
}

?>
