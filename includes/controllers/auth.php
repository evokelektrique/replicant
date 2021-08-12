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
      // Request Trust Endpoint
      register_rest_route(
         $this->namespace, 
         $this->resource . "/accept_trust", 
         [
            // Register the readable endpoint
            [
               "methods" => "POST",
               "callback" => [&$this, "accept_trust"]
            ]
         ]
      );
   }

   /**
    * Trust acceptance listener endpoint
    * 
    * @param  object $request Incoming request information
    * @return string          JSON Object containing specefic "status" and "message"
    */
   public function accept_trust($request) {
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
    * Send a HTTP request to node URL
    * 
    * @param  Node $node Need a Node to generate an URL 
    *                    And extract information from it
    * @return void
    */
   public static function request_trust($node) {
      // Fetch node and generate formed url and uri
      $url  = \Replicant\Helper::generate_url_from_node($node);
      $url  = $url["formed"] . "/?rest_route=/replicant/v1/auth/accept_trust";

      // Create a HTTP client and send current Node 
      // Information via POST method to the target Node
      $client = new \GuzzleHttp\Client();

      $body = [
         "hash" => $node->hash,
         "name" => $node->name,
         "host" => $node->host,
         "port" => $node->port,
         "ssl"  => $node->ssl
      ];

      try {
         $request = $client->request('POST', $url, [
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
}
