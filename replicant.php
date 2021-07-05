<?php
/**
 * Plugin Name:       Replicant
 * Plugin URI:        https://github.com/evokelektrique/replicant
 * Description:       This plugin replicates posts and content in your wordpress websites
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            EVOKE
 * Author URI:        https://github.com/evokelektrique/
 * License:           AGPL3
 * License URI:       https://www.gnu.org/licenses/agpl-3.0.txt
 * Text Domain:       replicant
 * Domain Path:       /languages
 */

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Replicant {

   /**
    * @access public
    * @static string $version Current version of plugin
    */
   public static $version = "0.1.0";

   /**
    * @access private
    * @static class $isntance Singleton class instance
    */
   private static $instance;

   /**
    * Load Files And Initialize Classes
    */
   private function __construct() {
      $files = [
         "includes/*.php",
         "includes/admin/*.php",
         "includes/database/*.php"
      ];
      $this->load_files($files);
      $this->init_db(self::$version);      

      new Replicant\Config();
      new Replicant\Admin\Panel();
   }

   /**
    * Initializes Database Tables
    *
    * @param string $version Current Plugin Version
    */
   private function init_db($version) {
      // Require wordpress database files
      global $wpdb;
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

      // Initialize Schema Class
      $schema = new Replicant\Database\Schema($wpdb);

      // Get Table Schemas 
      // And Create Them
      $settings_table = $schema::settings();
      var_dump($settings_table);
      var_dump(get_option("replicant_db_version"));
      // dbDelta( $sql );
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
