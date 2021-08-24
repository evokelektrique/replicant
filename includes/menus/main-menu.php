<?php

namespace Replicant\Menus;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class MainMenu {

   public function __construct() {
      add_menu_page( 
         __("Replicant", "replicant"),
         __("Replicant", "replicant"), 
         "manage_options", 
         "replicant-settings", 
         [$this, "handle"], 
         \Replicant\Config::$ROOT_URL . "../dist/images/icon_16x16.png", 
         6
      );
   }  
   
   public static function handle() {
      $file_path = \Replicant\Config::$ROOT_DIR . "views/dashboard.php";

      if(is_file($file_path)) {
         require_once $file_path;
      }
   }
}