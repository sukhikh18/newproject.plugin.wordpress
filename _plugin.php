<?php

/*
 * Plugin Name: New plugin
 * Plugin URI: https://github.com/nikolays93
 * Description: New plugin boilerplate
 * Version: 0.0.3
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: _plugin
 * Domain Path: /languages/
 */

namespace NikolayS93\Plugin;

use NikolayS93\WPAdminPage as Admin;

if ( !defined( 'ABSPATH' ) ) exit('You shall not pass');

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (version_compare(PHP_VERSION, '5.4') < 0) {
    throw new \Exception('Plugin requires PHP 5.4 or above');
}

class Plugin
{
    use Creational\Singleton;

    /**
     * @var array Commented data on this file top
     */
    protected $data;

    /**
     * @var array Field on wo_option for this plugin
     */
    protected $options;

    function __init()
    {
        /**
         * Define required plugin data
         */
        $this->data = get_plugin_data(__FILE__);

        if( !defined(__NAMESPACE__ . '\DOMAIN') )
            define(__NAMESPACE__ . '\DOMAIN', $this->data['TextDomain']);

        if( !defined(__NAMESPACE__ . '\PLUGIN_DIR') )
            define(__NAMESPACE__ . '\PLUGIN_DIR', __DIR__);

        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );

        $autoload = __DIR__ . '/vendor/autoload.php';
        if( file_exists($autoload) ) include $autoload;

        /**
         * include required files
         */
        require PLUGIN_DIR . '/include/utils.php';
        // require PLUGIN_DIR . '/include/class-plugin-queries.php';
        // require PLUGIN_DIR . '/include/class-plugin-routes.php';
        // require PLUGIN_DIR . '/include/class-plugin-widget.php';
    }

    static function activate() { add_option( self::get_option_name(), array() ); }
    static function uninstall() { delete_option( self::get_option_name() ); }

    // public static function _admin_assets()
    // {
    // }

    public function addMenuPage()
    {
        $page = new Admin\Page(
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

        // $page->set_assets( array(__CLASS__, '_admin_assets') );

        $page->set_content( function() {
            Utils::get_admin_template('menu-page.php', false, $inc = true);
        } );

        $page->add_section( new Admin\Section(
            'Section',
            __('Section'),
            function() {
                Utils::get_admin_template('section.php', false, $inc = true);
            }
        ) );

        $metabox1 = new Admin\Metabox(
            'metabox1',
            __('metabox1', DOMAIN),
            function() {
                Utils::get_admin_template('metabox1.php', false, $inc = true);
            },
            $position = 'side',
            $priority = 'high'
        );

        $page->add_metabox( $metabox1 );

        $metabox2 = new Admin\Metabox(
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
}

add_action( 'plugins_loaded', function() {

    $Plugin = Plugin::getInstance();
    $Plugin->addMenuPage();

    // $PluginRoutes = PluginRoutes::getInstance();
    // add_action( 'init', array($PluginRoutes, '__register') );

    // $PluginQueries = PluginQueries::getInstance();
    // add_action( 'pre_get_posts', array($PluginQueries, '__register') );

    // add_action( 'widgets_init', array(__NAMESPACE__ . '\PluginWidget', '__register') );
}, 10 );

// add_action( 'plugins_loaded', array( $Plugin, 'admin_menu_page' ), 10 );
// add_action( 'admin_init', 'seo_filter_taxanomy_actions' );

// register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
// register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );
