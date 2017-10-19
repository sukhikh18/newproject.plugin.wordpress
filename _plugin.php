<?php
/*
Plugin Name: Новый плагин
Plugin URI:
Description:
Version: 0.1
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('PLUGINNAME_DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );
define('PLUGINNAME_URL', rtrim(plugins_url(basename(__DIR__)), '/') );

register_activation_hook( __FILE__, array( 'PLUGINNAME', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'PLUGINNAME', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'PLUGINNAME', 'uninstall' ) );

add_action( 'plugins_loaded', array('PLUGINNAME', 'get_instance'), 10 );
class PLUGINNAME {
    const SETTINGS = __CLASS__;

    private static $settings = array();
    private static $_instance = null;
    private function __construct() {}
    private function __clone() {}

    static function activate() { add_option( self::SETTINGS, array() ); }
    static function uninstall() { delete_option(self::SETTINGS); }

    private static function include_required_classes()
    {
        $classes = array(
            'Example_List_Table' => 'wp-list-table.php',
            'WP_Admin_Page'      => 'wp-admin-page.php',
            'WP_Admin_Forms'     => 'wp-admin-forms.php',
            'WP_Post_Boxes'      => 'wp-post-boxes.php',
            );

        foreach ($classes as $classname => $dir) {
            if( ! class_exists($classname) ) {
                require_once PLUGINNAME_DIR . '/includes/classes/' . $dir;
            }
        }

        // includes
        require_once PLUGINNAME_DIR . '/includes/register-post_type.php';
        require_once PLUGINNAME_DIR . '/includes/admin-page.php';
    }

    public static function get_instance()
    {
        if( ! self::$_instance ) {
            load_plugin_textdomain( '_plugin', false, PLUGINNAME_DIR . '/languages/' );
            self::$settings = get_option( self::SETTINGS, array() );
            self::include_required_classes();

            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get( $prop_name )
    {
        return isset( self::$settings[ $prop_name ] ) ? self::$settings[ $prop_name ] : false;
    }
}
