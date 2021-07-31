<?php

namespace Replicant\Database;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Generates pre defined specific SQL Queries
 */
class Schema {

   /**
    * Wordpress Database Object Instance
    * @var class
    */
   private static $wpdb;

   /**
    * Sets $wpdb class variable
    */
   public function __construct() {
      global $wpdb;
      self::$wpdb = $wpdb;
   }

   /**
    * Generates schema of "replicant_settings" table
    * @return string Create table SQL query
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

   /**
    * Generates schema of "replicant_nodes" table
    * 
    * @return string Create table SQL query
    */
   public static function nodes() {
      $table_name = \Replicant\Config::$TABLES["nodes"];

      $charset_collate = self::$wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
         `id` BIGINT unsigned NOT NULL AUTO_INCREMENT,
         `name` VARCHAR(255) NOT NULL,
         `host` VARCHAR(200) NOT NULL,
         `port` INT unsigned NOT NULL,
         `ssl` boolean DEFAULT false NOT NULL,
         `is_trusted` boolean DEFAULT false NOT NULL,
         `hash` TEXT NOT NULL,
         `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         UNIQUE KEY host (`host`),
         PRIMARY KEY  (`id`)
      ) $charset_collate";

      return $sql;
   }

   /**
    * Generates schema of "replicant_logs" table
    * 
    * @return string Create table SQL query
    */
   public static function logs() {
      $table_name = \Replicant\Config::$TABLES["logs"];

      $charset_collate = self::$wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
         `id` BIGINT unsigned NOT NULL AUTO_INCREMENT,
         `level` INT DEFAULT 0 NOT NULL,
         `message` TEXT NOT NULL,
         `node_id` BIGINT unsigned NOT NULL,
         `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         PRIMARY KEY  (`id`)
      ) $charset_collate";

      return $sql;  
   }
}
