<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * This file is the main basis for building a server
 */
class Node {

   /**
    * Server nickname
    * 
    * @var string
    */
   public $name;

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
    * Server path
    * 
    * @var string
    */
   public $path;

   /**
    * Server unique hash
    * 
    * @var string
    */
   public $hash;

   /**
    * Full server URL
    * 
    * @var array
    */
   public $url;

   /**
    * WordPress database instance
    * 
    * @var class
    */
   private $wpdb;

   public function __construct(Node $node = null) {
      // Don't need $wpdb, maybe delete it later
      // global $wpdb;
      // $this->wpdb = $wpdb;

      global $replicant;

      if($node) {
         return $node;
      }

      // Define values
      $url        = get_site_url();
      $parsed_url = parse_url($url);

      // Assign associated variables
      $this->url["parsed"] = $parsed_url;
      $this->url["full"]   = $url;
      $this->name          = get_bloginfo('name');
      $this->host          = $this->url["parsed"]["host"];
      $this->path = isset($this->url["parsed"]["path"]) ? $this->url["parsed"]["path"] : "";
      $this->port = isset($this->url["parsed"]["port"]) ? $this->url["parsed"]["port"] : 80;
      $this->hash = $replicant::$default_db::current_node_hash()->value;
   }

   /**
    * Search for by custom Key and Value in "nodes" table
    * 
    * @param  string|null $key   WHERE key in sql query
    * @param  string|null $value WHERE value in sql query
    * @return array              Single row fetched by $wpdb
    */
   public function get_by(string $key = null, $value = null) {
      if(!$value) {
         return;
      }

      $table_name = \Replicant\Config::$TABLES["nodes"];
      $query      = "SELECT * FROM $table_name WHERE `$key` = %s";
      $result     = $this->wpdb->get_row($this->wpdb->prepare($query, $value));

      return $result;
   }

}
