<?php

namespace Replicant\Database;

/**
 * Insert Default Values Into Database
 */
class Defaults {

   /**
    * @access private
    * @static class $wpdb Wordpress Database Object Instance
    */
   private static $wpdb;

   /**
    * @access public
    * @static
    * 
    * Set $wpdb class variable
    */
   public function __construct() {
      global $wpdb;
      self::$wpdb = $wpdb;
   }

   /**
    * @return int|string could be an last insert_id or could be a value of row
    *
    * Insert Default Authorization value
    */
   public static function authorization() {
      $table_name = \Replicant\Config::$TABLES["settings"];
      $option = "authorization";
      
      $find_query = self::$wpdb->get_row(self::$wpdb->prepare(
         "SELECT * FROM $table_name WHERE `option` = %s",
         $option
      ));
      
      // If already exists, Don't continue
      if(!empty($find_query)) {
         return $find_query;
      }

      // Insert Default Value
      $default_value = self::generate_random_string();
      self::$wpdb->insert(
         $table_name,
         // Columns
         [
            "option" => $option,
            "value" => $default_value
         ],
         // Formats
         ["%s", "%s"]
      );

      return self::$wpdb->insert_id;
   }

   /**
    * @access private
    * @static
    * 
    * Generates an Unique Random Byte String
    */
   private static function generate_random_string() {
      // Solution for PHP_VERSION >= 7
      if(version_compare(PHP_VERSION, "7.0.0") >= 0) {
         $bytes = random_bytes(20);
         return bin2hex($bytes);
      }

      // Solution for PHP_VERSION >= 5  
      return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 40);
   }

}