<?php
/**
 * Metabox area output file
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

use NikolayS93\WPAdminForm\Form as Form;

// @var array $data id or name - required
$data = array(
	array(
		'id'    => 'example_0',
		'type'  => 'text',
		'label' => 'TextField',
		'desc'  => 'This is example text field',
	),
	array(
		'id'      => 'example_1',
		'type'    => 'select',
		'label'   => 'Select',
		'options' => array(
			'key_option5' => 'option5',
			'option1'     => array(
				'key_option2' => 'option2',
				'key_option3' => 'option3',
				'key_option4' => 'option4',
			),
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

$option        = new Option();
$option_values = $option
	->fetch()
	->get_array();

if ( ! empty( $option_values ) ) {
	echo '<pre>';

	foreach ( $option_values as $option_key => $option_value ) {
		echo esc_html( "$option_key: $option_value\r\n" );
	}

	echo '</pre>';
}

submit_button( 'Сохранить', 'primary right', 'save_changes' );
echo '<div class="clear"></div>';
