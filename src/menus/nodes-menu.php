<?php

namespace Replicant\Menus;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

class NodesMenu {

   public function __construct() {
      // Total Nodes awaiting to trust
      $nodes_trust_await_count = \Replicant\Tables\Nodes\Functions::get_await_count();

      add_submenu_page(
         "replicant-dashboard",
         __( "Nodes", "replicant" ),
         $nodes_trust_await_count ?  sprintf(__("Nodes", "replicant") . '<span class="awaiting-mod">%d</span>', $nodes_trust_await_count) : __('Nodes', 'replicant'),
         "manage_options",
         "replicant-nodes",
         [$this, "handle"]
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
            // Fetch Node
            $target_node_hash = $_GET["hash"];
            $target_node      = \Replicant\Tables\Nodes\Functions::get_by("hash", $target_node_hash);

            // Accept target Node trust on current database
            // and target Node database.
            $accept_response        = \Replicant\Tables\Nodes\Functions::accept_trust($target_node);
            $accept_target_response = \Replicant\Controllers\Auth::accept_target_trust($target_node);

            $response = [
               'status'  => $accept_response["status"],
               'message' => $accept_response["message"]
            ];

            $template = \Replicant\Config::$ROOT_DIR . "views/nodes/trust.php";
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
