<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

////////////////////////
// REST API Endpoints //
////////////////////////

// Setup REST API endpoints
add_action( 'rest_api_init', function() {
   $auth_controller = new Controllers\Auth();
   $auth_controller->register_routes();

   $info_controller = new Controllers\Info();
   $info_controller->register_routes();

   $publish_controller = new Controllers\Publish();
   $publish_controller->register_routes();
});
