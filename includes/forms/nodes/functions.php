<?php

namespace Replicant\Forms\Nodes;


/**
 * Functions for form submisison
 */
class Functions {

   /**
    * Insert a new node
    * @param  array      $args
    * @return int|false  Returns row ID or Error
    */
   public static function insert_node(array $args = []) {
      global $wpdb;

      $defaults = array(
        'id'   => null,
        'name' => '',
        'host' => '',
        'ssl'  => false,
        'port' => 80
      );

      $args         = wp_parse_args( $args, $defaults );
      $args["hash"] = \Replicant\Helper::generate_random_string(32);
      $table_name   = 'replicant_nodes';

      // Basic validations
      if(empty( $args['name'] )) {
        return new WP_Error( 'no-name', __( 'No Node Name provided.', 'replicant' ) );
      }
      if(empty( $args['host'] )) {
        return new WP_Error( 'no-address', __( 'No Address provided.', 'replicant' ) );
      }
      if(empty( $args['port'] )) {
        return new WP_Error( 'no-port', __( 'No Port provided.', 'replicant' ) );
      }

      // Remove row id to determine if new or update
      $row_id = (int) $args['id'];
      unset( $args['id'] );

      if(!$row_id) {
        // Insert a new Node
        if($wpdb->insert( $table_name, $args )) {
            \Replicant\Log::write(
               sprintf(__("%s successfully created", "replicant"), $args["name"]),
               $wpdb->insert_id,
               1 // Info
            );

            return $wpdb->insert_id;
        }
      } else {
         // Do update method here
         if($wpdb->update( $table_name, $args, ['id' => $row_id] )) {
            \Replicant\Log::write(
               sprintf(__("%s successfully updated", "replicant"), $args["name"]),
               $row_id,
               1 // Info
            );

            return $row_id;
         }
      }

      return false;
   }
}