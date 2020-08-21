<?php
/**
 * Plugin autoload register
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

spl_autoload_register(
	function ( $class_name ) {
		// If the specified $class_name does not include our namespace, duck out.
		if ( false === strpos( $class_name, __NAMESPACE__ ) ) {
			return;
		}

		// Path to classes directory.
		$class_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
		// Lower class name without namespace.
		$class_path = strtolower( str_replace( __NAMESPACE__, '', $class_name ) );
		// Split the class name into an array to read the namespace and class.
		$class_parts = array_filter( explode( '\\', $class_path ) );

		$class_basename = &$class_parts[ count( $class_parts ) ];
		$class_basename = 'class-' . str_ireplace( '_', '-', $class_basename ) . '.php';

		require_once $class_dir . implode( DIRECTORY_SEPARATOR, $class_parts );
	}
);
