<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Manage logs in database
 */
class Log {

   /**
    * Fetch all logs related to the given node ID
    * 
    * @param  int   $node_id The desired node ID
    * @return array          List of fetched logs from Database
    */
   public static function get_all(int $node_id) {
      global $wpdb;

      $table_name = \Replicant\Config::$TABLES["logs"];
      $query      = "SELECT * FROM $table_name WHERE node_id = %d";
      $result     = $wpdb->get_results($wpdb->prepare($query, $node_id));

      return $result;
   }

   /**
    * Write log into Database
    * 
    * @var $message  The desired message
    * @var $node_id  Node ID
    * @var $level    Log level (Debug: 0, Info: 1, Warning: 2, Error: 3)
    */
   public static function write(string $message, int $node_id, int $level = 0) {
      global $wpdb;
      
      $table_name = \Replicant\Config::$TABLES["logs"];
      $data       = ["message" => $message, "level" => $level, "node_id" => $node_id];
      $format     = ["%s", "%d", "%d"];
      $wpdb->insert($table_name, $data, $format);

      return $wpdb->insert_id;
   }

   /**
    * Deletes all associated logs with given node ID
    * 
    * @param  int $node_id The desired node ID
    * @return int|false    The number of rows affected, or false on error
    */
   public static function purge(int $node_id) {
      global $wpdb;

      $table_name = \Replicant\Config::$TABLES["logs"];
      $logs = self::get_all($node_id);
      $ids = array_map(function($item) {
         return $item->id;
      }, $logs);
      $ids = implode( ',', array_map( 'intval', $ids ) );
      // More efficent way to delete all related rows
      if(!empty($ids)) {
         $wpdb->query( "DELETE FROM $table_name WHERE id IN($ids)" );
      }
   }
}