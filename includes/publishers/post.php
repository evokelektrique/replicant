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

   public function __construct(array &$body, object $target_node) {
      // error_log(print_r([json_encode($body), $target_node, $target_node_url], true));

      $target_node_url = \Replicant\Helper::generate_url_from_node($target_node);
      $response        = $this->perform($body, $target_node_url);
   }

   public function perform(array &$body, array $target_node_url) {
      $controller      = new \Replicant\Controllers\Publish();
      $route           = $controller->get_route();
      $target_node_url = $target_node_url["formed"] . $route . "/posts";

      $client = new \GuzzleHttp\Client();

      error_log($target_node_url);

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
