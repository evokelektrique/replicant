<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

// Setup custom REST API endpoints
add_action( 'rest_api_init', function() {
   $auth_controller = new Controllers\Auth();
   $auth_controller->register_routes();

   $info_controller = new Controllers\Info();
   $info_controller->register_routes();
});
