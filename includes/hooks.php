<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

// Add Authentication Controller REST API Endpoints
add_action( 'rest_api_init', function() {
   $auth_controller = new Controllers\Auth();
   $auth_controller->register_routes();
});
