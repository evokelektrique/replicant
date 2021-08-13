<?php

namespace Replicant\Tables\Nodes;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Functions {

   /**
    * Get all node
    *
    * @param $args array
    * @return array
    */
   public static function get_all( $search = null, $args = array() ) {
      global $wpdb;

      $defaults = array(
         'number'     => 20,
         'offset'     => 0,
         'orderby'    => 'id',
         'order'      => 'ASC',
      );

      $args       = wp_parse_args( $args, $defaults );
      $cache_key  = 'node-all';
      $items      = wp_cache_get( $cache_key, 'replicant' );
      $table_name = \Replicant\Config::$TABLES["nodes"];

      if ( false === $items ) {
         if($search && !empty($search)) {
            $items = $wpdb->get_results(
               "SELECT * FROM $table_name WHERE name LIKE '%{$search}%' ORDER BY {$args['orderby']} {$args['order']} LIMIT {$args['offset']} , {$args['number']}" 
            );

         } else {
            $items = $wpdb->get_results(
               "SELECT * FROM $table_name ORDER BY {$args['orderby']} {$args['order']} LIMIT {$args['offset']} , {$args['number']}" 
            );
         }

         wp_cache_set( $cache_key, $items, 'replicant' );
      }

       return $items;
   }

   /**
    * Fetch all node from database
    *
    * @return array
    */
   public static function get_count() {
      global $wpdb;
      
      $table_name = \Replicant\Config::$TABLES["nodes"];

      return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
   }

   /**
    * Fetch a single node from database
    *
    * @param int $id
    * @return array
    */
   public static function get( $id = 0 ) {
      global $wpdb;

      $table_name = \Replicant\Config::$TABLES["nodes"];

      return $wpdb->get_row( 
         $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id )
      );
   }

   /**
    * Search for by custom Key and Value in "nodes" table
    * 
    * @param  string|null $key   WHERE key in sql query
    * @param  string|null $value WHERE value in sql query
    * @return array              Single row fetched by $wpdb
    */
   public static function get_by(string $key = null, $value = null) {
      if(!$value) {
         return;
      }

      global $wpdb;

      $table_name = \Replicant\Config::$TABLES["nodes"];
      $query      = "SELECT * FROM $table_name WHERE `$key` = %s";
      $result     = $wpdb->get_row($wpdb->prepare($query, $value));

      return $result;
   }

   public static function delete( $id = null ) {
      if(!$id) {
         return;
      }

      global $wpdb;
      $table_name = \Replicant\Config::$TABLES["nodes"];

      $wpdb->delete( $table_name, ["id" => $id], ["%d"] );
   }


   /**
    * Find and accept trust of given Node
    * 
    * @param  int          $id   Node ID
    * @param  string       $hash Node unique hash
    * @return array|object       Success message or error
    */
   public static function accept_trust(string $hash) {
      $node = self::get_by("hash", $hash);
      if(!$node) {
         return new \WP_Error( 'node-not-found', "Couldn't find Node" );
      }

      global $wpdb;
      $table_name = \Replicant\Config::$TABLES["nodes"];

      $args = [
         "is_trusted" => true
      ];

      $status  = true;
      $message = __("Successfully accepted trust of the Node", "replicant");
      $update  = $wpdb->update( $table_name, $args, ['hash' => $node->hash] );

      if($update) {
         $status  = false;
         $message = __("Something went wrong in accepting trust, Please try again.", "replicant");
      }

      return [
         "status"  => $status,
         "message" => $message
      ];
   }

}