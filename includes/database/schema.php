<?php

namespace Replicant\Database;


class Schema {

   private static $wpdb;

   public function __construct($wpdb) {
      self::$wpdb = $wpdb;
   }

   public static function settings($table_name) {
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
