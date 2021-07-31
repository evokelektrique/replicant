<?php

namespace Replicant\Controllers;

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

   public function accept_trust($request) {
      $data = ["trust" => true];
      return rest_ensure_response($data);
   }

}
