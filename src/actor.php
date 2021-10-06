<?php

namespace Replicant;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

/**
 * This file is the main basis for how a Node should act
 */
trait Actor {

   protected $ACTORS = [
      "SENDER" => 0,
      "RECEIVER" => 1
   ];

   public function set_acting($act_as = "SENDER") {
      $this->acting_as = $this->ACTORS[$act_as];
   }

   public function get_acting() {
      if($this->acting_as === 0) {
         return "SENDER";
      } else {
         return "RECEIVER";
      }
   }

}
