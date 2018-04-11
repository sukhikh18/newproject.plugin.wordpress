<?php

/**
 * Plugin Name: WP_Admin_Forms
 * Description: Render a custom admin forms.
 * Version: 1.0.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: nikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace NikolayS93\WPAdminForm;

if ( ! defined( 'ABSPATH' ) )
  exit; // With wordpress only

if( !class_exists('NikolayS93\WPAdminForm\Version') ) {
    include_once __DIR__ . '/src/Version.php';
    include_once __DIR__ . '/src/Preset.php';
    include_once __DIR__ . '/src/Util.php';
    include_once __DIR__ . '/src/Active.php';
    include_once __DIR__ . '/src/Form.php';
    include_once __DIR__ . '/src/Field.php';
    include_once __DIR__ . '/src/Input.php';

    // include_once __DIR__ . '/test/front-var-dump.php';
}
