<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Define controller functions
 */
trait Controller {
   
   public function get_route() {
      return '/?rest_route=/' . $this->namespace . $this->resource;
   }

}
