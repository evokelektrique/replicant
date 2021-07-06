<?php

/**
 * Plugin Name:       Replicant
 * Plugin URI:        https://github.com/evokelektrique/replicant
 * Description:       This plugin replicates posts and content in your wordpress websites
 * Version:           0.1
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            EVOKE
 * Author URI:        https://github.com/evokelektrique/
 * License:           GPL 3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       replicant
 * Domain Path:       /languages
 */

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Replicant {

   /**
    * @access private
    * @static class $isntance Singleton class instance
    */
   private static $instance;

   /**
    * @access private
    * @static float $db_version Current database migration version
    */
   private static $db_version = 0.1;

   /**
    * @access public
    * @static string $version Current version of plugin
    */
   public static $version = "0.1";

   /**
    * Load Files And Initialize Classes
    */
   private function __construct() {
      // Load necessary files 
      $files = [
         "includes/*.php",
         "includes/admin/*.php",
         "includes/database/*.php"
      ];
      $this->load_files($files);

      // Initialize Classes In Order
      new Replicant\Config();

      // Initialize Database
      $option_db_version = get_option("replicant_db_version");
      if($option_db_version === false || $option_db_version < self::$db_version) {
         $this->init_db();
      }

      // $default = new Replicant\Database\Defaults();
      // $default::key();

      new Replicant\Admin\Panel();
   }

   /**
    * Initializes Database Tables
    */
   private function init_db() {
      // Require wordpress database files
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      update_option( "replicant_db_version", self::$db_version );

      // Initialize Schema Generator Class
      $schema_generator = new Replicant\Database\Schema();

      // Get Table Schemas And Insert Them Into an Array
      $schemas = [];
      $schemas[] = $schema_generator::settings($settings_table_name);

      // Iterate over schemas and create them
      foreach($schemas as &$schema) {
         dbDelta( $schema );
      }
   }

   /**
    * Retrieve class instance
    *
    * @return class
    */
   public static function get_instance() {
      if(!isset(self::$instance)) {
         self::$instance = new Replicant();
      }

      return self::$instance;
   }

   /**
    * Include Files Located In Plugin Folder
    *
    * @param array $files An array of file paths
    */
   private function load_files(array $files) {
      foreach($files as &$file) {
         foreach(glob(plugin_dir_path( __FILE__ ).$file) as $filename) {
            require_once($filename);
         }
      }
   }
}

$GLOBALS["replicant"] = Replicant::get_instance();
