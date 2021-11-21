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
      $build    = $this->build_post($request);
      $post     = $build["post"];
      $metadata = $build["metadata"];

      $message = __("Post not found.", "replicant"); // Message to response back
      $status  = false;                              // Response status
      $force   = true;                               // Force delete or not

      if($this->is_valid_metadata($metadata)) {
         $search = $this->post_exists($metadata["replicant_post_hash"]);

         if($search["status"]) {
            $delete  = wp_delete_post( $build["id"], $force );
            $message = __("Post with id(".$build["id"].") successfully deleted.", "replicant");
            $status  = true;
         }
      }

      return rest_ensure_response(["status" => $status, "message" => $message]);
   }

   public function create_post(\WP_REST_Request $request) {
      $build     = $this->build_post($request);
      $post      = $build["post"];
      $post_id   = $build["id"];
      $is_update = $build["is_update"];
      $metadata  = $build["metadata"];

      error_log(print_r($build, true));

      ////////////////
      // CRUD Posts //
      ////////////////
      $insert = null;

      if($this->is_valid_metadata($metadata)) {
         $is_duplicate_node = $this->is_duplicate_node($metadata["replicant_node_hash"]);
         $search = $this->post_exists($metadata["replicant_post_hash"]);
         if($search["status"] === false && !$is_duplicate_node) {
            // Create Post
            $insert = wp_insert_post($post, true);

            // Attach featured image to the post
            $file = $build["temp_data"]["featured_image_url"];
            $featured_image = $this->generate_featured_image($file, $insert);

            // Message to response back
            $message = __("Post successfully created.", "replicant");
            $status  = true;

         } elseif($is_update && !$is_duplicate_node) {
            // Update Post
            $post["ID"] = $search["post"]->post_id;
            $insert = wp_update_post($post);

            // Attach featured image to the post
            $file = $build["temp_data"]["featured_image_url"];
            $featured_image = $this->generate_featured_image($file, $insert);

            // Message to response back
            $message = __("Post successfully updated.", "replicant");
            $status  = true;

         } else {
            // Duplication error
            $message = __("Post duplicate.", "replicant");
            $status  = false;
         }

         if(!is_null($insert) && is_wp_error($insert)) {
            $message = $insert->get_error_message();
            $status  = false;
         }
      } else {
         $message = __("Invalid input.", "replicant");
         $status  = false;
      }

      // TODO: Fix sticky posts
      // if($status) {
      //    // Handle sticky posts
      //    if($metadata["is_sticky"]) {
      //       stick_post($post_id);
      //    }
      // }

      return rest_ensure_response( ["status" => $status, "message" => $message] );
   }

   /**
    * Determines if a post exists based on post hash
    * in posts metadata database.
    *
    * @param  string $post_hash SHA-256 hashed post title
    * @return boolean           Existence status
    */
   private function post_exists(string $post_hash): array {
      if(empty($post_hash)) {
         $status = false;
      }

      global $wpdb;
      $query = "SELECT * FROM $wpdb->postmeta WHERE `meta_key` = %s AND  meta_value = %s";
      $key   = "replicant_post_hash";
      $post  = $wpdb->get_row($wpdb->prepare($query, [$key, $post_hash]));
      $status = true;

      if(is_null($post) || empty($post)) {
         $status = false;
      }

      return [
         "status" => $status,
         "post" => $post
      ];
   }

   /**
    * Determine wheither the sender node hash is equal to the current node or not
    *
    * @param  string  $sender_node_hash Sender node hash
    * @return boolean                   Duplication status
    */
   private function is_duplicate_node(string $sender_node_hash): bool {
      if(empty($sender_node_hash)) {
         return false;
      }

      $current_node = new \Replicant\Node();

      if($sender_node_hash === $current_node->hash) {
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

      // Remove unnecessary fields
      unset($fields["metadata"]["_thumbnail_id"]);
      unset($fields["metadata"]["_edit_lock"]);
      unset($fields["metadata"]["_edit_last"]);
      unset($fields["metadata"]["_encloseme"]);
      unset($fields["metadata"]["_pingme"]);
      unset($fields["post"]["ID"]);

      $metadata = $fields["metadata"];
      $post = $fields["post"];
      $temp_data = $fields["temp_data"];

      if($this->is_valid_metadata($metadata)) {
         $post_hash = maybe_unserialize($metadata["replicant_post_hash"][0]);
         $node_hash = maybe_unserialize($metadata["replicant_node_hash"][0]);
         unset($metadata["replicant_node_hash"]);
         unset($metadata["replicant_post_hash"]);
         $metadata["replicant_node_hash"] = $node_hash;
         $metadata["replicant_post_hash"] = $post_hash;
      }

      $post["meta_input"] = $metadata;

      return [
         "id"        => $post_id,
         "post"      => $post,
         "metadata"  => $metadata,
         "is_update" => $is_update,
         "temp_data" => $temp_data
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
      $metadata = $fields["metadata"];

      if(!$this->is_valid_metadata($metadata)) {
         return false;
      }

      $target_node_hash = $fields["node"]["hash"];
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

   /**
    * Check if metadata is valid
    *
    * @param  array   $metadata Metadata array
    * @return boolean           Validation status
    */
   private function is_valid_metadata(array $metadata): bool {
      if(empty($metadata)) {
         return false;
      }

      // Validate node metadata
      if(empty(maybe_unserialize($metadata["replicant_node_hash"][0]))) {
         return false;
      }

      // Validate post metadata
      if(empty(maybe_unserialize($metadata["replicant_node_hash"][0]))) {
         return false;
      }

      return true;
   }


   /**
    * Downloads an image from the specified URL and attaches it to a post as a post thumbnail.
    *
    * @param string $file     The URL of the image to download.
    * @param int    $post_id  The post ID the post thumbnail is to be associated with.
    * @param string $desc     Optional. Description of the image.
    * @return string|WP_Error Attachment ID, WP_Error object otherwise.
   */
   private function generate_featured_image($file, $post_id, $desc = null) {
      if(!function_exists('download_url')) {
         require_once(ABSPATH . 'wp-admin/includes/media.php');
         require_once(ABSPATH . 'wp-admin/includes/file.php');
         require_once(ABSPATH . 'wp-admin/includes/image.php');
      }

      // Set variables for storage, fix file filename for query strings.
      preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
      if(!$matches) {
         return new \WP_Error('image_sideload_failed', __('Invalid image URL'));
      }

      $file_array = array();
      $file_array['name'] = basename($matches[0]);

      // Download file to temp location.
      $file_array['tmp_name'] = \download_url($file);

      // If error storing temporarily, return the error.
      if(is_wp_error( $file_array['tmp_name'])) {
         return $file_array['tmp_name'];
      }

      // Do the validation and storage stuff.
      $id = media_handle_sideload( $file_array, $post_id, $desc );

      // If error storing permanently, unlink.
      if ( is_wp_error( $id ) ) {
         @unlink($file_array['tmp_name']);
         return $id;
      }

      return set_post_thumbnail($post_id, $id);
   }

}
