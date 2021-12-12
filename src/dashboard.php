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

      // Admin area table list custom columns
      add_filter( 'manage_post_posts_columns', function($columns) {
         $icon_url = \Replicant\Config::$ROOT_URL . "../dist/images/icon_16x16.png";
         $columns['replicant'] = "<img src='$icon_url' />";
         return $columns;
      });

      add_action( 'manage_post_posts_custom_column', function($column, $post_id) {
         $metadata = get_post_meta( $post_id );
         $css_styles = "color:white;background:#2196f3;display:block;text-align:center;font-size:13px;padding:5px;border-radius:5px;box-shadow:0px 0px 3px 3px #aed6f7;";

         // Replicant column
         if ( 'replicant' === $column ) {
            if(isset($metadata["replicant_node_hash"]) && !empty($metadata["replicant_node_hash"])) {
               $current_node = new \Replicant\Node();
               $node_hash = $metadata["replicant_node_hash"][0];
               $node = Tables\Nodes\Functions::get_by("hash", $node_hash);
               if($current_node->hash === $node_hash) {
                  echo "<span style='$css_styles'>";
                  echo __("Sent", "replicant");
                  echo "<br>";
                  echo "<b>";
                  echo "</b>";
                  echo "<span>";
               } else {
                  echo "<span style='$css_styles'>";
                  echo __("Received from", "replicant");
                  echo "<br>";
                  echo "<b>";
                  echo $node->name;
                  echo "</b>";
                  echo "<span>";
               }
            }
         }
      }, 10, 2);
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
    * Load dashboard css/js files only for Dashboard/Replicant Pages
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
