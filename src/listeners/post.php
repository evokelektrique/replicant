<?php

namespace Replicant\Listeners;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * Listens to post events
 */
class Post {

   use \Replicant\Listener;

   private $replicant_node_metadata_key;

   public function __construct() {
      $this->replicant_node_metadata_key = \Replicant\Config::$TABLES_PREFIX . "node_metadata";
      add_action("save_post", [$this, "listen"], 10, 3);
   }

   /**
    * Fires once when a post has been saved or updated
    *
    * @param  int     $id        Post ID
    * @param  WP_Post $post      Post Object
    * @param  boolean $is_update Update status
    */
   public function listen($post_id, $post, $is_update){
      $current_node = new \Replicant\Node();

      // Parse it
      $parsed_post = $this->parse($post);

      // Publish it across all trusted nodes
      if($parsed_post) {
         $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();

         if(!empty($trusted_nodes)) {
            foreach($trusted_nodes as $node) {

               // TODO:
               //
               // Still sends the duplicated post, the problem is with
               // metadata `sender_node_hash` it changes everytime,
               // seems like checking the existance of metadata is not working
               // to solve this I or "you" have to fix the metadata validator
               if($parsed_post["replicant_node_metadata"]["sender_node_hash"] !== $node->hash) {
                  error_log(print_r(["node" => $node, "parsed_post" => $parsed_post], true));
                  new \Replicant\Publishers\Post($parsed_post, $node, $is_update);
               }
            }
         }
      }
   }

   /**
    * Parse and filter out product based on its type
    *
    * @param  \WP_Post $post    Post
    * @return array             Parsed metadata and post
    */
   private function parse($post) {
      // Avoid auto saved and drafted posts
      if($post->post_status !== 'publish') {
         return;
      }

      $parsed_post = null;

      // Replicant attached metadata
      $sticky                               = is_sticky( $post->ID ) || 0;
      $replicant_node_metadata              = $this->generate_node_metadata();
      // $replicant_post_metadata["is_sticky"] = $sticky || 0;
      $replicant_node_metadata_json_encoded = wp_slash(json_encode($replicant_node_metadata));

      // If the meta key does not exists, add the specific key to the object.
      // if(!metadata_exists('post', $post->ID, $this->replicant_node_metadata_key)) {
         add_post_meta(
            $post->ID,
            $this->replicant_node_metadata_key,
            $replicant_node_metadata_json_encoded,
            true
         );
      // }

      // Check if it's a WooCommerce product
      // and whether it's activated or not
      if($post->post_type === 'product' && \Replicant\Helper::is_woocommerce_active()) {
         $parsed_post = $this->do_woocommerce($post);
      }

      // Check If it's actually a post or page
      if($post->post_type === 'post' || $post->post_type === 'page') {
         $parsed_post = $this->do_post($post->ID, $post);
      }

      $metadata = get_post_meta($post->ID);

      return [
         "replicant_node_metadata" => $replicant_node_metadata,
         "metadata"                => $metadata,
         "post"                    => $parsed_post->to_array()
      ];
   }

   /**
    * Parse Post or Page
    *
    * @param  \WP_Post $post    Post or Page
    * @param  int      $post_id
    * @return \WP_Post
    */
   private function do_post(int $post_id, $post) {
      return $post;
   }

   /**
    * Parse WooCommerce product
    * TODO: Make it working later
    *
    * @param  \WP_Post $post WooCommerce Product Post
    * @return \WP_Post
    */
   private function do_woocommerce($post) {
      if(!$product = wc_get_product($post)) {
         return;
      }

      // Do something with $product later...
      return $product;
   }

}
