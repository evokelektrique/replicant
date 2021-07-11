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
        'host' => ''
      );

      $args       = wp_parse_args( $args, $defaults );
      $table_name = 'replicant_nodes';

      // Basic validations
      if(empty( $args['name'] )) {
        return new WP_Error( 'no-name', __( 'No Node Name provided.', 'replicant' ) );
      }
      if(empty( $args['host'] )) {
        return new WP_Error( 'no-host', __( 'No Host Name provided.', 'replicant' ) );
      }

      // Remove row id to determine if new or update
      $row_id = (int) $args['id'];
      unset( $args['id'] );

      if(!$row_id) {
        // Insert a new Node
        if($wpdb->insert( $table_name, $args )) {
            return $wpdb->insert_id;
        }
      } else {
         // Do update method here
         if($wpdb->update( $table_name, $args, ['id' => $row_id] )) {
            return $row_id;
         }
      }

      return false;
   }
}