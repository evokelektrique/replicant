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
      // TODO: Debug, REMOVE IT
      error_log(print_r([$is_update, $target_node_url["full"], $response], true));
      return $response;
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
         return (string) $request->getBody();
      } catch(\GuzzleHttp\Exception\ServerException $e) {
         return new \WP_Error('request-server-error',
            __(
               "Couldn't establish a connection to the server or an error happened on the target server.",
               'replicant'
            )
         );
      } catch(\GuzzleHttp\Exception\ClientException $e) {
         $error = $e->getResponse()->getBody()->getContents();
         if(\Replicant\Helper::is_json($error)) {
            $error_array   = json_decode($error, true);
            $error_code    = $error_array["code"];
            $error_message = $error_array["message"];
            return new \WP_Error($error_code, $error_message);
         }

         return new \WP_Error('request-client-error',
            __(
               "Couldn't establish a connection to the server or an error happened on the target server.",
               'replicant'
            )
         );
      }
   }

}
