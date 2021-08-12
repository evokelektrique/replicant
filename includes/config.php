<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Config {
   
   private static $TABLES_PREFIX = "replicant_";
   public static $ROOT_DIR;
   public static $ROOT_URL;
   public static $TABLES;

   public function __construct() {
      self::$ROOT_DIR = plugin_dir_path( __FILE__ );
      self::$ROOT_URL = plugins_url( "/", __FILE__ );

      $this->set_tables();
   }

   private function set_tables() {
      $tables = [
         "settings"        => self::$TABLES_PREFIX . "settings",
         "nodes"           => self::$TABLES_PREFIX . "nodes",
         "logs"            => self::$TABLES_PREFIX . "logs",
         "trusted_nodes"   => self::$TABLES_PREFIX . "trusted_nodes"
      ];
      
      self::$TABLES = $tables;
   }
}
