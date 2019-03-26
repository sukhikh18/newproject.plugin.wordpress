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
if (version_compare(PHP_VERSION, '5.4') < 0) {
    throw new \Exception('Plugin requires PHP 5.4 or above');
}

if( !defined(__NAMESPACE__ . '\PLUGIN_DIR') ) define(__NAMESPACE__ . '\PLUGIN_DIR', __DIR__);
if( !defined(__NAMESPACE__ . '\PLUGIN_FILE') ) define(__NAMESPACE__ . '\PLUGIN_FILE', __FILE__);

require_once ABSPATH . "wp-admin/includes/plugin.php";
require_once PLUGIN_DIR . '/include/Creational/Singleton.php';
require_once PLUGIN_DIR . '/include/Plugin.php';

add_action( 'plugins_loaded', function() {

    $Plugin = Plugin::getInstance();

    // $PluginRoutes = PluginRoutes::getInstance();
    // add_action( 'init', array($PluginRoutes, '__register') );

    // $PluginQueries = PluginQueries::getInstance();
    // add_action( 'pre_get_posts', array($PluginQueries, '__register') );

    // add_action( 'widgets_init', array(__NAMESPACE__ . '\PluginWidget', '__register') );

    $Page = $Plugin->addMenuPage(__('New Plugin name Title', DOMAIN), array(
        'menu' => __('Example', DOMAIN),
    ));

    // $Page->set_assets( function() {} );

    $Page->set_content( function() {
        Plugin::get_admin_template('menu-page', false, $inc = true);
    } );

    $Page->add_section( new Admin\Section(
        'Section',
        __('Section'),
        function() {
            Plugin::get_admin_template('section', false, $inc = true);
        }
    ) );

    $metabox = new Admin\Metabox(
        'metabox',
        __('metabox', DOMAIN),
        function() {
            Plugin::get_admin_template('metabox', false, $inc = true);
        },
        $position = 'side',
        $priority = 'high'
    );

    $Page->add_metabox( $metabox );
}, 10 );

// register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
// register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );
