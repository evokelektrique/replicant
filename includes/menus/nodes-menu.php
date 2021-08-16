<?php

namespace Replicant\Menus;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class NodesMenu {

   public function __construct() {
      add_submenu_page( 
         "replicant-settings",
         __( "Nodes", "replicant" ),
         __( "Nodes", "replicant" ),
         "manage_options",
         "replicant-nodes",
         [&$this, "handle"]
      );
   }  
   
   public static function handle() {
      $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
      $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

      switch ($action) {
         case "view":
            $template = \Replicant\Config::$ROOT_DIR . "views/nodes/single.php";
            break;

         case "edit":
            $template = \Replicant\Config::$ROOT_DIR . "views/nodes/edit.php";
            break;

         case "new":
            $template = \Replicant\Config::$ROOT_DIR . "views/nodes/new.php";
            break;

         case "request_trust":
            $node_id  = intval($_GET["id"]);
            $node     = \Replicant\Tables\Nodes\Functions::get($node_id);
            $request  = \Replicant\Controllers\Auth::request_trust($node);
            $response = json_decode($request, true);
            $template = \Replicant\Config::$ROOT_DIR . "views/nodes/trust.php";
            break;

         case "accept_trust":
            $target_node_hash       = $_GET["hash"];
            $target_node            = \Replicant\Tables\Nodes\Functions::get_by("hash", $target_node_hash);
            $accept_response        = \Replicant\Tables\Nodes\Functions::accept_trust($target_node);
            $accept_target_response = \Replicant\Controllers\Auth::accept_target_trust($target_node);
            var_dump($accept_target_response);
            $response = [
               'status'  => $accept_response["status"],
               'message' => $accept_response["message"]
            ];

            $template         = \Replicant\Config::$ROOT_DIR . "views/nodes/trust.php";            

            // $node_id  = intval($_GET["id"]);
            // $node     = \Replicant\Tables\Nodes\Functions::get($node_id);
            // $url      = \Replicant\Helper::generate_url_from_node($node);
            // $request  = \Replicant\Controllers\Info::request_get_node($url["full"]);
            // $response = json_decode($request);
            break;

         default:
            $template = \Replicant\Config::$ROOT_DIR . "views/nodes/list.php";
            break;
      }

      if ( file_exists( $template ) ) {
          require_once $template;
      }
   }
   
}
