<?php

namespace Replicant;

class Config {
   public static $config;
   public static $ROOT_DIR;
   public static $ROOT_URL;

   public function __construct() {
      self::$ROOT_DIR = plugin_dir_path( __FILE__ );
      self::$ROOT_URL = plugins_url( "/", __FILE__ );
      self::$config = [];
   }
}