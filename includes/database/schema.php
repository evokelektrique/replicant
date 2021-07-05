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
        id int NOT NULL AUTO_INCREMENT,
        key varchar(255) NOT NULL,
        value text NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      return $sql;
   }
}