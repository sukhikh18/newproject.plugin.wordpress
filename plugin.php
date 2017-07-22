<?php
/*
Plugin Name: Bootstrap 4 Plugins for MCE
Plugin URI: 
Description: 
Version: 1.0
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

add_action( 'plugins_loaded', function(){
  new BS4Plugins();
} );
register_activation_hook( __FILE__, array( 'BS4Plugins', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'BS4Plugins', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'BS4Plugins', 'uninstall' ) );

class BS4Plugins {
  const SETTINGS = 'BS4MCE';

  public $settings = array();

  private function __clone() {}
  private function __wakeup() {}

  public static function activate(){
    add_option( self::SETTINGS, array() );
  }
  
  public static function uninstall(){
    delete_option(self::SETTINGS);
  }

  function __construct() {
    self::define_constants();
    self::load_classes();
    $this->settings = get_option( self::SETTINGS, array() );

    if( is_admin() ){
      new WPAdminPageRender(
        self::SETTINGS,
        array(
          'parent' => 'options-general.php',
          'title' => __('Bootstrap Plugins for MCE'),
          'menu' => __('Bootstrap for MCE'),
          ),
        array($this, 'admin_settings_page')
        );
    }
  }

  private static function define_constants(){
    define('BSMCE_DIR', plugin_dir_path( __FILE__ ) );
  }
  private static function load_classes(){
    require_once BSMCE_DIR . '/inc/class-wp-admin-page-render.php';
    require_once BSMCE_DIR . '/inc/class-wp-form-render.php';
  }

  function admin_settings_page(){
    echo "render page here";
  }
}