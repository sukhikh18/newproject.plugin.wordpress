<?php

namespace CDevelopers\PLUGINNAME;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

class Utils {
    const SETTINGS = 'PLUGINNAME';

    private $settings = array();
    private static $_instance = null;
    private function __construct() {}
    private function __clone() {}

    static function activate() { add_option( self::SETTINGS, array() ); }
    static function uninstall() { delete_option(self::SETTINGS); }

    /**
     * Если вы не знаете что это за класс вам нечего здесь делать
     */
    public static function get_instance()
    {
        if( ! self::$_instance ) {
            self::$_instance = new self();
            self::$_instance->initialize();
        }

        return self::$_instance;
    }

    private function initialize()
    {
        load_plugin_textdomain( '_plugin', false, DIR . '/languages/' );
        $this->settings = get_option( self::SETTINGS, array() );
        self::include_required_classes();
    }

    private static function include_required_classes()
    {
        $classes = array(
            'Example_List_Table' => 'wp-list-table.php',
            'WP_Admin_Page'      => 'wp-admin-page.php',
            'WP_Admin_Forms'     => 'wp-admin-forms.php',
            'WP_Post_Boxes'      => 'wp-post-boxes.php',
            );

        foreach ($classes as $classname => $dir) {
            if( ! class_exists($classname) ) {
                require_once DIR . '/includes/classes/' . $dir;
            }
        }

        // includes
        require_once DIR . '/includes/register-post_type.php';
        require_once DIR . '/includes/admin-page.php';
    }

    /**
     * Простой способ получить настройку из $this->settings (Если в файле используется лишь один раз)
     */
    public static function _get( $prop_name )
    {
        $self = self::get_instance();
        $self->get( $prop_name );
    }

    /**
     * Получает настройку из $this->settings
     */
    public function get( $prop_name )
    {
        if( 'all' === $prop_name ) {
            if( $this->settings )
                return $this->settings;

            return false;
        }

        return isset( $this->settings[ $prop_name ] ) ? $this->settings[ $prop_name ] : false;
    }

    /**
     * Записываем ошибку
     */
    public static function write_debug( $msg, $dir )
    {
        if( ! defined('WP_DEBUG_LOG') || ! WP_DEBUG_LOG )
            return;

        $dir = str_replace(DIR, '', $dir);
        $msg = str_replace(DIR, '', $msg);

        $date = new \DateTime();
        $date_str = $date->format(\DateTime::W3C);

        $handle = fopen(DIR . "/debug.log", "a+");
        fwrite($handle, "[{$date_str}] {$msg} ({$dir})\r\n");
        fclose($handle);
    }

    /**
     * Загружаем файл если существует
     */
    public static function load_file_if_exists( $file_array )
    {
        $cant_be_loaded = __('The file %s can not be included', LANG);
        if( is_array( $file_array ) ) {
            foreach ( $file_array as $id => $path ) {
                if ( ! is_readable( $path ) ) {
                    self::write_debug(sprintf($cant_be_loaded, $path), __FILE__);
                    continue;
                }

                require_once( $path );
            }
        }
        else {
            if ( ! is_readable( $file_array ) ) {
                self::write_debug(sprintf($cant_be_loaded, $file_array), __FILE__);
                return false;
            }

            require_once( $file_array );
        }
    }
}