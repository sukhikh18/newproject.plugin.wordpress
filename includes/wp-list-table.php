<?php

namespace CDevelopers\NSPACE;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Example_List_Table extends \WP_List_Table {

    protected $columns = array();

    protected $fields = array();
    protected $data = array();

    public function __construct()
    {
        // Set parent defaults.
        parent::__construct( array(
            'singular' => 'modal',
            'plural'   => 'modals',
            'ajax'     => false,
        ) );
    }

    public function set_fields( $post_args = array('post_type'=>'post') )
    {
        $res = array();
        $posts = get_posts( $post_args );
        // var_dump(current( $posts ));

        $columns = $this->get_columns();
        array_shift( $columns );
        // $columns = array_merge(array('ID' => 0), $columns);
        $allows = array_keys( $columns );

        foreach ($posts as $post) {
            $res[$post->ID]['ID'] = $post->ID;
            foreach ($allows as $allow) {
                if( strpos($allow, '_') == 0 ) {
                    $res[$post->ID][$allow] = get_post_meta( $post->ID, $allow, true );
                }
                elseif( isset( $post->$allow ) ) {
                    $res[$post->ID][$allow] = $post->$allow;
                }
            }
        }
        $this->data = $res;
        // $this->fields = $rows;

        // foreach ($rows as $row) {
        //     foreach ($row as $field_key => $field_val) {
        //         if( in_array($field_key, $allows) ) {

        //         }
        //     }
        // }
    }

    /** THEAD */
    public function get_columns()
    {
        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'post_title' => __( 'Title', DOMAIN ),
            // '_count'    => 'Click Count',
            // '_selector' => 'Selector',
            // '_theme'    => 'Design',
            'post_author'   => __( 'Author', DOMAIN ),
            'post_date'     => __( 'Date', DOMAIN ),
            );

        return $columns;
    }

    /********************************* Columns ********************************/
    protected function column_default( $item, $column_name )
    {

        return isset( $item[ $column_name ] ) ? $item[ $column_name ] : 'null';
    }

    protected function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
            $item['ID']                // The value of the checkbox should be the record's ID.
        );
    }

    protected function column_post_title( $item )
    {
        $page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

        // Build edit row action.
        $edit_query_args = array(
            'page'   => $page,
            'action' => 'edit',
            'movie'  => $item['ID'],
        );

        $actions['edit'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            get_edit_post_link( $item['ID'] ),
            __( 'Edit' )
        );

        // Build delete row action.
        $delete_query_args = array(
            'page'   => $page,
            'action' => 'delete',
            'movie'  => $item['ID'],
        );

        $actions['delete'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            get_delete_post_link( $item['ID'], '', true ),
            __( 'Delete' )
        );

        // Return the title contents.
        return sprintf( '%1$s %2$s',
            $item['post_title'],
            $this->row_actions( $actions )
        );
    }

    protected function column_post_author( $item ) {
        $_user = get_user_by( 'id', $item['post_author'] );

        return sprintf('<a href="%s">%s</a>', get_edit_user_link( $_user->ID ), $_user->data->user_nicename );
    }

    /****************************** Bulk Actions ******************************/
    protected function get_bulk_actions() {
        $actions = array(
            'delete' => __( 'Delete' ),
        );

        return $actions;
    }

    protected function process_bulk_action() {
        // Detect when a bulk action is being triggered.
        if ( 'delete' === $this->current_action() ) {
            wp_die( 'Items deleted (or they would be if we had items to delete)!' );
        }
    }

    /******************************** Sortable ********************************/
    protected function get_sortable_columns() {
        $sortable_columns = array(
            'post_title'  => array( 'title', false ),
            '_count'  => array( '_count', false ),
            '_theme'  => array( '_theme', false ),
            'post_author' => array( 'author', false ),
            'post_date'   => array( 'post_date', false ),
            );

        return $sortable_columns;
    }

    protected function usort_reorder( $a, $b ) {
        // If no sort, default to title.
        $orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'post_title'; // WPCS: Input var ok.

        // If no order, default to asc.
        $order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

        // Determine sort order.
        $result = strcmp( $a[ $orderby ], $b[ $orderby ] );

        return ( 'asc' === $order ) ? $result : - $result;
    }

    /**
     * Prepares the list of items for displaying.
     *
     * @global wpdb $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    public function prepare_items() {
        global $wpdb;

        $per_page = 20;

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->process_bulk_action();

        $data = $this->data;


        usort( $data, array( $this, 'usort_reorder' ) );

        $current_page = $this->get_pagenum();

        $total_items = count( $data );

        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                     // WE have to calculate the total number of items.
            'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
            'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
        ) );
    }
}