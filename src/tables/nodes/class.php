<?php

namespace Replicant\Tables\Nodes;

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

if(!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class ListTable extends \WP_List_Table {

   function __construct() {
      parent::__construct([
         'singular' => 'node',
         'plural'   => 'nodes',
         'ajax'     => false
      ]);
   }

   function get_table_classes() {
      return ['replicant-nodes', 'widefat', 'fixed', 'striped', $this->_args['plural']];
   }

   /**
    * Message to show if no designation found
    *
    * @return void
    */
   function no_items() {
      _e( 'No node found', 'replicant' );
   }

   /**
    * Default column values if no callback found
    *
    * @param  object  $item
    * @param  string  $column_name
    * @return string
    */
    function column_default( $item, $column_name ) {

      switch ( $column_name ) {
         case 'name':
            return $item->name;

         case 'host':
            return $item->host;

         case 'port':
            return $item->port;

         case 'ssl':
            return \Replicant\Helper::print_badge($item->ssl);

         case 'is_trusted':
            return \Replicant\Helper::print_badge($item->is_trusted);

         default:
            return isset( $item->$column_name ) ? $item->$column_name : '';
      }
   }

   /**
    * Get the column names
    *
    * @return array
    */
    function get_columns() {
      $columns = array(
         'cb'           => '<input type="checkbox" />',
         'name'         => __( 'Name', 'replicant' ),
         'host'         => __( 'Address', 'replicant' ),
         'port'         => __( 'Port', 'replicant' ),
         'ssl'          => __( 'SSL', 'replicant' ),
         'is_trusted'   => __( 'Trust', 'replicant' )
      );

      return $columns;
   }

   /**
    * Render the designation name column
    *
    * @param  object  $item
    * @return string
    */
   function column_name( $item ) {
      $actions           = [];

      $actions['edit']   = sprintf(
         '<a href="%s" data-id="%d" title="%s">%s</a>',
         admin_url( 'admin.php?page=replicant-nodes&action=edit&id=' . $item->id ),
         $item->id, __( 'Edit this item', 'replicant' ), __( 'Edit', 'replicant' )
      );

      $actions['view']   = sprintf(
         '<a href="%s" data-id="%d" title="%s">%s</a>',
         admin_url( 'admin.php?page=replicant-nodes&action=view&id=' . $item->id ),
         $item->id, __( 'View this item', 'replicant' ), __( 'View', 'replicant' )
      );

      $actions['delete'] = sprintf(
         '<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>',
         admin_url( 'admin.php?page=replicant-nodes&action=delete&id=' . $item->id ),
         $item->id, __( 'Delete this item', 'replicant' ), __( 'Delete', 'replicant' )
      );

      // Only visible when the Node item is not trusted
      if(!intval($item->is_trusted)) {
         if(intval($item->is_trust_request)) {
            $actions['accept_trust'] = sprintf(
               '<a href="%s" class="replicant_submit_request_trust" data-id="%d" title="%s">%s</a>',
               admin_url( 'admin.php?page=replicant-nodes&action=accept_trust&id=' . $item->id . "&hash=" . $item->hash ),
               $item->id, __( 'Accept trust', 'replicant' ), __( 'Accept trust', 'replicant' )
            );
         } else {
            $actions['request_trust'] = sprintf(
               '<a href="%s" class="replicant_submit_request_trust" data-id="%d" title="%s">%s</a>',
               admin_url( 'admin.php?page=replicant-nodes&action=request_trust&id=' . $item->id ),
               $item->id, __( 'Request trust this item', 'replicant' ), __( 'Request trust', 'replicant' )
            );
         }
      }

      return sprintf(
         '<a href="%1$s"><strong>%2$s</strong></a> %3$s',
         admin_url( 'admin.php?page=replicant-nodes&action=view&id=' . $item->id ),
         $item->name,
         $this->row_actions( $actions )
      );
   }

   /**
    * Get sortable columns
    *
    * @return array
    */
   function get_sortable_columns() {
      $sortable_columns = [
         'name' => ['name', true],
      ];
      return $sortable_columns;
   }

   /**
    * Set the bulk actions
    *
    * @return array
    */
   function get_bulk_actions() {
      $actions = [
         'trash'  => __( 'Move to Trash', 'replicant' ),
      ];
      return $actions;
   }

   /**
    * Render the checkbox column
    *
    * @param  object  $item
    * @return string
    */
   function column_cb( $item ) {
      return sprintf(
         '<input type="checkbox" name="node_id[]" value="%d" />', $item->id
      );
   }

   function process_trash() {
      if($this->current_action() === 'delete') {
         $id = intval($_GET["id"]);
         \Replicant\Log::purge($id);
         Functions::delete($id);
      }
   }

   function process_bulk_action() {
      if($this->current_action() === 'trash') {
         foreach ($_POST['node_id'] as $id) {
            \Replicant\Log::purge(intval($id));
            Functions::delete(intval($id));
         }
      }
   }

   /**
    * Prepare the class items
    *
    * @return void
    */
   function prepare_items() {
      // Check if user is searching
      $search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : false;

      $columns               = $this->get_columns();
      $hidden                = [];
      $sortable              = $this->get_sortable_columns();
      $this->_column_headers = [$columns, $hidden, $sortable];

      // Deletion processes
      $this->process_bulk_action();
      $this->process_trash();

      $per_page          = 20;
      $current_page      = $this->get_pagenum();
      $offset            = ( $current_page - 1 ) * $per_page;
      $this->page_status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';

      // only ncessary because we have sample data
      $args = [
         'offset' => $offset,
         'number' => $per_page,
      ];

      if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
         $args['orderby'] = $_REQUEST['orderby'];
         $args['order']   = $_REQUEST['order'] ;
      }

      $this->items = Functions::get_all($search, $args);

      $this->set_pagination_args([
         'total_items' => Functions::get_count(),
         'per_page'    => $per_page
      ]);
   }
}
