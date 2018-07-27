<?php

/*
Plugin Name: Новый плагин
Plugin URI: https://github.com/nikolays93
Description:
Version: 0.1.1
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Хуки плагина:
 * $pageslug . _after_title (default empty hook)
 * $pageslug . _before_form_inputs (default empty hook)
 * $pageslug . _inside_page_content
 * $pageslug . _inside_side_container
 * $pageslug . _inside_advanced_container
 * $pageslug . _after_form_inputs (default empty hook)
 * $pageslug . _after_page_wrap (default empty hook)
 *
 * Фильтры плагина:
 * "get_{DOMAIN}_option_name" - имя опции плагина
 * "get_{DOMAIN}_option" - значение опции плагина
 * "load_{DOMAIN}_file_if_exists" - информация полученная с файла
 * "get_{DOMAIN}_plugin_dir" - Дирректория плагина (доступ к файлам сервера)
 * "get_{DOMAIN}_plugin_url" - УРЛ плагина (доступ к внешним файлам)
 *
 * $pageslug . _form_action - Аттрибут action формы на странице настроек плагина
 * $pageslug . _form_method - Аттрибут method формы на странице настроек плагина
 */

namespace Nikolays93\NSPACE;

use NikolayS93\WPAdminPageBeta as AdminPage;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

const PLUGIN_DIR = __DIR__;
const DOMAIN = '_plugin';

// активации и деактивации плагина @see activate(), uninstall();
require PLUGIN_DIR . '/include/utils.php';

class Plugin
{
    private function __construct() {}

    static function activate() { add_option( Utils::get_option_name(), array() ); }
    static function uninstall() { delete_option( Utils::get_option_name() ); }

    public static function _admin_assets()
    {
    }


    public static function setAdminMenuPage()
    {
        $page = new AdminPage\Page(
            Utils::get_option_name(),
            __('Pluginname Title', DOMAIN),
            array(
                'parent'      => false,
                'menu'        => __('Example', DOMAIN),
                // 'validate'    => array($this, 'validate_options'),
                'permissions' => 'manage_options',
                'columns'     => 2,
            )
        );

        $page->set_assets( array(__CLASS__, '_admin_assets') );

        $page->set_content( function() {
            include Utils::get_admin_template('menu-page.php');
        } );

        $page->add_section( new AdminPage\Section(
            'Section',
            __('Section'),
            function() {
                include Utils::get_admin_template('section.php');
            }
        ) );

        $metabox1 = new AdminPage\Metabox(
            'metabox1',
            __('metabox1', DOMAIN),
            function() {
                include Utils::get_admin_template('metabox1.php');
            },
            $position = 'side',
            $priority = 'high'
        );

        $page->add_metabox( $metabox1 );

        $metabox2 = new AdminPage\Metabox(
            'metabox2',
            __('metabox2', DOMAIN),
            function() {
                include Utils::get_admin_template('metabox2.php');
            },
            $position = 'side',
            $priority = 'high'
        );

        $page->add_metabox( $metabox2 );
    }

    public static function initialize()
    {
        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );

        self::setAdminMenuPage();
    }
}

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'initialize' ), 10 );
