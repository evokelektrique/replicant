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
      global $wpdb;
      global $replicant;

      $this->wpdb = $wpdb;

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
      $this->ssl  = is_ssl();
      $this->hash = $replicant::$default_db::current_node_hash()->value;
   }

   /**
    * Return current node information as JSON string
    * 
    * @return string JSON encoded $node
    */
   public function get_json() {
      $node = [
         "name" => $this->name,
         "host" => $this->host,
         "path" => $this->path,
         "port" => $this->port,
         "ssl"  => $this->ssl,
         "url"  => $this->url,
         "hash" => $this->hash
      ];

      return $node;
   }

}
