<?php

namespace Replicant\Publishers;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Handle publishing posts
 */
class Post {

   public function __construct(array $body, object $target_node) {
      // error_log(print_r([json_encode($body), $target_node, $target_url], true));
      $target_url = \Replicant\Helper::generate_url_from_node($target_node);
      $this->perform($body, $target_url);
   }

   public function perform(array $body, array $target_url) {
      $body = json_encode($body);
      
   }

}
