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

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class Plugin {
	const FILE = __FILE__;

	const DIR = __DIR__;

	const DOMAIN = '_plugin';

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
		$option_name = $suffix ? "{self::DOMAIN}_{$suffix}" : self::DOMAIN;

		return apply_filters( "get_{self::DOMAIN}_option_name", $option_name, $suffix );
	}

	/**
	 * Get plugin url
	 *
	 * @param string $path path must be started from / (also as __DIR__)
	 *
	 * @return string
	 */
	public static function get_plugin_url( $path = '' ) {
		$url = plugins_url( basename( self::DIR ) ) . $path;

		return apply_filters( "get_{self::DOMAIN}_plugin_url", $url, $path );
	}

	/**
	 * Register all of the needed hooks and actions.
	 */
	public function setup() {
		require_once self::DIR . '/vendor/autoload.php';
		// require ./../;
		// Allow people to change what capability is required to use this plugin.
		$this->permissions = apply_filters( 'plugin_cap', $this->permissions );

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
			Plugin::get_template( 'admin/template/menu-page', false, array(), true );
		} );

		$Page->add_section( new Admin\Section(
			'Section',
			__( 'Section', self::DOMAIN ),
			function () {
				Plugin::get_template( 'admin/template/section', false, array(), true );
			}
		) );

		$Page->add_metabox( new Admin\Metabox(
			'metabox',
			__( 'metabox', self::DOMAIN ),
			function () {
				Plugin::get_template( 'admin/template/metabox', false, array(), true );
			},
			$position = 'side',
			$priority = 'high'
		) );

		$Page->set_assets( function () {
		} );

		return $Page;
	}

	/**
	 * Get plugin template (and include with $data maybe)
	 *
	 * @param  [type]  $template [description]
	 * @param boolean $slug [description]
	 * @param array $data @todo
	 *
	 * @return string|false
	 */
	public static function get_template( $template, $slug = false, $data = array(), $include = false ) {
		if ( false !== strripos( $template, '.' ) ) {
			// @todo repair it (filename maybe include twice and more dots)
			@list( $template, $ext ) = explode( '.', $template );
		} else {
			$ext = 'php';
		}

		$paths = array();
		// push paths to find list
		if ( $slug ) {
			array_push( $paths, self::DIR . "/$template-$slug.$ext" );
		}
		array_push( $paths, self::DIR . "/$template.$ext" );

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) && is_readable( $path ) ) {
				// extract to included file
				if ( $data ) {
					extract( $data );
				}

				if ( $include ) {
					include $path;
				}

				return $path;
			}
		}

		return false;
	}

	/**
	 * Получает параметр из опции плагина
	 *
	 * @param string $prop_name Ключ опции плагина или null (вернуть опцию целиком)
	 * @param mixed $default Что возвращать, если параметр не найден
	 *
	 * @return mixed
	 * @todo Добавить фильтр
	 *
	 */
	public static function get_setting( $prop_name = null, $default = false, $context = '' ) {
		$option_name = static::get_option_name( $context );

		/**
		 * Get plugin setting from cache or database
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 */
		$option = apply_filters( "get_{DOMAIN}_option", get_option( $option_name, $default ) );

		if ( ! $prop_name || 'all' == $prop_name ) {
			return ! empty( $option ) ? $option : $default;
		}

		return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
	}

	/**
	 * Установит параметр в опцию плагина
	 *
	 * @param string|array $prop_name Ключ опции плагина || array(параметр => значение)
	 * @param string $value значение (если $prop_name не массив)
	 * @param string $context
	 *
	 * @return bool             Совершились ли обновления @see update_option()
	 * @todo Подумать, может стоит сделать $autoload через фильтр, а не параметр
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
			$autoload    = null;
			if ( 'settings' == $context ) {
				$autoload = 'no';
			}

			return update_option( $option_name, $option, $autoload );
		}

		return false;
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
