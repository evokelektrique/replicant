<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

class Dashboard {

   /**
    * Attach dashboard functions to hooks
    */
   public function __construct() {
      add_action( 'admin_enqueue_scripts', [$this, 'admin_assets'] );
      add_action( 'admin_menu', [$this, "add_menus"] );
   }

   /**
    * Load dashboard menus
    */
   public static function add_menus() {
      new \Replicant\Menus\MainMenu();
      new \Replicant\Menus\NodesMenu();
      new \Replicant\Menus\SettingsMenu();
   }

   /**
    * Load dashboard css/js files only for Dashboard/Admin area
    */
   public static function admin_assets() {
      if ( isset( $_GET["page"] ) && ! empty( $_GET["page"] ) && strpos($_GET["page"], "replicant-") !== false  ) {
         wp_register_script(
            "replicant_js",
            \Replicant\Config::$ROOT_URL . "../dist/scripts.js"
         );
         wp_register_style(
            "replicant_css",
            \Replicant\Config::$ROOT_URL . "../dist/styles.css"
         );
         wp_enqueue_style( "replicant_css" );
         wp_enqueue_script( "replicant_js" );
      }
   }
}
