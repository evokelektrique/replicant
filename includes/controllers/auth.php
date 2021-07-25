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
    * Namepsace version number
    * 
    * @var string
    */
   public $version

   /**
    * Namespace resource name
    * 
    * @var string
    */
   public $resource;


   public function __construct() {
      // Generates "replicant/v1/auth"
      $this->namespace = "replicant";
      $this->version = "v1";
      $this->resource = "auth";
   }

   public function register_routes() {
      // ...
   }
}
