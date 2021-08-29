<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Define listener functions
 */
trait Listener {
   
   public function generate_metadata() {
      $metadata         = [];
      $metadata["hash"] = \Replicant\Helper::generate_random_string();

      return $metadata;
   }

}
