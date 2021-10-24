<?php

namespace Replicant\Forms\Nodes;


/**
 * Functions for form submisison
 */
class Functions {

   /**
    * Insert a new node
    *
    * @param   array      $args
    * @param   bool       $is_trust_request  Determine if this is requesting for trust
    * @return  int|false                     Returns row ID or Error
    */
   public static function insert_node(array $args = [], bool $is_trust_request = false) {
      global $wpdb;

      $defaults = array(
        'id'   => null,
        'name' => '',
        'host' => '',
        'ssl'  => false,
        'port' => 80
      );

      $args       = wp_parse_args( $args, $defaults );
      $table_name = \Replicant\Config::$TABLES["nodes"];

      // Check if hash exists in parameters because hashes
      // should be identical When a node is requesting trust
      if(!isset($args["hash"])) {
         $args["hash"] = \Replicant\Helper::generate_random_string();
      }

      if($is_trust_request) {
         $args["is_trust_request"] = true;
      }

      // Basic validations
      // if(empty( $args['name'] )) {
      //   return new \WP_Error( 'no-name', __( 'No Node Name provided.', 'replicant' ) );
      // }
      if(empty( $args['host'] )) {
        return new \WP_Error( 'no-address', __( 'No Address provided.', 'replicant' ) );
      }
      if(empty( $args['port'] )) {
        return new \WP_Error( 'no-port', __( 'No Port provided.', 'replicant' ) );
      }

      // Remove row id to determine if new or update
      $row_id = (int) $args['id'];
      unset( $args['id'] );

      if(!$row_id) {
        // Insert a new Node
        if($wpdb->insert( $table_name, $args )) {
            // \Replicant\Log::write(
            //    sprintf(__("%s successfully created", "replicant"), $args["name"]),
            //    $wpdb->insert_id,
            //    1 // Info
            // );
            // error_log(print_r($wpdb->insert_id, true));
            return $wpdb->insert_id;
        } else {
            return new \WP_Error('db_error',  __('Something went wrong, Please try again.', 'replicant'), $args);
        }
      } else {
         // Do update method here
         if($wpdb->update( $table_name, $args, ['id' => $row_id] )) {
            // \Replicant\Log::write(
            //    sprintf(__("%s successfully updated", "replicant"), $args["name"]),
            //    $row_id,
            //    1 // Info
            // );

            return $row_id;
         }
      }

      return false;
   }

}
