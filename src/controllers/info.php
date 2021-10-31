<?php

namespace Replicant\Controllers;

use GuzzleHttp\Client;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * Information Controller
 */
class Info {

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
               "callback" => [$this, "get_node"],
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
      error_log(print_r($target_url, true));
      $client = new \GuzzleHttp\Client();

      try {
         $request = $client->request('GET', $target_url);
         return (string) $request->getBody();
      } catch(\Exception $e) {
         return new \WP_Error('request-server-error',
            __(
               "Couldn't establish a connection to the server or an error happened on the target server.",
               'replicant'
            )
         );
      }
   }

}
