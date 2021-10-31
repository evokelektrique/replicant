<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * This file is the main basis for building a server
 */
class Node {

   use \Replicant\Actor;

   /**
    * Determine how this Node should act
    *
    * @var act
    */
   public $acting_as;

   /**
    * Server nickname
    *
    * @var string
    */
   public $name;

   /**
    * Server HTTP(s) support
    *
    * @var boolean
    */
   public $ssl;

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

   /**
    * Initialize a Node
    *
    * @param Node|null $node
    */
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
      $this->name = get_bloginfo('name');
      $this->host = $this->url["parsed"]["host"];
      $this->path = isset($this->url["parsed"]["path"]) ? $this->url["parsed"]["path"] : "";
      $this->ssl  = is_ssl();

      // Define port based on SSL support
      if($this->ssl) {
         // It's SSL
         $this->port = 443;
      } elseif(isset($this->url["parsed"]["port"])) {
         // It's a different port
         $this->port = $this->url["parsed"]["port"];
      } else {
         // Default port
         $this->port = 80;
      }

      $this->hash = $replicant::$default_db::current_node_hash()->value;
      $this->acting_as = $replicant::$default_db::acting_as()->value;
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

   public function get_acting_as() {
      return "Sender";
   }

}
