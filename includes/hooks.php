<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

////////////////////////
// REST API Endpoints //
////////////////////////

// Setup custom REST API endpoints
add_action( 'rest_api_init', function() {
   $auth_controller = new Controllers\Auth();
   $auth_controller->register_routes();

   $info_controller = new Controllers\Info();
   $info_controller->register_routes();
});

///////////////
// Listeners //
///////////////

// Post listener
// add_action("save_post", __NAMESPACE__ . "\\Listeners\\Post::listen");

// function listen($post_id, $post, $update) {
//    error_log(print_r($post_id, true));
//    error_log(print_r($post, true));
//    error_log(print_r($update, true));
// }

// add_action( 'post_updated', function($post_ID, $post_after, $post_before) {
//    new \Replicant\Listeners\Post($post_ID, $post_after, $post_before);
// }, 10, 3 );
