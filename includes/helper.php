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
   private static function generate_random_string($length) {
      // Solution for PHP_VERSION >= 7
      if(version_compare(PHP_VERSION, "7.0.0") >= 0) {
         $bytes = random_bytes($length);
         return bin2hex($bytes);
      }

      // Solution for PHP_VERSION >= 5  
      $bytes = openssl_random_pseudo_bytes($length);
      return bin2hex($bytes);
   }
}