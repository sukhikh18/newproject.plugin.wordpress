<?php
/**
 * Register plugin actions
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

use NikolayS93\WPAdminPage\Page;
use NikolayS93\WPAdminPage\Section;
use NikolayS93\WPAdminPage\Metabox;

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
	 * Initialize this plugin
	 */
	public static function init() {
		$pagetitle = __( 'New plugin', DOMAIN );
		$menuname  = __( 'Example', DOMAIN );

		self::register_plugin_page(
			$pagetitle,
			array(
				'parent'      => '', // for ex. woocommerce.
				'menu'        => $menuname,
				'permissions' => 'manage_options',
				'columns'     => 2,
			)
		);
	}

	/**
	 * Register new admin menu item
	 *
	 * @param  string $pagename  h1 title on plugin page.
	 * @param  array  $pageprops page properties @see NikolayS93\WPAdminPage\Page.
	 * @return Page $Page
	 */
	public static function register_plugin_page( $pagename = '', $pageprops = array() ) {
		$page = new Page( Option::get_option_name(), $pagename, $pageprops );

		$page->set_content( array( __CLASS__, 'plugin_page_content' ) );
		$page->set_assets( array( __CLASS__, 'plugin_page_assets' ) );

		$page->add_section(
			new Section(
				'section',
				__( 'Section', DOMAIN ),
				IO::get_template( 'admin/template/section' )
			)
		);

		$page->add_metabox(
			new Metabox(
				'metabox',
				__( 'MetaBox', DOMAIN ),
				IO::get_template( 'admin/template/metabox' ),
				$position = 'side',
				$priority = 'high'
			)
		);

		return $page;
	}

	/**
	 * [@todo write plugin_page_content description]
	 */
	public static function plugin_page_content() {
		return IO::get_template( 'admin/template/menu-page', true );
	}

	/**
	 * [@todo write plugin_page_assets description]
	 */
	public static function plugin_page_assets() {
	}
}
