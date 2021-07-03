<?php
/**
 * Plugin Name:       Proxima
 * Plugin URI:        https://github.com/evokelektrique/proxima
 * Description:       Proxima unifies posts and content in your websites
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            EVOKE
 * Author URI:        https://github.com/evokelektrique/
 * License:           AGPL3
 * License URI:       https://www.gnu.org/licenses/agpl-3.0.txt
 * Text Domain:       proxima
 * Domain Path:       /languages
 */

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Proxima {

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

   private function __construct() {
      $files = [
         "hooks/*.php"
      ];

      $this->load_files($files);
   }

   /**
    * Retrieve class instance
    *
    * @return class
    */
   public static function get_instance() {

      // Check if $instance has been set
      if(!isset(self::$instance)) {

         // Create and set object to instance
         self::$instance = new Proxima();
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

$proxima = Proxima::get_instance();
$GLOBALS["proxima"] = $proxima;
