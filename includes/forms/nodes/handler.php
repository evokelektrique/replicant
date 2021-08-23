<?php

namespace Replicant\Forms\Nodes;

/**
 * Handle form submissions
 */
class Handler {


   /**
    * Form update status
    * 
    * @var boolean
    */
   private $is_update;

   /**
    * Hook 'em all
    */
   public function __construct() {
      $this->is_update = false;
      add_action( 'admin_init', [&$this, 'handle_form'] );
   }

   /**
    * Handle the node new and edit form
    *
    * @return void
    */
   public function handle_form() {
      if(!isset( $_POST['submit_node'] )) {
         return;
      }

      if(!wp_verify_nonce( $_POST['_wpnonce'], '' )) {
        die( __( 'Are you cheating?', 'replicant' ) );
      }

      if(!current_user_can( 'read' )) {
        wp_die( __( 'Permission Denied!', 'replicant' ) );
      }

      $errors   = [];
      $page_url = admin_url( 'admin.php?page=replicant-nodes' );
      $field_id = isset( $_POST['field_id'] ) ? intval( $_POST['field_id'] ) : 0;

      ////////////
      // Fields //
      ////////////
      $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
      $host = isset( $_POST['host'] ) ? sanitize_text_field( $_POST['host'] ) : '';
      $ssl  = isset( $_POST['ssl'] ) ? true : false;
      $port = isset( $_POST['port'] ) ? sanitize_text_field( intval($_POST['port']) ) : '';

      /////////////////
      // Validations //
      /////////////////
      if(!$name) {
         $errors[] = __( 'Error: Node Name is required', 'replicant' );
      }

      if(!$host) {
         $errors[] = __( 'Error: Address is required', 'replicant' );
      }

      if(!$port) {
         $errors[] = __( 'Error: Port is required', 'replicant' );
      }

      // Bail out if error found
      if($errors) {
         $first_error = reset( $errors );
         $redirect_to = add_query_arg( ['error' => $first_error], $page_url );
         wp_safe_redirect( $redirect_to );
         exit;
      }

      // Arguments to be inserted into database
      $fields = [
         'host' => $host,
         'ssl'  => $ssl,
         'port' => $port
      ];

      /////////////////////////////////////
      // Receive target node information //
      /////////////////////////////////////
      $url           = [];
      $url["scheme"] = intval($fields["ssl"]) === 0 ? "http://" : "https://";
      $url["host"]   = $fields["host"];

      // Merge $url array
      $url_string = implode('', $url);

      // We separate parsed results 
      $scheme  = parse_url(trim($url_string), PHP_URL_SCHEME);
      $host    = parse_url(trim($url_string), PHP_URL_HOST);
      $path    = parse_url(trim($url_string), PHP_URL_PATH);

      // Final URl formation
      $formed_target_url = $url["scheme"] . $host . ":" . $fields["port"] . $path;

      // Send request to target URL
      $request  = \Replicant\Controllers\Info::request_get_node($formed_target_url);

      if(is_wp_error( $request )) {
         $error_message = $request->get_error_message();
         $redirect_to   = add_query_arg([
               'status'  => 'error',
               'message' => $error_message
            ], 
            $page_url
         );
         wp_safe_redirect( $redirect_to );
         die();
      }

      // Decode request message
      $response = json_decode($request);

      // Assign the related variables into $fields
      $fields["hash"] = htmlspecialchars($response->hash);
      $fields["name"] = htmlspecialchars($response->name);

      //////////////////////
      // Insert or Update //
      //////////////////////
      if(!$field_id) {
         $insert_id = Functions::insert_node( $fields );
      } else {
         $fields['id']    = $field_id;
         $fields['name']  = $name;
         $insert_id       = Functions::insert_node( $fields );
         $this->is_update = true;
      }

      if(is_wp_error( $insert_id )) {
         $error_message = $insert_id->get_error_message();
         $redirect_to   = add_query_arg([
               'status'  => 'error',
               'message' => $error_message
            ], 
            $page_url
         );
      } else {
         $message_method  = $this->is_update ? "updated" : "added";
         $success_message = sprintf(__("Node successfully %s.", "replicant"), $message_method);
         $redirect_to = add_query_arg( [
               'status'  => 'success',
               'message' => $success_message
            ],
            $page_url
         );

         // Request trust at node creation
         $node_id        = $this->is_update ? $fields["id"] : $insert_id;
         $node           = \Replicant\Tables\Nodes\Functions::get($node_id);
         $trust_response = \Replicant\Controllers\Auth::request_trust($node);
         
         if(is_wp_error( $trust_response )) {
            $error_message = $trust_response->get_error_message();
            $redirect_to = add_query_arg( [
                  'status'  => 'error',
                  'message' => $error_message
               ],
               $page_url
            );
         }
      }

      wp_safe_redirect( $redirect_to );
      exit;
   }
}
