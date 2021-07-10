<?php

namespace Replicant\Admin\Menus;

class MainMenu {

   public function __construct() {
      add_menu_page( 
         __("Replicant", "replicant"),
         __("Replicant", "replicant"), 
         "manage_options", 
         "replicant-settings", 
         [&$this, "handle"], 
         \Replicant\Config::$ROOT_URL . "../dist/images/icon_16x16.png", 
         6
      );
   }  
   
   public static function handle() {
      $file_path = \Replicant\Config::$ROOT_DIR . "admin/layout/panel.php";

      if(is_file($file_path)) {
         require_once $file_path;
      }
   }
}