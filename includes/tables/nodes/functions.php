<?php

namespace Replicant\Tables\Nodes;

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

       $args      = wp_parse_args( $args, $defaults );
       $cache_key = 'node-all';
       $items     = wp_cache_get( $cache_key, 'replicant' );

       if ( false === $items ) {
           $items = $wpdb->get_results( 'SELECT * FROM ' . 'replicant_nodes ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'] );

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

       return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . 'replicant_nodes' );
   }

   /**
    * Fetch a single node from database
    *
    * @param int   $id
    *
    * @return array
    */
   public static function get( $id = 0 ) {
       global $wpdb;

       return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'replicant_nodes WHERE id = %d', $id ) );
   }
}