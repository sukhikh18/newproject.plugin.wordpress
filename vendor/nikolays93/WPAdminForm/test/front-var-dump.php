<?php

use NikolayS93\WPAdminForm\Form;

add_action( 'get_footer', 'WPAdminFormTest' );
function WPAdminFormTest() {
    $fields = array(
        array(
            'label' => 'Label:',
            'id' => 'test',
            'default' => 'text',
        )
    );

    $form = new Form( $fields, true, array(
        'admin_page' => 'field'
    ) );

    $form->display();
}