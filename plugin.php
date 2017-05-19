<?php
/*
Plugin Name: NEW PLUGIN
Plugin URI: 
Description: 
Version: 1.1b
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('COMPILER_OPT', 'wp-compiler');
define('COMPILER_PLUG_DIR', plugin_dir_path( __FILE__ ) );
define('SCSS_OPTION', 'scss');
define('SCSS_CACHE', 'scss-cache');
define('SCSS_DEFAULT_DIR', get_template_directory() . '/assets/scss/');
define('ASSETS_DEFAULT_DIR', get_template_directory() . '/assets/');

register_activation_hook(__FILE__, function(){
    $defaults = array(
      'scss-auto-compile' => 'on',
      );

    add_option( COMPILER_OPT, $defaults );
});

if(is_admin()){
  require_once COMPILER_PLUG_DIR . '/inc/class-wp-admin-page-render.php';
  require_once COMPILER_PLUG_DIR . '/inc/class-wp-form-render.php';

  $page = new SCSS_COMPILER\WPAdminPageRender( COMPILER_OPT,
  array(
    'parent' => 'options-general.php',
    'title' => __('Настройки компиляции проекта'),
    'menu' => __('Компиляция'),
    ), '_render_page' );
}

$options = get_option( COMPILER_OPT );

/**
 * Admin Page
 */
function _render_page(){
  $data = array(
    array(
      'id' => 'scss-auto-compile',
      'type' => 'checkbox',
      'label' => 'Автокомпиляция',
      'desc' => 'По умолчанию автокомпиляция работает только с style.scss используя кэширование (Не компилируется если файл не изменялся с последней компиляции)',
      ),
    );

  SCSS_COMPILER\WPForm::render( apply_filters( 'SCSS_COMPILER\dt_admin_options', $data ),
    SCSS_COMPILER\WPForm::active(COMPILER_OPT, false, true),
    true,
    array('clear_value' => false)
    );

  submit_button();
}