<?php

namespace Replicant\Publishers;

use GuzzleHttp\Client;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Handle publishing posts
 */
class Post {

   private $route;

   public function __construct(array $body, object $target_node, bool $is_update) {
      $target_node_url = \Replicant\Helper::generate_url_from_node($target_node);
      $response        = $this->perform($body, $target_node_url, $is_update);
   }

   public function perform(array $body, array $target_node_url, bool $is_update) {
      $controller      = new \Replicant\Controllers\Publish();
      $route           = $controller->get_route();
      $target_node_url = $target_node_url["formed"] . $route . "/posts";

      $client = new \GuzzleHttp\Client();

      try {
         $request = $client->request('POST', $target_node_url, [
            'json' => $body
         ]);
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
