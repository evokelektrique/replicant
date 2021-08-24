<?php

namespace Replicant\Controllers;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Publish Controller, listens for an entry 
 * and it will insert the exact type and metadata
 */
class Publish {

   use \Replicant\Controller;

   /**
    * Controller REST API Namespace name
    * 
    * @var string
    */
   public $namepsace;

   /**
    * Namespace resource name
    * 
    * @var string
    */
   public $resource;


   public function __construct() {
      $this->namespace = "replicant/v1";
      $this->resource = "/publish";
   }

   public function register_routes() {
      // CRUD Post endpoints
      register_rest_route(
         $this->namespace, 
         $this->resource . "/posts", 
         [
            // Register the readable endpoint
            [
               "methods"             => "POST",
               "callback"            => [$this, "create_post"],
               "permission_callback" => "__return_true"
            ]
         ]
      );
   }

   ////////////////////////
   // Response Callbacks //
   ////////////////////////

   public function create_post($request) {
      $fields    = $request->get_json_params();

      // Remove unnecessary fields
      unset($fields["post"]["ID"]);
      unset($fields["meta_data"]["_edit_lock"]);
      unset($fields["meta_data"]["_pingme"]);
      unset($fields["meta_data"]["_encloseme"]);

      $post               = $fields["post"];
      $post["meta_input"] = $fields["meta_data"];

      // Create post
      $insert_id = $this->post_exists($fields['post']['post_title']) || wp_insert_post($post, true);

      error_log(print_r($this->post_exists($fields['post']['post_title']), true));
      error_log(print_r($insert_id, true));

      $message = __("Post successfully created.", "replicant");
      $status  = true;

      if(is_wp_error($insert_id)) {
         $message = $insert_id->get_error_message();
         $status  = false;
      }

      return rest_ensure_response( ["status" => $status, "message" => $message] );
   }


   /**
    * Determines if a post exists based on title.
    *
    * @since 2.0.0
    *
    * @global wpdb $wpdb WordPress database abstraction object.
    *
    * @param string $title   Post title.
    * @return int Post ID if post exists, 0 otherwise.
    */
   private function post_exists($title) {
      // Copied from WordPress source code
      // https://core.trac.wordpress.org/browser/tags/5.8/src/wp-admin/includes/post.php#L777
      // TODO: Find a better solution.

      global $wpdb;

      $post_title   = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );

      $query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
      $args  = array();

      if ( ! empty( $title ) ) {
         $query .= ' AND post_title = %s';
         $args[] = $post_title;
      }

      if ( ! empty( $args ) ) {
         return (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );
      }

      return 0;
   }      

}
