<?php

add_action('init', 'register_PLUGINNAME_types');
function register_PLUGINNAME_types(){
    register_post_type(PLUGINNAME::SETTINGS, array(
        'label'  => null,
        'labels' => array(
            'name'               => __('Post'), // основное название для типа записи
            'singular_name'      => 'Запись', // название для одной записи этого типа
            'add_new'            => 'Добавить Запись', // для добавления новой записи
            'add_new_item'       => 'Добавление Записи', // заголовка у вновь создаваемой записи в админ-панели.
            'edit_item'          => 'Редактирование Записи', // для редактирования типа записи
            'new_item'           => 'Новая Запись', // текст новой записи
            'view_item'          => 'Смотреть Запись', // для просмотра записи этого типа.
            'search_items'       => 'Искать Запись', // для поиска по этим типам записи
            'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
            'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
            'parent_item_colon'  => '', // для родителей (у древовидных типов)
            // 'menu_name'          => 'Записи', // название меню
        ),
        'description'         => '',
        'public'              => false,
        'publicly_queryable'  => null,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => false, // показывать ли в меню адмнки
        'show_in_admin_bar'   => false, // по умолчанию значение show_in_menu
        'show_in_nav_menus'   => false,
        'show_in_rest'        => null, // добавить в REST API. C WP 4.7
        'rest_base'           => null, // $post_type. C WP 4.7
        'menu_position'       => null,
        'menu_icon'           => null,
        //'capability_type'   => 'post',
        //'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
        //'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
        'hierarchical'        => false,
        'supports'            => array('title','editor'), // 'title','editor','author','thumbnail','excerpt','trackbacks','comments','revisions','page-attributes','post-formats', 'custom-fields'
        'taxonomies'          => array(),
        'has_archive'         => false,
        'rewrite'             => true,
        'query_var'           => true,
    ) );
}

$mb = new WP_Post_Boxes( array( strtolower(PLUGINNAME::SETTINGS) ) );
$mb->add_fields( '_count' );
$mb->add_box( 'Test Name', 'test_callback', $side = false );
function test_callback() {
    global $post;

    $data = array(
            // id or name - required
            array(
                'id'    => '_count',
                'type'  => 'text',
                'label' => 'TextField',
                'desc'  => 'This is example text field',
                ),
            );

    $count = array( '_count' => get_post_meta( $post->ID, '_count', true ) );

    $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            'admin_page'  => false,
            'postmeta'    => true,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
            // 'hide_desc'   => false,
        ) );
    echo $form->render();
}