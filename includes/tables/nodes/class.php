<?php

namespace Replicant\Tables\Nodes;

if ( ! class_exists ( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class TableList extends \WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular' => 'node',
            'plural'   => 'nodes',
            'ajax'     => false
        ) );
    }

    function get_table_classes() {
        return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
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
     *
     * @return string
     */
    function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'name':
                return $item->name;

            case 'host':
                return $item->host;

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
            'name'      => __( 'Name', 'replicant' ),
            'host'      => __( 'Host Name', 'replicant' ),

        );

        return $columns;
    }

    /**
     * Render the designation name column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_name( $item ) {
      $actions           = array();
      
      $actions['edit']   = sprintf( 
         '<a href="%s" data-id="%d" title="%s">%s</a>', 
         admin_url( 'admin.php?page=replicant-nodes&action=edit&id=' . $item->id ), 
         $item->id, __( 'Edit this item', 'replicant' ), __( 'Edit', 'replicant' ) 
      );

      $actions['delete'] = sprintf( 
         '<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>', 
         admin_url( 'admin.php?page=replicant-nodes&action=delete&id=' . $item->id ), 
         $item->id, __( 'Delete this item', 'replicant' ), __( 'Delete', 'replicant' ) 
      );

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
        $sortable_columns = array(
            'name' => array( 'name', true ),
        );

        return $sortable_columns;
    }

    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'trash'  => __( 'Move to Trash', 'replicant' ),
        );
        return $actions;
    }

    /**
     * Render the checkbox column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="node_id[]" value="%d" />', $item->id
        );
    }

    /**
     * Prepare the class items
     *
     * @return void
     */
    function prepare_items() {

        $columns               = $this->get_columns();
        $hidden                = array( );
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page              = 20;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
        );

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order']   = $_REQUEST['order'] ;
        }

        $this->items = Functions::get_all( $args );

        $this->set_pagination_args( array(
            'total_items' => Functions::get_count(),
            'per_page'    => $per_page
        ) );
    }
}