<?php

namespace Replicant\Menus;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

class SettingsMenu {

   public function __construct() {
      add_submenu_page(
         "replicant-dashboard",
         __("Settings", "replicant"),
         __("Settings", "replicant"),
         "manage_options",
         "replicant-settings",
         [$this, "handle"]
      );
   }

   public static function handle() {
      $file_path = \Replicant\Config::$ROOT_DIR . "views/settings.php";

      if(is_file($file_path)) {
         require_once $file_path;
      }
   }
}
