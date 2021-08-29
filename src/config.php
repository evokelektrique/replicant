<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Handle configuration
 */
class Config {
   
   public static $TABLES_PREFIX = "replicant_";
   public static $ROOT_DIR;
   public static $ROOT_URL;
   public static $TABLES;

   public function __construct() {
      self::$ROOT_DIR = plugin_dir_path( __FILE__ );
      self::$ROOT_URL = plugins_url( "/", __FILE__ );

      $this->set_tables();
   }

   /**
    * Setup table names
    *
    * @global wpdb $wpdb WordPress database abstraction object.
    */
   private function set_tables() {
      global $wpdb;

      $tables = [
         "settings"        => $wpdb->prefix . self::$TABLES_PREFIX . "settings",
         "nodes"           => $wpdb->prefix . self::$TABLES_PREFIX . "nodes",
         "logs"            => $wpdb->prefix . self::$TABLES_PREFIX . "logs",
         "trusted_nodes"   => $wpdb->prefix . self::$TABLES_PREFIX . "trusted_nodes"
      ];
      
      self::$TABLES = $tables;
   }
   
}
