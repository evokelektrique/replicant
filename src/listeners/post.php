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
      if($post->post_status !== 'publish') {
         return;
      }

      $current_node = new \Replicant\Node();
      $is_delete = false;

      // Parse it
      $parsed_post = $this->parse($post, $is_update);

      // Publish it across all trusted nodes
      if($parsed_post) {
         $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();

         if(!empty($trusted_nodes)) {
            foreach($trusted_nodes as $node) {
               // TODO: add validator for not publishing the posts for the `sender_node`
               // the easiest way to solve this problem is to insert sender_node in
               // post meta data and then fetch it here and then if it the $node matched
               // sender_node of the post, ignore it and don't publish the post.
               if($parsed_post["replicant_node_metadata"]["sender_node_hash"] !== $node->hash) {
                  new \Replicant\Publishers\Post($parsed_post, $node, $is_update, $is_delete);
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

      $is_delete = true;
      $is_update = false;

      // Parse it
      $parsed_post = $this->parse($post);

      // Publish it across all trusted nodes
      if($parsed_post) {
         $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();

         if(!empty($trusted_nodes)) {
            foreach($trusted_nodes as $node) {
               if($parsed_post["replicant_node_metadata"]["sender_node_hash"] !== $node->hash) {
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
   private function parse($post, $is_update): array {
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
         "post"                    => $parsed_post->to_array(),
         "is_update"               => $is_update
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
