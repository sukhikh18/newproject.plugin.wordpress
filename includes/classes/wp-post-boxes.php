<?php

class WP_Post_Boxes {
	const SECURITY = 'Secret';

	private $output_function = '';
	private $box_name = 'Example title';
	private $side = false;
	private $priority;
	private $post_types;

	private $meta_fields = array('');

	private static $count = 0;

	/**
	 * @param mixed $post_types Типы записей на которых нужно добавить бокс
	 */
	function __construct( $post_types = null ) {
		if( is_string($post_types) ) {
			$this->post_types = array( $post_types );
		}
		elseif( is_array($post_types) ) {
			$this->post_types = $post_types;
		}
		else {
			$this->post_types = array('post', 'page');
		}
	}

	/**
	 * Добавляет в массив значения которые нужно сохранять.
	 *
	 * @param string $field_name Название (name) значения.
	 */
	public function add_fields($field_name){
		if(is_array($field_name)){
			foreach ($field_name as $field) {
				array_push($this->meta_fields, esc_attr( $field ) );
			}
		}
		else {
			array_push($this->meta_fields, esc_attr( $field_name ) );
		}

		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Установка хука с предварительной установкой значений
	 * @param string 	$name   Название бокса
	 * @param string 	$output_function Название callback функции
	 * @param boolean 	$side   Показывать с боку / Нормально
	 */
	public function add_box($name = false, $output_function = false, $side = false){
		if($name)
			$this->box_name = sanitize_text_field($name);

		if($output_function)
			$this->output_function = $output_function;

		$this->side = $side;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Обертка WP функции add_meta_box, добавляет метабокс по параметрам класса
	 *
	 * @param string $post_type Название используемого типа записи
	 * @access private
	 *         (public for wordpress)
	 */
	function add_meta_box( $post_type ){
		// get post types without WP default (for exclude menu, revision..)
		// $post_types = get_post_types(array('_builtin' => false));
		// $add = array('post', 'page');
		// $post_types = array_merge($post_types, $add);

		if(!empty($this->output_function) && !empty($this->box_name)){
			$side = ($this->side) ? 'side' : 'advanced';

			self::$count++;
			add_meta_box(
				'custom-meta-box-'.self::$count,
				$this->box_name,
				array($this, 'callback_with_nonce'),
				$this->post_types,
				$side,
				null,
				array( self::SECURITY )
				);
		}
	}

	function callback_with_nonce()
	{
		call_user_func( $this->output_function );
		wp_nonce_field( self::SECURITY, $name = '_wp_metabox_nonce' );
	}

	/**
	 * Сохраняем данные при сохранении поста.
	 *
	 * @param int $post_id ID поста, который сохраняется.
	 * @access private
	 *         (public for wordpress)
	 */
	function save( $post_id ) {
		// file_put_contents(__DIR__ . '/debug.log', print_r($_POST, 1) );
		if ( ! isset( $_POST['_wp_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_metabox_nonce'], self::SECURITY ) )
			return $post_id;

		// $test = array();
		// $test['post'] = $_POST;
		// $test['metas'] = $this->meta_fields;
		// file_put_contents(__DIR__ . '/meta_debug.log', print_r($test, 1));
		// if ( ! current_user_can( 'edit_page', $post_id ) )
		// 	return $post_id;

		foreach ($this->meta_fields as $field) {
			if(isset($_POST[$field])){
				$meta = is_array($_POST[$field]) ?
				array_filter($_POST[$field], 'sanitize_text_field') : sanitize_text_field( $_POST[$field] );
				update_post_meta( $post_id, $field, $meta );
			}
			else {
				delete_post_meta( $post_id, $field );
			}
		}
	}
}
