<div class="wrap">
   <?php
      // Print out notice
      if(is_wp_error( $response )) {
         $message = $response->get_error_message();
         $status  = "error";
      } else {
         $status  = $response["status"] ? "success" : "error";
         $message = $response["message"];
      }

      \Replicant\Helper::print_notice($status, $message);
   ?>
   <br>
   <a href="<?php echo admin_url( 'admin.php?page=replicant-nodes' ); ?>" class="button">
      <?php esc_html_e("Go back", "replicant") ?>
   </a>
</div>

