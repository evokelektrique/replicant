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

   /**
    * Initialize router endpoints
    */
   public function __construct() {
      $this->namespace = "replicant/v1";
      $this->resource = "/posts";
   }

   public function register_routes() {
      // CRUD Post endpoints
      register_rest_route(
         $this->namespace,
         $this->resource,
         [
            // Create
            [
               "methods"             => "POST",
               "callback"            => [$this, "create_post"],
               "permission_callback" => [$this, 'check_permissions']
            ],

            // Delete
            [
               "methods"             => "DELETE",
               "callback"            => [$this, "delete_post"],
               "permission_callback" => [$this, 'check_permissions']
            ]
         ]
      );


   }

   ////////////////////////
   // Response Callbacks //
   ////////////////////////

   public function delete_post(\WP_REST_Request $request) {
      $build  = $this->build_post($request);
      $post   = $build["post"];
      $search = $this->post_exists($post['post_title']);

      $message = __("Post not found.", "replicant"); // Message to response back
      $status  = false;                              // Response status

      $force   = true;                               // Force delete or not

      if($search !== null) {
         $delete  = wp_delete_post( $search->ID, $force );
         $message = __("Post(".$search->ID.") successfully deleted.", "replicant");
         $status  = true;
      }

      return rest_ensure_response(["status" => $status, "message" => $message]);
   }

   public function create_post(\WP_REST_Request $request) {
      $build     = $this->build_post($request);
      $post      = $build["post"];
      $post_id   = $build["id"];
      $is_update = $build["is_update"];
      error_log(print_r(["is_update" => $is_update, "post" => $post], true));

      $search = $this->post_exists($post['post_title']);

      // Message to response back
      $message = __("Post successfully created.", "replicant");
      $status  = true;

      ////////////////
      // CRUD Posts //
      ////////////////
      $insert = null;

      if($search === null && !$this->is_duplicate_node($build["replicant_metadata"])) {
         // Create Post
         $insert = wp_insert_post($post, true);

      } elseif($is_update && !$this->is_duplicate_node($build["replicant_metadata"])) {
         // Update Post
         unset($post["import_id"]);
         $post["ID"] = $post_id;
         $insert = wp_update_post($post);

      } else {
         // Duplication error
         $message = __("Post duplicate.", "replicant");
         $status  = false;
      }

      if(!is_null($insert) && is_wp_error($insert)) {
         $message = $insert->get_error_message();
         $status  = false;
      }

      // TODO: Fix sticky posts
      // if($status) {
      //    // Handle sticky posts
      //    if($replicant_metadata["is_sticky"]) {
      //       stick_post($post_id);
      //    }
      // }

      return rest_ensure_response( ["status" => $status, "message" => $message] );
   }

   /**
    * Determines if a post exists based on title.
    *
    * @param string $post_title Post title
    * @param string $post_type  Post type
    *
    * @return WP_Post|null Post object if post exists, null otherwise
    */
   private function post_exists(string $post_title, string $post_type = "post") {
      $output_type = OBJECT;
      $post        = get_page_by_title( $post_title, $output_type, $post_type );

      return $post;
   }

   /**
    * Determine wheither the sender node hash is equal to the current node or not
    *
    * @param  object  $replicant_metadata Sender node metadata
    * @return boolean                     Duplication status
    */
   private function is_duplicate_node(array $replicant_metadata): bool {
      if(empty($replicant_metadata)) {
         return false;
      }

      $current_node = new \Replicant\Node();

      if($replicant_metadata["node_hash"] === $current_node->hash) {
         return true;
      }

      return false;
   }

   /**
    * Generate post from request and delete unnecessary fields
    *
    * @param  WP_REST_Request $request Wordpress REST
    * @return Array                    Post and Node metadata
    */
   private function build_post(\WP_REST_Request $request): array {
      $fields    = $request->get_json_params();
      $is_update = $fields["is_update"];
      $post_id   = $fields["post"]["ID"];
      $post      = [];

      // Remove unnecessary fields
      unset($fields["metadata"]["_edit_lock"]);
      unset($fields["metadata"]["_encloseme"]);
      unset($fields["metadata"]["_pingme"]);
      unset($fields["post"]["ID"]);

      $replicant_metadata = $fields["replicant_metadata"];
      $post               = $fields["post"];
      $post["meta_input"] = $fields["metadata"];
      $post["import_id"]  = $post_id;

      return [
         "id"                 => $post_id,
         "post"               => $post,
         "replicant_metadata" => $replicant_metadata,
         "is_update"          => $is_update
      ];
   }

   /**
    * Controller validation middleware
    *
    * @param  WP_REST_Request $request Wordpress REST
    * @return boolean                  Pass on `true`, reject on `false`
    */
   public function check_permissions(\WP_REST_Request $request): bool {
      $fields = $request->get_json_params();
      $target_node_hash = $fields["replicant_metadata"]["node_hash"];
      $trusted_nodes = \Replicant\Tables\Nodes\Functions::get_all_trusted_nodes();

      // Iterate through a list of trusted nodes
      // and check if the target node is in the list.
      foreach($trusted_nodes as $trusted_node) {
         if($trusted_node->hash === $target_node_hash) {
            return true;
         }
      }

      // Returns HTTP 401 error
      return false;
   }

}
