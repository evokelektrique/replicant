<?php

namespace Replicant\Database;


class Schema {

   public static $wpdb;

   public function __construct($wpdb) {
      self::$wpdb = $wpdb;
   }

   public static function settings() {
      $charset_collate = self::$wpdb->get_charset_collate();
      $table_name = "settings";
      $sql = "CREATE TABLE $table_name (
        -- id mediumint(9) NOT NULL AUTO_INCREMENT,
        -- time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        -- name tinytext NOT NULL,
        -- text text NOT NULL,
        -- url varchar(55) DEFAULT '' NOT NULL,
        -- PRIMARY KEY  (id)
      ) $charset_collate;";

      return $sql;
   }
}