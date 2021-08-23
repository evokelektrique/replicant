<?php

namespace Replicant\Listeners;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Listens to post events
 */
class Post {

   public function __construct() {
      add_action("save_post", [&$this, "listen"], 10, 3);
   }

   /**
    * Fires once when a post has been saved or updated
    * 
    * @param  int     $id   Post ID
    * @param  WP_Post $post Post Object
    */
   public function listen($post_id, $post, $update){
      // Parse it
      $parsed_post = $this->parse($post_id, $post);

      // Publish it across all trusted nodes
      if($parsed_post) {
         $nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();
         if(!empty($nodes)) {
            foreach($nodes as &$node) {
               $publish_post = new \Replicant\Publishers\Post($parsed_post, $node);
            }
         }
      }
   }

   /**
    * Parse and filter out product based on its type
    * 
    * @param  int      $post_id Post ID
    * @param  \WP_Post $post    Post
    * @return array             Parsed metadata and post
    */
   private function parse(int $post_id, $post) {
      $parsed_post = null;
      
      // Avoid auto saved and drafted posts
      if($post->post_status !== 'publish') {
         return;
      }

      // Check if it's a WooCommerce product 
      // and whether it's activated or not
      if($post->post_type === 'product' && \Replicant\Helper::is_woocommerce_active()) {
         $parsed_post = $this->do_woocommerce($post);
      }

      // Check If it's actually a post
      if($post->post_type === 'post' || $post->post_type === 'page') {
         $parsed_post = $this->do_post($post);
      }

      $meta_data = get_post_meta($post_id);

      return [
         "meta_data" => $meta_data,
         "post"      => $parsed_post->to_array()
      ];
   }

   /**
    * Parse Post or Page
    * 
    * @param  \WP_Post $post Post or Page
    * @return \WP_Post
    */
   private function do_post($post) {
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
