<?php
/*
Plugin Name: Новый плагин
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
  new PluginClassName();
} );
register_activation_hook( __FILE__, array( 'PluginClassName', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'PluginClassName', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'PluginClassName', 'uninstall' ) );

class PluginClassName {
  const SETTINGS = 'Page_Slug';

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

    add_filter( self::SETTINGS . '_columns', function(){return 2;} );

    $page = new WPAdminPageRender(
      self::SETTINGS,
      array(
        'parent' => 'options-general.php',
        'title' => __('Plugin page title'),
        'menu' => __('Plugin menu title'),
        // 'tab_sections' => array('tab1' => 'title1', 'tab2' => 'title2')
        ),
      array($this, 'admin_settings_page')
      // array(
      //   'tab1' => array($this, 'admin_settings_page'),
      //   'tab2' => array($this, 'admin_settings_page_tab2'),
      //   )
      );

    $page->add_metabox( 'metabox1', 'first metabox', array($this, 'metabox_cb'), $position = 'side');
    $page->add_metabox( 'metabox2', 'second metabox', array($this, 'metabox_cb'), $position = 'side');
    $page->set_metaboxes();
  }

  private static function define_constants(){
    define('BSMCE_DIR', plugin_dir_path( __FILE__ ) );
  }
  private static function load_classes(){
    require_once BSMCE_DIR . '/inc/class-wp-admin-page-render.php';
    require_once BSMCE_DIR . '/inc/class-wp-form-render.php';
  }

  function admin_settings_page(){
    echo __("Choose options:");

    $args = array(
      array(
        'id' => 'add][tabs',
        'type' => 'checkbox',
        'label' => __('Add Tabs'),
        'desc' => __('Include bootstrap tabs to MCE'),
        ),
      );

    $active = WPForm::active(self::SETTINGS, false, true);
    WPForm::render( $args, $active, true, array('admin_page' => self::SETTINGS) );

    submit_button( __('Save') );
  }
  function admin_settings_page_tab2(){
    echo "Page 2";

    submit_button( __('Save') );
  }

  function metabox_cb(){
    echo "METABOX";
  }
}
