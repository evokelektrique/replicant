<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * This file is the main basis for building a server
 */
class Node {

   /**
    * Server hostname
    * 
    * @var string
    */
   public $host;

   /**
    * Server port
    * 
    * @var integer
    */
   public $port;

   /**
    * Server nickname
    * 
    * @var string
    */
   public $name;

   /**
    * WordPress database instance
    * 
    * @var class
    */
   private $wpdb;

   public function __construct(Node $node = null) {
      global $wpdb;
      
      if($node) {
         var_dump($node);
      }

      $this->wpdb = $wpdb;
   }

   /**
    * Search for by custom Key and Value in "nodes" table
    * 
    * @param  string|null $key   WHERE key in sql query
    * @param  string|null $value WHERE value in sql query
    * @return array              Single row returned by $wpdb
    */
   public function get_by(string $key = null, $value = null) {
      if(!$value) {
         return;
      }

      $table_name = \Replicant\Config::$TABLES["nodes"];
      $query = "SELECT * FROM $table_name WHERE `$key` = %s";
      $result = $this->wpdb->get_row($this->wpdb->prepare($query, $value));

      var_dump($result);
   }

}
