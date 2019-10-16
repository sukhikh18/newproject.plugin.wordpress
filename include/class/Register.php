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
	 * @return Page $Page
	 */
	public function register_plugin_page() {
		$plugin = plugin();

		$page = new Page(
			$plugin->get_option_name(),
			__( 'New Plugin name Title', Plugin::DOMAIN ),
			array(
				'parent'      => '', // for ex. woocommerce.
				'menu'        => __( 'Example', Plugin::DOMAIN ),
				'permissions' => $plugin->get_permissions(),
				'columns'     => 2,
			)
		);

		$page->set_content(
			function () use ( $plugin ) {
				include_plugin_file( $plugin->get_template( 'admin/template/menu-page' ) );
			}
		);

		$page->add_section(
			new Section(
				'section',
				__( 'Section', Plugin::DOMAIN ),
				$plugin->get_template( 'admin/template/section' )
			)
		);

		$page->add_metabox(
			new Metabox(
				'metabox',
				__( 'MetaBox', Plugin::DOMAIN ),
				$plugin->get_template( 'admin/template/metabox' ),
				$position = 'side',
				$priority = 'high'
			)
		);

		$page->set_assets(
			function () use ( $plugin ) {
			}
		);

		return $page;
	}
}
