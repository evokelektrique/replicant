<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Helper {

   /**
    * Generates an Unique Random Byte String
    * 
    * @var    int    $length Length of the unique generated string
    * @return string         Random generated value dependant to the $length
    */
   public static function generate_random_string(int $length = 32) {
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
    * @param  string $status  Status given from $_GET arguments ("error" or "success")
    * @param  string $message Message given from $_GET arguments
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
    * @param  array $node
    * @return array       A list of Raw and Parsed version of generated URL
    */
   public static function generate_url_from_node($node) {
      // Create an empty list and append the node variables into it
      // And create different versions of URL
      $url           = [];
      $url["scheme"] = intval($node->ssl) === 0 ? "http://" : "https://";
      $url["host"]   = $node->host;
      $url["path"]   = isset($node->path) ? $node->path : "";

      // Merge $url array
      $url_string = implode('', $url);

      // We separate parsed results 
      $scheme  = parse_url(trim($url_string), PHP_URL_SCHEME);
      $host    = parse_url(trim($url_string), PHP_URL_HOST);
      $path    = parse_url(trim($url_string), PHP_URL_PATH);

      // Final URl formation
      $formed_url = $host . ":" . $node->port . $path;
      
      return [
         "full"   => $url["scheme"] . $formed_url,
         "formed" => $formed_url,
         "parsed" => [
            "scheme" => $url["scheme"], 
            "host"   => $host,
            "port"   => $node->port,
            "path"   => $path
         ]
      ];
   }

   /**
    * Check if WooCommerce is activated in theme
    * 
    * @return bool Activation status
    */
   public static function is_woocommerce_active() {
      return class_exists("WooCommerce");
   }

}
