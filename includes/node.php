<?php

namespace Replicant;

/**
 * This file is the main basis for building a server
 */
class Node {

   /**
    * Server hostname
    * @var string
    */
   public $host;

   /**
    * Server port
    * @var integer
    */
   public $port;

   /**
    * Server nickname
    * @var string
    */
   public $name;

   private $wpdb;

   public function __construct() {
      global $wpdb;
      $this->wpdb = $wpdb;
   }

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
