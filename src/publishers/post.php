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

   public function __construct(array $body, object $target_node, bool $is_update, bool $is_delete) {
      $target_node_url = \Replicant\Helper::generate_url_from_node($target_node);
      $response        = $this->perform($body, $target_node_url, $is_update, $is_delete);
      error_log(print_r($response, true));
   }

   public function perform(array $body, array $target_node_url, bool $is_update, bool $is_delete) {
      $controller      = new \Replicant\Controllers\Publish();
      $route           = $controller->get_route();
      $target_node_url = $target_node_url["formed"] . $route;

      $client = new \GuzzleHttp\Client();

      $method = "POST";
      if($is_delete) {
         $method = "DELETE";
      }

      try {
         $request = $client->request($method, $target_node_url, [
            'json' => $body
         ]);
         // TODO: Debug, REMOVE IT
         error_log(print_r([$target_node_url, $method, (string) $request->getBody()], true));
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
