<?php
/**
 * IO Plugin methods
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

/**
 * IO plugin class
 */
class IO {

	/**
	 * Get plugin dir (without slash end)
	 *
	 * @param string $path Path to something relative.
	 *
	 * @return string
	 */
	public static function dir( $path = '' ) {
		return PLUGIN_DIR . ltrim( $path, DIRECTORY_SEPARATOR );
	}

	/**
	 * Get file by plugin dir path
	 *
	 * @param string $dir_path [@todo write description].
	 * @param string $filename [@todo write description].
	 *
	 * @return string
	 */
	public static function file( $dir_path, $filename ) {
		return static::dir( $dir_path ) . trim( $filename, DIRECTORY_SEPARATOR );
	}

	/**
	 * Get plugin template path
	 *
	 * @param string $template [@todo write description].
	 * @param bool   $include  Include finded file.
	 *
	 * @return string|false
	 */
	public static function get_template( $template, $include = false ) {
		if ( ! pathinfo( $template, PATHINFO_EXTENSION ) ) {
			$template .= '.php';
		}

		$path = static::dir() . ltrim( $template, '/' );
		if ( file_exists( $path ) && is_readable( $path ) ) {
			if ( $include ) {
				require $path;
			}

			return $path;
		}

		return false;
	}
}
