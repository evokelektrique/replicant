<?php

namespace Replicant\Listeners;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * Listens to post events
 */
class Post {

   use \Replicant\Listener;

   public function __construct() {
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

      $is_delete = false;

      // Parse it
      $parsed_post = $this->parse($post, $is_update);
      $metadata = $parsed_post["metadata"];

      // Publish it across all trusted nodes
      if($parsed_post) {
         $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();
         if(!empty($trusted_nodes)) {
            foreach($trusted_nodes as $node) {
               // Do not send the request back where it came from
               if($metadata["replicant_node_hash"] !== $node->hash) {
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
               if($parsed_post["metadata"]["replicant_node_hash"] !== $node->hash) {
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
      $current_node = new \Replicant\Node();
      $parsed_post = null;

      // Replicant attached metadata
      $sticky = is_sticky( $post->ID ) || 0;
      $replicant_metadata = $this->generate_metadata(get_the_title( $post ));

      $table_prefix = \Replicant\Config::$TABLES_PREFIX;

      // Add metadata to current $post
      $metadata_node_hash_id = add_post_meta(
         $post->ID,
         $table_prefix . "node_hash",
         $replicant_metadata["node_hash"],
         true
      );
      $metadata_post_hash_id = add_post_meta(
         $post->ID,
         $table_prefix . "post_hash",
         $replicant_metadata["post_hash"],
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

      $post_metadata = get_post_meta($post->ID);
      $post_categories = get_the_category($post->ID);
      $thumbnail_options = ["size" => "full"];

      $temp_data = [
         "featured_image_url" => get_the_post_thumbnail_url($post, $thumbnail_options["size"]),
         "post_tags" => $this->get_post_tags($post->ID),
         "post_categories" => $post_categories
      ];

      return [
         "metadata"  => $post_metadata,
         "post"      => $parsed_post->to_array(),
         "is_update" => $is_update,
         "node"      => $current_node->get_json(),
         "temp_data" => $temp_data
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

   /**
    * Retrieve post tags
    *
    * @param  int   $id Post ID
    * @return array     An array of post tags
    */
   private function get_post_tags(int $id): array {
      $temp_tags = get_the_tags($id);
      $tags = [];

      if($temp_tags) {
         foreach($temp_tags as $tag) {
            $tags[] = $tag->name;
         }

         return $tags;
      }

      return [];
   }

}
