<?php
/**
 * Plugin Name: New plugin
 * Plugin URI: https://github.com/nikolays93
 * Description: New plugin boilerplate
 * Version: 0.0.7
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

if ( ! defined( __NAMESPACE__ . '\PLUGIN_DIR' ) ) {
	define( __NAMESPACE__ . '\PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
}

if ( ! function_exists( 'include_plugin_file' ) ) {
	/**
	 * Safe dynamic expression include.
	 *
	 * @param string $path relative path.
	 */
	function include_plugin_file( $path ) {
		if ( 0 !== strpos( $path, PLUGIN_DIR ) ) {
			$path = PLUGIN_DIR . $path;
		}
		if ( is_file( $path ) && is_readable( $path ) ) {
			return include $path; // phpcs:ignore
		}

		return false;
	}
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! include_once PLUGIN_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php' ) {
	array_map(
		__NAMESPACE__ . '\include_plugin_file',
		array(
			'include/class/Creational/Singleton.php',
			'include/class-plugin.php',
			'include/class-register.php',
		)
	);
}

/**
 * Returns the single instance of this plugin, creating one if needed.
 *
 * @return Plugin
 */
function plugin() {
	return Plugin::get_instance();
}

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\Plugin', 10 );
add_action(
	'plugins_loaded',
	function () {
		$register = new Register();
		$register->register_plugin_page();
	},
	20
);

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'activate' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'uninstall' ) );
