<?php

namespace Replicant\Listeners;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * Listens to post events
 */
class Post {

   use \Replicant\Listener;

   private $replicant_metadata_key;

   public function __construct() {
      $this->replicant_metadata_key = \Replicant\Config::$TABLES_PREFIX . "metadata";
      add_action("save_post", [$this, "listen_save"], 10, 3);
      add_action("before_delete_post", [$this, "listen_delete"], 10, 3);
   }

   /**
    * Fires once when a post has been saved or updated
    *
    * @param  int     $id        Post ID
    * @param  WP_Post $post      Post Object
    * @param  boolean $is_update Update status
    */
   public function listen_save($post_id, $post, $is_update){
      // Avoid auto saved and drafted posts
      // TODO: Add these
      // wp_is_post_autosave
      // wp_is_post_revision
      if($post->post_status !== 'publish') {
         return;
      }

      // Avoid firing the hook twice
      if(defined('REST_REQUEST') && REST_REQUEST) {
         return;
      }

      $current_node = new \Replicant\Node();
      $is_delete = false;

      // Parse it
      $parsed_post = $this->parse($post, $is_update);
      $metadata = wp_unslash($parsed_post["replicant_metadata"]);

      // Publish it across all trusted nodes
      if($parsed_post) {
         $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();
         if(!empty($trusted_nodes)) {
            foreach($trusted_nodes as $node) {
               // Do not send the request back where it came from
               if($metadata["node_hash"] !== $node->hash) {
                  // Publish the post
                  $response = new \Replicant\Publishers\Post(
                     $parsed_post,
                     $node,
                     $is_update,
                     $is_delete
                  );
               }
            }
         }
      }
   }

   public function listen_delete($post_id, $post) {
      // Avoid auto saved and drafted posts
      if($post->post_status !== 'trash') {
         return;
      }

      // Avoid firing the hook twice
      if(defined('REST_REQUEST') && REST_REQUEST) {
         return;
      }

      $is_delete = true;
      $is_update = false;

      // Parse it
      $parsed_post = $this->parse($post);

      // Publish it across all trusted nodes
      if($parsed_post) {
         $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();
         if(!empty($trusted_nodes)) {
            foreach($trusted_nodes as $node) {
               if($parsed_post["replicant_metadata"]["node_hash"] !== $node->hash) {
                  new \Replicant\Publishers\Post($parsed_post, $node, $is_update, $is_delete);
               }
            }
         }
      }
   }

   /**
    * Parse and filter out product based on its type
    *
    * @param  \WP_Post $post      Post
    * @param  bool     $is_update Update status
    * @return array               Parsed post and its properties
    */
   private function parse($post, bool $is_update = false): array {
      $parsed_post = null;

      // Replicant attached metadata
      $sticky = is_sticky( $post->ID ) || 0;
      $replicant_metadata = $this->generate_metadata();
      $metadata = wp_slash(json_encode($replicant_metadata));

      // Add metadata to current $post
      $metadata_id = add_post_meta(
         $post->ID,
         $this->replicant_metadata_key,
         $metadata,
         true
      );

      // Check if it's a WooCommerce product
      // and whether it's activated or not
      if($post->post_type === 'product' && \Replicant\Helper::is_woocommerce_active()) {
         $parsed_post = $this->do_woocommerce($post);
      }

      // Check If it's actually a post or page
      if($post->post_type === 'post' || $post->post_type === 'page') {
         $parsed_post = $this->do_post($post->ID, $post);
      }

      $get_metadata = get_post_meta($post->ID);

      return [
         "replicant_metadata" => $replicant_metadata,
         "metadata"           => $get_metadata,
         "post"               => $parsed_post->to_array(),
         "is_update"          => $is_update
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
