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
    * Custom notice output 
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
   public static function badge_html($status) {
      ?>
      <span class="replicant-badge replicant-badge-<?= $status ? "success" : "error" ?>">
         <?= $status ? __( 'Yes', 'replicant' ) : __( 'No', 'replicant' ); ?>
      </span>
      <?php
   }
}