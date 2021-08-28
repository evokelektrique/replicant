<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Handle hooks and actions
 */
class Hooks {

   /**
    * Attach custom function to wordpress hooks
    * 
    * @param hook $tag      Hook name
    * @param void $function Function to run when hook is triggered
    */
   public static function add_action($tag, $function) {
      add_action($tag, $function);
   }

}
