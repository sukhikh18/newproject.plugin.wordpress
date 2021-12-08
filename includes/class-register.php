<?php
/**
 * Register plugin actions
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

/**
 * Class Register
 */
class Register {

	/**
	 * Call this method before activate plugin
	 */
	public static function activate() {
	}

	/**
	 * Call this method before disable plugin
	 */
	public static function deactivate() {
	}

	/**
	 * Call this method before delete plugin
	 */
	public static function uninstall() {
	}

	/**
	 * Register new admin menu item
	 *
	 * @param  string $pagename  h1 title on plugin page.
	 * @param  array  $pageprops page properties @see NikolayS93\WPAdminPage\Page.
	 * @return Page $Page
	 */
	public static function plugin_settings_page( $pagename = '', $pageprops = array() ) {
		new Settings_Page(
			__( 'New plugin', DOMAIN ),
			array(
				'parent'      => '', // for ex. woocommerce.
				'menu'        => __( 'Example', DOMAIN ),
				'permissions' => 'manage_options',
				'columns'     => 2,
			)
		);
	}
}
