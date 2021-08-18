<?php

namespace Replicant\Controllers;

use GuzzleHttp\Client;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Information Controller
 */
class Info {

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
      $this->resource = "/info";
   }

   public function register_routes() {
      // Retrieve current Node information Endpoint
      register_rest_route(
         $this->namespace, 
         $this->resource . "/get_node", 
         [
            // Register the readable endpoint
            [
               "methods"  => "GET",
               "callback" => [&$this, "get_node"],
               "permission_callback" => "__return_true"
            ]
         ]
      );
   }

   ////////////////////////
   // Response Callbacks //
   ////////////////////////

   /**
    * Display current node information (Exclude sensetive data)
    * 
    * @return string JSON encoded of current node associated information
    */
   public function get_node() {
      $node = new \Replicant\Node();
      return rest_ensure_response( $node->get_json() );
   }

   ///////////////////
   // API Functions //
   ///////////////////

   /**
    * Send a HTTP request to fetch target Node information
    * 
    * @param  string $target_url Target server URl
    * @return string             HTTP Response
    */
   public static function request_get_node($target_url) {
      $target_url = $target_url . "/?rest_route=/replicant/v1/info/get_node";
      $client = new \GuzzleHttp\Client();

      try {
         $request = $client->request('GET', $target_url);
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
