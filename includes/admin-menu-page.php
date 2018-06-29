<?php

namespace CDevelopers\NSPACE;

use NikolayS93\WPAdminFormBeta\Form as Form;
use NikolayS93\WPAdminPageBeta as AdminPage;
use NikolayS93\WP_List_Table_Framework as Table;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class AdminSettingsPage
{
    function __construct()
    {
        $page = new AdminPage\Page(
            Utils::get_option_name(),
            __('Pluginname Title', DOMAIN),
            array(
                'parent'      => false,
                'menu'        => __('New Plugin', DOMAIN),
                // 'validate'    => array($this, 'validate_options'),
                'permissions' => 'manage_options',
                'columns'     => 2,
            )
        );

        $page->set_assets( new AdminPage\Callback( array($this, '_assets') ) );

        $page->set_content( new AdminPage\Callback( array($this, 'welcome_message') ) );

        $page->add_section( new AdminPage\Section(
            'Section',
            __('Section'),
            new AdminPage\Callback( array($this, 'post_table_example') )
        ) );

        $page->add_section( new AdminPage\Section(
            'Section_2',
            __('Section 2'),
            new AdminPage\Callback( array($this, 'metabox2_callback') )
        ) );

        $metabox1 = new AdminPage\Metabox(
            'metabox1',
            __('metabox1', DOMAIN),
            new AdminPage\Callback( array($this, 'metabox1_callback') ),
            $position = 'side',
            $priority = 'high'
        );
        $page->add_metabox( $metabox1 );
    }

    function _assets()
    {
        // wp_enqueue_style();
        // wp_enqueue_script();
    }

    function welcome_message()
    {
        echo '<p style="margin-top:-15px;">Hello world</p>';
    }

    /**
     * Основное содержимое страницы
     *
     * @access
     *     must be public for the WordPress
     */
    function post_table_example()
    {
        $table = new Table();
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

        $form = new Form( $data, $is_table = true );
        $form->display();

        submit_button( 'Сохранить', 'primary right', 'save_changes' );
        echo '<div class="clear"></div>';
    }
}
new AdminSettingsPage();
