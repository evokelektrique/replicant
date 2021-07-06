<?php

namespace Replicant\Database;

/**
 * Generate Specific SQL Queries
 */
class Schema {

   /**
    * @access private
    * @static class $wpdb Wordpress Database Object Instance
    */
   private static $wpdb;

   /**
    * Set $wpdb class variable
    */
   public function __construct() {
      global $wpdb;
      self::$wpdb = $wpdb;
   }

   /**
    * @access public
    * @static
    * @param $table_name="settings" string Settings Table Name
    * @return $sql string
    */
   public static function settings() {
      $table_name = \Replicant\Config::$TABLES["settings"];

      $charset_collate = self::$wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
         `id` INT unsigned NOT NULL AUTO_INCREMENT,
         `option` VARCHAR(255) NOT NULL,
         `value` TEXT NOT NULL,
         PRIMARY KEY  (`id`)
      ) $charset_collate;";
      return $sql;
   }
}
