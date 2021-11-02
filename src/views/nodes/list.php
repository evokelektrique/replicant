<div class="wrap">

   <?php
      $message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : NULL;
      $status  = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : NULL;

      // Only print the notice when a status has given
      if($status) {
         \Replicant\Helper::print_notice($status, $message);
      }
   ?>

    <h2>
      <?php _e( 'Nodes', 'replicant' ); ?>

      <a
      href="<?php echo admin_url( 'admin.php?page=replicant-nodes&action=new' ); ?>"
      class="add-new-h2"
      >
         <?php _e( 'Add New', 'replicant' ); ?>
      </a>
   </h2>

    <form method="post">
        <input type="hidden" name="page">

        <?php
           $list_table = new \Replicant\Tables\Nodes\ListTable();
           $list_table->prepare_items();
           $list_table->search_box(__('Search'), 'search_id');
           $list_table->display();
        ?>
    </form>
</div>
