<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Example_List_Table extends WP_List_Table {

    protected $columns = array();

    protected $fields = array();
    protected $example_data = array(
        array(
            'ID'       => 1,
            'title'    => '300',
        ),
        array(
            'ID'       => 2,
            'title'    => 'Eyes Wide Shut',
        ),
        array(
            'ID'       => 3,
            'title'    => 'Moulin Rouge!',
        ),
        array(
            'ID'       => 4,
            'title'    => 'Snow White',
        ),
        array(
            'ID'       => 5,
            'title'    => 'Super 8',
        ),
        array(
            'ID'       => 6,
            'title'    => 'The Fountain',
        ),
        array(
            'ID'       => 7,
            'title'    => 'Watchmen',
        ),
        array(
            'ID'       => 8,
            'title'    => '2001',
        ),
    );

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
        $this->example_data = $res;
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
            'post_title' => __( 'Title' ),
            '_count'    => __( 'Click Count' ),
            '_selector' => __( 'Selector' ),
            '_theme'    => __( 'Design' ),
            'post_author'   => __( 'Author' ),
            'post_date'     => __( 'Date' ),
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
            esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletemovie_' . $item['ID'] ) ),
            __( 'Delete' )
        );

        // Return the title contents.
        return sprintf( '%1$s %2$s',
            $item['post_title'],
            $this->row_actions( $actions )
        );
    }

    protected function column_rating( $item ) {

        return 'Yes';
    }

    /****************************** Bulk Actions ******************************/
    protected function get_bulk_actions() {
        $actions = array(
            'delete' => _x( 'Delete', 'List table bulk action', 'wp-list-table-example' ),
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
        global $wpdb; //This is used only if making any database queries

        /*
         * First, lets decide how many records per page to show
         */
        $per_page = 5;

        /*
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        /*
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * three other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array( $columns, $hidden, $sortable );

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        /*
         * GET THE DATA!
         *
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our dummy data.
         *
         * In a real-world situation, this is probably where you would want to
         * make your actual database query. Likewise, you will probably want to
         * use any posted sort or pagination data to build a custom query instead,
         * as you'll then be able to use the returned query data immediately.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         */

        $data = $this->example_data;


        /*
         * This checks for sorting input and sorts the data in our array of dummy
         * data accordingly (using a custom usort_reorder() function). It's for
         * example purposes only.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary. In other words: remove this when
         * you implement your own query.
         */
        usort( $data, array( $this, 'usort_reorder' ) );

        /*
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /*
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count( $data );

        /*
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to do that.
         */
        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

        /*
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
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