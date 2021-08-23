<?php

namespace Replicant\Controllers;

use GuzzleHttp\Client;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Authentication Controller
 */
class Auth {

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
      $this->resource = "/auth";
   }

   public function register_routes() {
      // Trust Request Endpoint
      register_rest_route(
         $this->namespace, 
         $this->resource . "/trust", 
         [
            // Register the readable endpoint
            [
               "methods"  => "POST",
               "callback" => [&$this, "trust"],
               "permission_callback" => "__return_true"
            ]
         ]
      );

      // Trust Acceptance Endpoint
      register_rest_route(
         $this->namespace, 
         $this->resource . "/accept_trust",
         [
            // Register the readable endpoint
            [
               "methods"  => "POST",
               "callback" => [&$this, "accept_trust"],
               "permission_callback" => "__return_true"
            ]
         ]
      );

      // Current Node Trust Acceptance Endpoint
      register_rest_route(
         $this->namespace, 
         $this->resource . "/accept_current",
         [
            // Register the readable endpoint
            [
               "methods"  => "POST",
               "callback" => [&$this, "current_node_accept_trust"],
               "permission_callback" => "__return_true"
            ]
         ]
      );
   }

   ////////////////////////
   // Response Callbacks //
   ////////////////////////

   /**
    * Trust request callback
    * 
    * @param  object $request Incoming request information
    * @return string          JSON Object containing specefic "status" and "message"
    */
   public function trust($request) {
      $fields    = $request->get_json_params();
      $insert_id = \Replicant\Forms\Nodes\Functions::insert_node($fields, true);

      $status  = true;
      $message = __("Node successfully inserted", "replicant");

      if(is_wp_error($insert_id)) {
         $message = $insert_id->get_error_message();
         $status  = false;
      }

      $data = [
         "status"  => $status,
         "message" => $message
      ];

      return rest_ensure_response($data);
   }

   /**
    * Trust acceptance callback
    * 
    * @param  object $request Incoming request information
    * @return string          JSON Object containing specefic "status" and "message"
    */
   public function accept_trust($request) {
      $fields   = $request->get_json_params();
      $response = null;
      $data     = [];

      if(isset($fields["node_id"])) {
         $response = \Replicant\Tables\Nodes\Functions::accept_trust($fields["node_id"]);
         $data     = $response;
      }

      if(!$response) {
         $data["status"]  = false;
         $data["message"] = __("Something went wrong.", "replicant");
      }

      return rest_ensure_response( $data );
   }

   /**
    * Current Node trust acceptance callback
    * 
    * NOTE: This function will change the value of "is_trusted" 
    *       of target Node in current Node database so both Nodes
    *       would be identical
    * 
    * @param  object $request Incoming request information
    * @return string          JSON Object containing specefic "status" and "message"
    */
   public function current_node_accept_trust($request) {
      global $wpdb;

      $fields   = $request->get_json_params();
      $response = null;
      $data     = [];

      if(empty($fields) || !isset($fields["hash"])) {
         return rest_ensure_response([
            "status"  => false, 
            "message" => __("Invalid request", "replicant")
         ]);
      }

      $table_name = \Replicant\Config::$TABLES["nodes"];

      $args = [
         "is_trusted" => true
      ];

      $data['status']  = true;
      $data['message'] = __("Successfully accepted trust of the Node", "replicant");

      // Do update
      $update = $wpdb->update( 
         $table_name,
         $args,
         ['hash' => htmlspecialchars($fields["hash"])]
      );

      if(!$update) {
         $data['status']  = false;
         $data['message'] = __("Something went wrong in accepting trust, Please try again.", "replicant");
      }

      return rest_ensure_response($data);
   }

   ///////////////////
   // API Functions //
   ///////////////////

   /**
    * Send a HTTP request to node URL
    * 
    * @param  Node $target_node Need a Node to generate an URL 
    *                           And extract information from it
    * @return void
    */
   public static function request_trust($target_node) {
      // Generate formed URL and parsed URI 
      // for current and target nodes
      $current_node     = new \Replicant\Node();
      $current_node_url = \Replicant\Helper::generate_url_from_node($current_node);

      $target_node_url = \Replicant\Helper::generate_url_from_node($target_node);
      $target_node_url = $target_node_url["formed"] . "/?rest_route=/replicant/v1/auth/trust";

      // Create a HTTP client and send current Node 
      // Information via POST method to the target Node
      $client = new \GuzzleHttp\Client();

      $body = [
         "hash" => $current_node->hash,
         "name" => $current_node->name,
         "host" => $current_node_url["formed"],
         "port" => $current_node->port,
         "ssl"  => $current_node->ssl
      ];

      try {
         $request = $client->request('POST', $target_node_url, [
            'json' => $body
         ]);
         return (string) $request->getBody();
      } catch(\GuzzleHttp\Exception\ServerException $e) {
         $error_message = $response->getBody()->getContents();
         return new \WP_Error('request-server-error',
            __( 
               "Couldn't establish a connection to server or an error happened on the target server.",
               'replicant' 
            ) 
         );
      }
   }


   /**
    * Send a HTTP request to the $target_node's URL and 
    * Tell it that current Node has accepted you
    *
    * @param object $target_node Given Node to send information to it
    * @return string JSON repsonse of target node
    */
   public static function accept_target_trust(object $target_node) {
      $current_node    = new \Replicant\Node();

      $target_node_url = \Replicant\Helper::generate_url_from_node($target_node);
      $target_node_url = $target_node_url["formed"] . "/?rest_route=/replicant/v1/auth/accept_current";
      // Create a HTTP client and send current Node 
      // Information(Hash) via POST method to the target Node
      $client = new \GuzzleHttp\Client();

      $body = [
         "hash" => $current_node->hash
      ];

      try {
         $request = $client->request('POST', $target_node_url, [
            'json' => $body
         ]);
         var_export((string) $request->getBody());
         return (string) $request->getBody();
      } catch(\GuzzleHttp\Exception\ServerException $e) {
         return new \WP_Error('request-server-error',
            __( 
               "Couldn't establish a connection to the server or an error happened on the target server.",
               'replicant' 
            ) 
         );
      }
   }
   
}
