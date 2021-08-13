<?php

namespace Replicant\Forms\Nodes;

/**
 * Handle form submissions
 */
class Handler {

   /**
    * Hook 'em all
    */
   public function __construct() {
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

      // Fields
      $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
      $host = isset( $_POST['host'] ) ? sanitize_text_field( $_POST['host'] ) : '';
      $ssl  = isset( $_POST['ssl'] ) ? true : false;
      $port = isset( $_POST['port'] ) ? sanitize_text_field( intval($_POST['port']) ) : '';

      // some basic validation
      if(!$name) {
         $errors[] = __( 'Error: Node Name is required', 'replicant' );
      }

      if(!$host) {
         $errors[] = __( 'Error: Address is required', 'replicant' );
      }

      if(!$port) {
         $errors[] = __( 'Error: Port is required', 'replicant' );
      }

      // bail out if error found
      if($errors) {
         $first_error = reset( $errors );
         $redirect_to = add_query_arg( ['error' => $first_error], $page_url );
         wp_safe_redirect( $redirect_to );
         exit;
      }

      $fields = [
         'name' => $name,
         'host' => $host,
         'ssl'  => $ssl,
         'port' => $port
      ];

      // New or edit?
      if(!$field_id) {
         $insert_id = Functions::insert_node( $fields );
      } else {
         $fields['id'] = $field_id;
         $insert_id = Functions::insert_node( $fields );
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
         $success_message = __('Node successfully added.', 'replicant');
         $redirect_to = add_query_arg( [
               'status'  => 'success',
               'message' => $success_message
            ],
            $page_url
         );

         // Request trust at node creation
         $node           = \Replicant\Tables\Nodes\Functions::get($insert_id);
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
