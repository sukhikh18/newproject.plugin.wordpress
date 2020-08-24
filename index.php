<?php
/**
 * Plugin Name: New plugin
 * Plugin URI: https://github.com/nikolays93
 * Description: New plugin boilerplate
 * Version: 0.1.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: _plugin
 * Domain Path: /languages/
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You shall not pass' );
}

// Plugin top doc properties.
$plugin_data = get_plugin_data( __FILE__ );

if ( ! defined( __NAMESPACE__ . '\PLUGIN_DIR' ) ) {
	define( __NAMESPACE__ . '\PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
}

if ( ! defined( __NAMESPACE__ . 'DOMAIN' ) ) {
	define( __NAMESPACE__ . '\DOMAIN', $plugin_data['TextDomain'] );
}

if ( ! defined( __NAMESPACE__ . 'PREFIX' ) ) {
	define( __NAMESPACE__ . '\PREFIX', DOMAIN . '_' );
}

// load plugin languages.
load_plugin_textdomain( DOMAIN, false, basename( PLUGIN_DIR ) . $plugin_data['DomainPath'] );

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once PLUGIN_DIR . 'vendor/autoload.php';
require_once PLUGIN_DIR . 'includes/autoload.php';

register_activation_hook( __FILE__, array( Register::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Register::class, 'deactivate' ) );
register_uninstall_hook( __FILE__, array( Register::class, 'uninstall' ) );

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action(
	'plugins_loaded',
	function() {
		Register::register_plugin_page(
			__( 'New plugin', DOMAIN ),
			array(
				'parent'      => '', // for ex. woocommerce.
				'menu'        => __( 'Example', DOMAIN ),
				'permissions' => 'manage_options',
				'columns'     => 2,
			)
		);
	},
	10
);
