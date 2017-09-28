<?php
/*
Plugin Name: Новый плагин
Plugin URI:
Description:
Version: 0.0
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('PLUGINNAME_DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );

register_activation_hook( __FILE__, array( 'PLUGINNAME', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'PLUGINNAME', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'PLUGINNAME', 'uninstall' ) );

add_action( 'plugins_loaded', array('PLUGINNAME', 'init'), 10 );
class PLUGINNAME
{
    const SETTINGS = __CLASS__;

    public static $settings = array();

    static function activate()
    {
        add_option( self::SETTINGS, array() );
    }

    static function uninstall()
    {
        delete_option(self::SETTINGS);
    }

    public static function init()
    {
        self::$settings = get_option( self::SETTINGS, array() );
        self::include_required_classes();
    }

    private static function include_required_classes(){
        // Classes
        require_once PLUGINNAME_DIR . '/includes/classes/wp-list-table.php';
        require_once PLUGINNAME_DIR . '/includes/classes/wp-admin-page.php';
        require_once PLUGINNAME_DIR . '/includes/classes/wp-admin-forms.php';

        // includes
        require_once PLUGINNAME_DIR . '/includes/admin-list-page.php';
        require_once PLUGINNAME_DIR . '/includes/admin-edit-page.php';
    }
}
