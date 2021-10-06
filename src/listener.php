<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * Define listener functions
 */
trait Listener {

   public function generate_node_metadata() {
      $current_node = new Node();

      $metadata = [];
      // $metadata["hash"] = \Replicant\Helper::generate_random_string();
      $metadata["sender_node_hash"] = $current_node->hash; // Reason: Prevent duplicate publishes

      return $metadata;
   }

}
