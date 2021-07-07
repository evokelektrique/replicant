<?php

namespace Replicant\Admin;

class Panel {
   
   public function __construct() {
      add_action( 'admin_enqueue_scripts', [&$this, 'admin_assets']);
      add_action( 'admin_menu', [&$this, "add_menu"] );
   }

   public static function add_menu() {
      add_menu_page( 
         __("Replicant Modules", "replicant"),
         __("Replicant", "replicant"), 
         "manage_options", 
         "replicant-settings", 
         [$this, "handle_page_menu"], 
         "dashicon-tagcloud", 
         6
      );
   }

   public static function admin_assets() {
      if ( isset( $_GET["page"] ) && ! empty( $_GET["page"] ) && "replicant-settings" === $_GET["page"] ) {
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

   public static function handle_page_menu() {
      $file_path = \Replicant\Config::$ROOT_DIR . "admin/layout/panel.php";

      if(is_file($file_path)) {
         require_once $file_path;
      }
   }
}
