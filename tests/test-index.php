<?php
/**
 * Class PluginTest
 *
 * @package Newproject.wordpress.plugin/
 */

/**
 * Sample test case.
 */
class PluginTest extends WP_UnitTestCase {

	const PLUGIN_NAME = 'Plugin';

	private $Plugin;

	public function setUp() {
		$this->Plugin = Plugin();
	}

	public function test_instance() {
		$this->assertEquals( get_class($this->Plugin::instance()), self::PLUGIN_NAME );
	}

	public function test_activate() {
		/** @todo */
		$this->assertTrue( true );
	}

	public function test_deactivate() {
		/** @todo */
		$this->assertTrue( true );
	}

	public function test_uninstall() {
		/** @todo */
		$this->assertTrue( true );
	}

	public function test_get_option_name() {
		$filter_name = $this->Plugin::PREFIX . 'get_option_name';
		$option_name = 'test';

		$this->assertEquals( $this->Plugin::get_option_name(),
			apply_filters( $filter_name, $this->Plugin::DOMAIN, null ) );

		$this->assertEquals( $this->Plugin::get_option_name( $option_name ),
			apply_filters( $filter_name, $this->Plugin::PREFIX . $option_name, $option_name ) );
	}

	public function test_get_plugin_url() {
		$filter_name = $this->Plugin::PREFIX . 'get_plugin_url';
		$plugins_url = plugins_url();
		$plugin_url = $plugins_url . '/' . basename( $this->Plugin::DIR );

		$path = '/test/';
		$path2 = 'test/';
		$required_url = $plugin_url . $path;

		$this->assertEquals( $this->Plugin::get_plugin_url( $path ),
			apply_filters( $filter_name, $required_url, $path ) );
		$this->assertEquals( $this->Plugin::get_plugin_url( $path2 ),
			apply_filters( $filter_name, $required_url, $path ) );
	}

	public function test_get_template() {
		$template = 'admin/template/menu-page';
		$tpl = $this->Plugin::DIR . "/$template";

		$this->assertFalse( $this->Plugin::get_template('fail/template/path') );
		$this->assertEquals( $this->Plugin::get_template($template), $tpl . '.php' );
		$this->assertEquals( $this->Plugin::get_template('/' . $template . '.php'), $tpl . '.php' );
	}

	public function test_get_setting() {
		/** @todo learn to stay awake for 24 hours */
		$this->assertTrue( true );
	}

	public function test_set_setting() {
		/** @todo */
		$this->assertTrue( true );
	}

	public function test_register_plugin_page() {
		$get_method = self::PLUGIN_NAME;
		$this->assertEquals( get_class($get_method()->register_plugin_page()),
			'NikolayS93\WPAdminPage\Page' );
	}
}
