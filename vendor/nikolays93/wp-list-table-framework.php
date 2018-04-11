<?php

namespace CDevelopers\NSPACE;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WP_List_Table_Framework extends \WP_List_Table {

    private $columns = array(),
            $sortable = array(),
            $values = array(),
            $actions = array();

    public function __construct($args = array())
    {
        $args = wp_parse_args( $args, array(
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false,
        ) );

        parent::__construct( $args );
    }

    /**
     * Set: Head Row
     */
    public function set_columns( $columns = array() )
    {
        $this->columns = wp_parse_args( $columns, array(
            'cb'     => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'  => __('Title'),
        ) );

        return $this->columns;
    }

    /**
     * required WP_List_Table method
     */
    public function get_columns() {

        return $this->columns;
    }

    public function set_sortable_columns( $sortable )
    {
        $this->sortable = wp_parse_args( $sortable, array(
            'title'  => array( 'title', false ),
        ) );

        return $this->sortable;
    }

    protected function get_sortable_columns() {

        return $this->sortable;
    }

    /**
     * Set: Body Row
     */
    public function set_value( $values )
    {
        $this->values[] = wp_parse_args( $values, array(
            'ID'    => '',
            'title' => '',
        ) );
    }

    /********************************* Columns ********************************/
    /**
     * Render: Callbacks checkbox
     */
    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="cb[]" value="%s" />',
            esc_attr($item['title']) );
    }

    /**
     * Render: Row Title
     */
    function column_title($item)
    {
        $first = mb_substr($item['title'], 0, 1, 'UTF-8');
        $last =  mb_substr($item['title'], 1);
        $first = mb_strtoupper($first, 'UTF-8');
        $last =  mb_strtolower($last, 'UTF-8');
        $name = $first . $last;

        /**
         * @todo repair it
         */
        // $actions = array(
        //     'edit' => sprintf('<a href="?page=%s&do=edit&context=%s&value=%s">%s</a>',
        //         Utils::OPTION,
        //         esc_attr( $this->context ),
        //         esc_attr( $item['title'] ),
        //         esc_attr( __('Edit') )
        //     ),
        //     'delete' => sprintf('<a href="%s">%s</a>',
        //         wp_nonce_url( sprintf('?page=%s&do=remove&context=%s&value=%s',
        //             esc_attr( $_REQUEST['page'] ),
        //             esc_attr( $this->context ),
        //             esc_attr( $item['title'] ) ), 'trash-'.$item['title'], '_wpnonce' ),
        //         __('Delete')
        //     ),
        // );
        $actions = array();

        return $name . $this->row_actions($actions);
    }

    /**
     * Render: Columns Data
     */
    function column_default($item, $column_name)
    {
        if( isset($item[ $column_name ]) )
            return $item[ $column_name ];

        return false;
    }

    public function single_row( $item )
    {
        printf('<tr class="%s">',
            !empty( $item['classrow'] ) ? $item['classrow'] : '');
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /****************************** Bulk Actions ******************************/
    public function set_bulk_actions( $actions )
    {
        $this->actions = wp_parse_args( $actions, array(
            'delete' => __( 'Delete' ),
        ) );

        return $actions;
    }

    protected function get_bulk_actions() {

        return $this->actions;
    }

    protected function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            /**
             * @todo set hooks
             */
            wp_die( 'Items deleted (or they would be if we had items to delete)!' );
        }
    }

    /******************************** Sortable ********************************/
    protected function usort_reorder( $a, $b )
    {
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
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    public function prepare_items()
    {
        if( !count( $this->columns ) )
            $this->set_columns();

        $per_page = 20;

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->process_bulk_action();

        $data = $this->values;

        usort( $data, array( $this, 'usort_reorder' ) );

        $current_page = $this->get_pagenum();

        $total_items = count( $data );

        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

        $this->items = $data;

        if( $total_items > $per_page ) {
            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page ),
            ) );
        }
    }
}