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
      $fields  = $request->get_json_params();
      $post_id = $fields["post"]["ID"];

      // Remove unnecessary fields
      unset($fields["metadata"]["_edit_lock"]);
      unset($fields["metadata"]["_encloseme"]);
      unset($fields["metadata"]["_pingme"]);
      unset($fields["post"]["ID"]);

      $replicant_node_metadata = $fields["replicant_node_metadata"];
      $post["meta_input"]      = $fields["metadata"];
      $post                    = $fields["post"];
      // TODO: Check update event ( Create another function )
      $post["import_id"]       = $post_id;

      $message = __("Post successfully created.", "replicant");
      $status  = true;

      // Find/Create post
      $insert_id = null;
      $find_post = $this->post_exists($fields['post']['post_title']);
      if($find_post === null && !$this->is_duplicate_node($replicant_node_metadata)) {
         $insert_id = wp_insert_post($post, true);
      } else {
         $message = __("Post duplicate.", "replicant");
         $status  = false;
      }

      if(!is_null($insert_id) && is_wp_error($insert_id)) {
         $message = $insert_id->get_error_message();
         $status  = false;
      }

      if($status) {
         // // Handle sticky posts
         // if($replicant_node_metadata["is_sticky"]) {
         //    stick_post($post_id);
         // }
      }

      return rest_ensure_response( ["status" => $status, "message" => $message] );
   }


   /**
    * Determines if a post exists based on title.
    *
    * @param string $post_title   Post title
    * @param string $post_type    Post Type
    * 
    * @return WP_Post|null Post object if post exists, null otherwise
    */
   private function post_exists(string $post_title, string $post_type = "post") {
      $output_type = OBJECT;
      $post_type   = "post";
      $post        = get_page_by_title( $post_title, $output_type, $post_type );

      return $post;
   }      

   /**
    * Determine wheither the sender node hash is equal to the current node or not
    * 
    * @param  object  $node_metadata Sender node metadata
    * @return boolean                Duplication status
    */
   private function is_duplicate_node($node_metadata) {
      if(empty($node_metadata)) {
         return false;
      }
      
      $current_node = new \Replicant\Node();

      if($node_metadata["sender_node_hash"] === $current_node->hash) {
         return true;
      }

      return false;
   }

}
