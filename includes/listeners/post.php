<?php

namespace Replicant\Listeners;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit; 

/**
 * Listens to post events
 */
class Post {

   public function __construct() {
      // add_action("save_post", [&$this, "listen"]);
      add_action( 'post_updated', [&$this, 'check_values'], 10, 3 );
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
   }

   function check_values($post_ID, $post_after, $post_before){
      echo '<b>Post ID:</b><br />';
      var_dump($post_ID);

      echo '<b>Post Object AFTER update:</b><br />';
      var_dump($post_after);

      echo '<b>Post Object BEFORE update:</b><br />';
      var_dump($post_before);
   }
    


}

