<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * Define listener functions
 */
trait Listener {

   public function generate_metadata(string $title = ""): array {
      $current_node = new Node();
      $post_title_hash = hash("sha256", $title, false);

      $metadata = [
         // Reason: Prevent duplicate publishes
         "node_hash" => $current_node->hash, // Sender node hash
         "post_hash" => $post_title_hash     // Current post hash
      ];

      return $metadata;
   }

}
