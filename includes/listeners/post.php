<?php

namespace Replicant\Listeners;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Listens to posts events
 */
class Post {

   public function __construct() {
      add_action("save_post", [&$this, "listen"]);
   }

   /**
    * Fires once a post has been saved
    * 
    * @param  int     $post_id   Post ID
    * @param  WP_Post $post Post Object
    * @param  bool    $update    Whether this is an existing post being updated or not
    */
   private function listen($post_id, $post, $update) {
      if($update) {
         $this->update($post);
      }

      $this->
   }



}

