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
				realpath( PLUGIN_DIR . 'admin/template/section.php' )
			)
		);

		$page->add_metabox(
			new Metabox(
				'metabox',
				__( 'MetaBox', DOMAIN ),
				realpath( PLUGIN_DIR . 'admin/template/metabox.php' ),
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
		$page_content = realpath( PLUGIN_DIR . 'admin/template/menu-page.php' );

		if ( $page_content ) {
			require $page_content;
		}
	}

	/**
	 * [@todo write plugin_page_assets description]
	 */
	public static function plugin_page_assets() {
	}
}
