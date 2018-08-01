<?php

/*
 * Plugin Name: New plugin
 * Plugin URI: https://github.com/nikolays93
 * Description: New plugin boilerplate
 * Version: 0.1.2
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: nikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: _plugin
 * Domain Path: /languages/
 */

/**
 * Фильтры плагина:
 * "get_{Text Domain}_option_name" - имя опции плагина
 * "get_{Text Domain}_option" - значение опции плагина
 * "get_{Text Domain}_plugin_url" - УРЛ плагина
 */

namespace Nikolays93\_plugin;

use NikolayS93\WPAdminPageBeta as AdminPage;

if ( ! defined( 'ABSPATH' ) )
  exit('You shall not pass'); // disable direct access

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (version_compare(PHP_VERSION, '5.3') < 0) {
    throw new \Exception('Plugin requires PHP 5.3 or above');
}

class Plugin
{
    protected static $data;
    protected static $options;

    private function __construct() {}

    static function activate() { add_option( self::get_option_name(), array() ); }
    static function uninstall() { delete_option( self::get_option_name() ); }

    /**
     * Получает название опции плагина
     *     Чаще всего это название плагина
     *     Чаще всего оно используется как название страницы настроек
     * @return string
     */
    public static function get_option_name()
    {
        return apply_filters("get_{DOMAIN}_option_name", DOMAIN);
    }

    public static function _admin_assets()
    {
    }

    public static function admin_menu_page()
    {
        $page = new AdminPage\Page(
            Utils::get_option_name(),
            __('New Plugin name Title', DOMAIN),
            array(
                'parent'      => false,
                'menu'        => __('Example', DOMAIN),
                // 'validate'    => array($this, 'validate_options'),
                'permissions' => 'manage_options',
                'columns'     => 2,
            )
        );

        $page->set_assets( array(__CLASS__, '_admin_assets') );

        $page->set_content( function() {
            Utils::get_admin_template('menu-page.php', false, $inc = true);
        } );

        $page->add_section( new AdminPage\Section(
            'Section',
            __('Section'),
            function() {
                Utils::get_admin_template('section.php', false, $inc = true);
            }
        ) );

        $metabox1 = new AdminPage\Metabox(
            'metabox1',
            __('metabox1', DOMAIN),
            function() {
                Utils::get_admin_template('metabox1.php', false, $inc = true);
            },
            $position = 'side',
            $priority = 'high'
        );

        $page->add_metabox( $metabox1 );

        $metabox2 = new AdminPage\Metabox(
            'metabox2',
            __('metabox2', DOMAIN),
            function() {
                Utils::get_admin_template('metabox2.php', false, $inc = true);
            },
            $position = 'side',
            $priority = 'high'
        );

        $page->add_metabox( $metabox2 );
    }

    public static function define()
    {
        self::$data = get_plugin_data(__FILE__);

        if( !defined(__NAMESPACE__ . '\DOMAIN') )
            define(__NAMESPACE__ . '\DOMAIN', self::$data['TextDomain']);

        if( !defined(__NAMESPACE__ . '\PLUGIN_DIR') )
            define(__NAMESPACE__ . '\PLUGIN_DIR', __DIR__);
    }

    public static function initialize()
    {
        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );

        require PLUGIN_DIR . '/include/utils.php';

        self::admin_menu_page();
    }
}

Plugin::define();

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'initialize' ), 10 );
