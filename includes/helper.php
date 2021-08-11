<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Helper {

   /**
    * Generates an Unique Random Byte String
    * 
    * @access private
    * @var int $length Length of the unique generated string
    */
   public static function generate_random_string($length) {
      // Solution for PHP_VERSION >= 7
      if(version_compare(PHP_VERSION, "7.0.0") >= 0) {
         $bytes = random_bytes($length);
         return bin2hex($bytes);
      }

      // Solution for PHP_VERSION >= 5  
      $bytes = openssl_random_pseudo_bytes($length);
      return bin2hex($bytes);
   }

   /**
    * Print custom notice
    * 
    * @param  string $status  status given from $_GET arguments
    * @param  string $message message given from $_GET arguments
    * @return string          Custom html notice output with $status class
    */
   public static function print_notice($status, $message) {
      if($message):
      ?>

      <div class="replicant-notice replicant-notice-<?= $status ?>">
         <?= htmlspecialchars($message) ?>
      </div>

      <?php
      endif;
   }

   /**
    * Print custom badge
    * 
    * @param  boolean $status Current status
    * @return string          Custom HTML badge
    */
   public static function print_badge($status) {
      ?>
      <span class="replicant-badge replicant-badge-<?= $status ? "success" : "error" ?>">
         <?= $status ? __( 'Yes', 'replicant' ) : __( 'No', 'replicant' ); ?>
      </span>
      <?php
   }

   /**
    * Generate an URL from given Node ID
    * 
    * @param  int    $node_id
    * @return array          A list of Raw and Parsed version of generated URL
    */
   public static function generate_url_from_node(int $node_id) {
      // Fetch node
      $node = \Replicant\Tables\Nodes\Functions::get($node_id);

      // Create an empty list and append the node variables into it
      // And create different versions of URL
      $url           = [];
      $url["scheme"] = $node->ssl === 0 ? "http://" : "https://";
      $url["host"]   = $node->host;

      // Merge $url array
      $url_string = implode('', $url);

      // We separate parsed results 
      $scheme  = parse_url(trim($url_string), PHP_URL_SCHEME);
      $host    = parse_url(trim($url_string), PHP_URL_HOST);
      $path    = parse_url(trim($url_string), PHP_URL_PATH);

      // Final URl formation
      $formed_url = $url["scheme"] . $host . ":" . $node->port . $path;
      
      return [
         "formed" => $formed_url,
         "parsed" => [
            "scheme" => $scheme, 
            "host"   => $host, 
            "path"   => $path
         ]
      ];
   }
}