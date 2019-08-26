<?php

/*
 * Plugin Name: New plugin
 * Plugin URI: https://github.com/nikolays93
 * Description: New plugin boilerplate
 * Version: 0.0.6
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: _plugin
 * Domain Path: /languages/
 */

use NikolayS93\WPAdminPage as Admin;

class Plugin {
	/**
	 * Path to this file
	 */
	const FILE = __FILE__;

	/**
	 * Path to plugin directory
	 */
	const DIR = __DIR__;

	/**
	 * Uniq plugin slug name
	 */
	const DOMAIN = 'plugin';

	/**
	 * Uniq plugin prefix
	 */
	const PREFIX = 'plugin_';

	/**
	 * The capability required to use this plugin.
	 * Please don't change this directly. Use the "regenerate_thumbs_cap" filter instead.
	 *
	 * @var string
	 */
	protected $permissions = 'manage_options';

	/**
	 * The single instance of this plugin.
	 *
	 * @var Plugin this class object
	 */
	private static $instance;

	/**
	 * Prevents the class from being cloned.
	 */
	public function __clone() {
		wp_die( "Don't clone this!" );
	}

	/**
	 * Prints the class from being unserialized and woken up.
	 */
	public function __wakeup() {
		wp_die( "Don't wakeup this!" );
	}

	/**
	 * Creates a new instance of this class if one hasn't already been made
	 * and then returns the single instance of this class.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->setup();
		}

		return self::$instance;
	}

	public static function activate() {
	}

	public static function deactivate() {
	}

	public static function uninstall() {
	}

	/**
	 * Get option name for a options in the Wordpress database
	 */
	public static function get_option_name( $suffix = '' ) {
		$option_name = $suffix ? self::PREFIX . $suffix : self::DOMAIN;

		return apply_filters( self::PREFIX . 'get_option_name', $option_name, $suffix );
	}

	/**
	 * Get plugin url
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function get_plugin_url( $path = '' ) {
		$url = plugins_url( basename( self::DIR ) ) . '/' . ltrim($path, '/');

		return apply_filters( self::PREFIX . 'get_plugin_url', $url, $path );
	}


	/**
	 * Get plugin template path
	 *
	 * @param  [type]  $template [description]
	 *
	 * @return string|false
	 */
	public static function get_template( $template ) {
		if( ! pathinfo($template, PATHINFO_EXTENSION) ) {
			$template .= '.php';
		}

		$path = self::DIR . '/' . ltrim($template, '/');
		if( file_exists( $path ) && is_readable( $path ) ) {
			return $path;
		}

		return false;
	}

	/**
	 * Get plugin setting from cache or database
	 *
	 * @param string $prop_name Option key or null (for a full request)
	 * @param mixed $default What's return if field value not defined.
	 *
	 * @return mixed
	 *
	 */
	public static function get_setting( $prop_name = null, $default = false, $context = '' ) {
		$option_name = static::get_option_name( $context );

		/**
		 * Get field value from wp_options
		 *
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 * @var mixed
		 */
		$option = apply_filters( self::PREFIX . 'get_option',
			get_option( $option_name, $default ) );

		if ( ! $prop_name ) {
			return ! empty( $option ) ? $option : $default;
		}

		return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
	}

	/**
	 * Set new plugin setting
	 *
	 * @param string|array $prop_name Option key || array
	 * @param string $value           value for $prop_name string key
	 * @param string $context
	 *
	 * @return bool                   Is updated @see update_option()
	 *
	 */
	public static function set_setting( $prop_name, $value = '', $context = '' ) {
		if ( ! $prop_name || ( $value && ! (string) $prop_name ) ) {
			return false;
		}

		if ( ! is_array( $prop_name ) ) {
			$prop_name = array( (string) $prop_name => $value );
		}

		$option = static::get_setting( null, false, $context );

		foreach ( $prop_name as $prop_key => $prop_value ) {
			$option[ $prop_key ] = $prop_value;
		}

		if ( ! empty( $option ) ) {
			$option_name = static::get_option_name( $context );
			// Do not auto load for plugin settings (default)
			$autoload    = ! $context ? 'no' : null;

			return update_option( $option_name, $option,
				apply_filters( self::PREFIX . 'autoload', $autoload, $option, $context ) );
		}

		return false;
	}

	/**
	 * Register all of the needed hooks and actions.
	 */
	public function setup() {
		require_once self::DIR . '/vendor/autoload.php';
		// Allow people to change what capability is required to use this plugin.
		$this->permissions = apply_filters( self::PREFIX . 'permissions', $this->permissions );

		// load plugin languages
		load_plugin_textdomain( self::DOMAIN, false,
			basename( self::DIR ) . '/languages/' );

		$this->register_plugin_page();
	}

	/**
	 * Register new admin menu item
	 *
	 * @return $Page NikolayS93\WPAdminPage\Page
	 */
	public function register_plugin_page() {
		/** @var Admin\Page */
		$Page = new Admin\Page(
			Plugin::get_option_name(),
			__( 'New Plugin name Title', self::DOMAIN ),
			array(
				'parent'      => '', // for ex. woocommerce
				'menu'        => __( 'Example', self::DOMAIN ),
				'permissions' => $this->permissions,
				'columns'     => 2,
				// 'validate'    => array($this, 'validate_options'),
			)
		);

		$Page->set_content( function () {
			include Plugin::get_template( 'admin/template/menu-page' );
		} );

		$Page->add_section( new Admin\Section(
			'Section',
			__( 'Section', self::DOMAIN ),
			function () {
				include Plugin::get_template( 'admin/template/section' );
			}
		) );

		$Page->add_metabox( new Admin\Metabox(
			'metabox',
			__( 'metabox', self::DOMAIN ),
			function () {
				include Plugin::get_template( 'admin/template/metabox' );
			},
			$position = 'side',
			$priority = 'high'
		) );

		$Page->set_assets( function () {
		} );

		return $Page;
	}
}

/**
 * Returns the single instance of this plugin, creating one if needed.
 *
 * @return Plugin
 */
function Plugin() {
	return Plugin::instance();
}

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action( 'plugins_loaded', 'Plugin', 10 );

register_activation_hook( Plugin::FILE, array( 'Plugin', 'activate' ) );
register_deactivation_hook( Plugin::FILE, array( 'Plugin', 'deactivate' ) );
register_uninstall_hook( Plugin::FILE, array( 'Plugin', 'uninstall' ) );
