<?php

namespace Replicant\Database;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

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
    * Set $wpdb class variable
    * 
    * @access public
    * @static
    */
   public function __construct() {
      global $wpdb;
      self::$wpdb = $wpdb;
   }

   /**
    * Insert Default Authorization value
    *
    * @return int|string could be an last insert_id or could be a value of row
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
      // (Will be needed in one day)
      $default_value = \Replicant\Helper::generate_random_string(32);
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
}