<?php

namespace Replicant\Tables\Nodes;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

class Functions {

   /**
    * Get all node
    *
    * @param $args array
    *
    * @return array
    */
   public static function get_all( $args = array() ) {
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
         $items = $wpdb->get_results( 
            "SELECT * FROM $table_name ORDER BY {$args['orderby']} {$args['order']} LIMIT {$args['offset']} , {$args['number']}" 
         );

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
    *
    * @return array
    */
   public static function get( $id = 0 ) {
      global $wpdb;

      $table_name = \Replicant\Config::$TABLES["nodes"];

      return $wpdb->get_row( 
         $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id )
      );
   }
}