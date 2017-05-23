<?php
/*
Plugin Name: Test new page
Plugin URI: 
Description: 
Version: 1.1b
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
namespace PLUGIN_NAME;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('NEW_OPTION', 'option_name');
define('NEW_PLUG_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook(__FILE__, function(){
    $defaults = array(
      'some_option' => 'on',
      );

    add_option( NEW_OPTION, $defaults );
});

if(is_admin()){
  require_once NEW_PLUG_DIR . '/inc/class-wp-admin-page-render.php';
  require_once NEW_PLUG_DIR . '/inc/class-wp-form-render.php';

  add_filter( NEW_OPTION . '_columns', function(){return 2;} );

  $page = new WPAdminPageRender( NEW_OPTION,
  array(
    'parent' => 'options-general.php',
    'title' => __('Test New Plugin'),
    'menu' => __('New Plug Page'),
    ), 'PLUGIN_NAME\_render_page' );

  $page->add_metabox( 'handle', 'label', 'PLUGIN_NAME\qq');
  $page->set_metaboxes();
}

/**
 * Admin Page
 */
function qq(){
	echo "string";
}
function _render_page(){
  $data = array(
    array(
      'id' => 'scss-auto-compile',
      'type' => 'checkbox',
      'label' => 'Автокомпиляция',
      'desc' => 'По умолчанию автокомпиляция работает только с style.scss используя кэширование (Не компилируется если файл не изменялся с последней компиляции)',
      ),
    );

  WPForm::render(
    apply_filters( 'PLUGIN_NAME\dt_admin_options', $data ),
   WPForm::active(NEW_OPTION, false, true),
    true,
    array('clear_value' => false)
    );

  submit_button();
}