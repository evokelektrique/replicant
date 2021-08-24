<?php

/**
 * Plugin Name:       Replicant
 * Plugin URI:        https://github.com/evokelektrique/replicant
 * Description:       This plugin replicates posts and content in your wordpress websites
 * Version:           0.2
 * Requires at least: 5.7.0
 * Author:            EVOKE
 * Author URI:        https://github.com/evokelektrique/
 * License:           GPL 3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       replicant
 * Domain Path:       /languages
 */

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

require_once __DIR__ . "/vendor/autoload.php";

class Replicant {

   /**
    * @access public
    * @static string $version Current version of plugin
    */
   public static $version = 0.2;

   /**
    * @access public
    * @static class $default_db Database Default Values Class Instance
    */
   public static $default_db;

   /**
    * @access private
    * @static class $isntance Singleton class instance
    */
   private static $instance;

   /**
    * Current database migration version 
    * And will be saved in 'wp_options' table
    * 
    * @static float $db_version Migration version
    */
   private static $db_version = 0.6;

   /**
    * Load Files And Initialize Classes In Order
    */
   private function __construct() {
      // Load necessary files 
      $files = [                       // TODO: Write a good documentation for this section.
         "includes/*.php",             
         "includes/admin/*.php",       // Dashboard related files such as its views and functions
         "includes/database/*.php",
         "includes/tables/nodes/*.php",
         "includes/forms/nodes/*.php",
         "includes/controllers/*.php",
         "includes/listeners/*.php",
         "includes/publishers/*.php"   // No need to initialize publishers
                                       // because they don't include hooks
      ];
      $this->load_files($files);

      // Initialize Classes In Order
      new Replicant\Config();

      // Initialize Database
      // 
      // Check if current database version stored on `wp_options` 
      // table is set or lower than the plugin's version($db_version) 
      // and if so, then upgrade current database.
      $option_db_version = get_option("replicant_db_version");
      if($option_db_version === false || $option_db_version < self::$db_version) {
         $this->init_db();
      }

      // Insert Default Values Into Database
      self::$default_db = new Replicant\Database\Defaults();
      self::$default_db::authorization();
      self::$default_db::current_node_hash();

      // Display menus on dashboard and other pages
      new Replicant\Dashboard();

      // Initialize Forms
      new Replicant\Forms\Nodes\Handler();

      // Initialize Listeners
      new Replicant\Listeners\Post();
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
      $schemas[] = $schema_generator::settings();
      $schemas[] = $schema_generator::nodes();
      $schemas[] = $schema_generator::logs();

      // Iterate over schemas and create them
      foreach($schemas as $schema) {
         dbDelta( $schema );
      }
   }

   /**
    * Retrieve singleton class instance
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
    * @param array $files List of files paths
    */
   private function load_files(array $files) {
      foreach($files as $file) {
         foreach(glob(plugin_dir_path(__FILE__) . $file) as $filename) {
            require_once($filename);
         }
      }
   }
}

$GLOBALS["replicant"] = Replicant::get_instance();
