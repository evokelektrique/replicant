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
               "callback"            => [&$this, "create_post"],
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

      unset($fields["post"]["ID"]);
      unset($fields["meta_data"]["_edit_lock"]);
      unset($fields["meta_data"]["_pingme"]);
      unset($fields["meta_data"]["_encloseme"]);

      $post               = $fields["post"];
      $post["meta_input"] = $fields["meta_data"];

      error_log(print_r($post, true));

      $insert_id = wp_insert_post($post, true);

      error_log(print_r($insert_id, true));

      return rest_ensure_response( ["status" => true, "message" => "Test"] );
   }

}
