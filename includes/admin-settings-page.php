<?php

namespace CDevelopers\NSPACE;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class AdminSettingsPage
{
    function __construct()
    {
        $page = new WP_Admin_Page( Utils::get_option_name() );
        $page->set_args( array(
            'parent'      => false,
            'title'       => __('Pluginname Title', DOMAIN),
            'menu'        => __('New Plugin', DOMAIN),
            'callback'    => array($this, 'page_render'),
            // 'validate'    => array($this, 'validate_options'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
            ) );

        $page->set_assets( array($this, '_assets') );

        $page->add_metabox( 'metabox1', 'metabox1', array($this, 'metabox1_callback'), $position = 'side');
        $page->add_metabox( 'metabox2', 'metabox2', array($this, 'metabox2_callback'), $position = 'side');
        $page->set_metaboxes();
    }

    function _assets()
    {
        // wp_enqueue_style();
        // wp_enqueue_script();
    }

    /**
     * Основное содержимое страницы
     *
     * @access
     *     must be public for the WordPress
     */
    function page_render()
    {
        $table = new WP_List_Table();
        $table->set_columns();
        // @todo repair it
        // $table->set_sortable_columns();

        foreach (get_posts() as $post) {
            $table->set_value( array(
                'title' => esc_html( $post->post_title ),
            ) );
        }

        $table->prepare_items();
        $table->display();

        printf( '<input type="hidden" name="page" value="%s" />', $_REQUEST['page'] );
    }

    /**
     * Тело метабокса вызваное функций $this->add_metabox
     *
     * @access
     *     must be public for the WordPress
     */
    function metabox1_callback()
    {
        echo "<pre>";
        print_r( Utils::get( 'all', array() ) );
        echo "</pre>";
    }

    function metabox2_callback() {
        $data = array(
            // id or name - required
            array(
                'id'    => 'example_0',
                'type'  => 'text',
                'label' => 'TextField',
                'desc'  => 'This is example text field',
                ),
             array(
                'id'    => 'example_1',
                'type'  => 'select',
                'label' => 'Select',
                'options' => array(
                    // simples first (not else)
                    'key_option5' => 'option5',
                    'option1' => array(
                        'key_option2' => 'option2',
                        'key_option3' => 'option3',
                        'key_option4' => 'option4'),
                    ),
                ),
            array(
                'id'    => 'example_2',
                'type'  => 'checkbox',
                'label' => 'Checkbox',
                ),
            );

        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            // Defaults:
            // 'admin_page'  => true,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
            // 'hide_desc'   => false,
            ) );
        echo $form->render();

        submit_button( 'Сохранить', 'primary right', 'save_changes' );
        echo '<div class="clear"></div>';
    }
}
new AdminSettingsPage();
