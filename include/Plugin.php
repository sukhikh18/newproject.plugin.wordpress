<?php

namespace NikolayS93\Plugin;

use NikolayS93\WPAdminPage as Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

class Plugin
{
    use Creational\Singleton;

    /**
     * @var array Commented data on this file top
     */
    protected $data;

    /**
     * @var array Field on wo_option for this plugin
     */
    protected $options;

    function __init()
    {
        /**
         * Define required plugin data
         */
        if( !defined(__NAMESPACE__ . '\DOMAIN') )
            define(__NAMESPACE__ . '\DOMAIN', static::get_plugin_data('TextDomain'));

        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );

        $autoload = PLUGIN_DIR . '/vendor/autoload.php';
        if( file_exists($autoload) ) include $autoload;

        /**
         * include required files
         */
        // require PLUGIN_DIR . '/include/class-plugin-queries.php';
        // require PLUGIN_DIR . '/include/class-plugin-routes.php';
        // require PLUGIN_DIR . '/include/class-plugin-widget.php';
    }

    function addMenuPage( $pagename = '', $args = array() )
    {
        $args = wp_parse_args( $args, array(
            'parent'      => false,
            'menu'        => __('New plugin', DOMAIN),
            // 'validate'    => array($this, 'validate_options'),
            'permissions' => 'manage_options',
            'columns'     => 2,
        ) );

        $Page = new Admin\Page( static::get_option_name(), $pagename, $args );

        return $Page;
    }

    static function activate() { add_option( static::get_option_name(), array() ); }
    static function uninstall() { delete_option( static::get_option_name() ); }

    public static function get_plugin_data( $arg = '' )
    {
        $Plugin = static::getInstance();
        if( !$Plugin->data ) {
            $Plugin->data = get_plugin_data(PLUGIN_FILE);
        }

        if( $arg ) {
            return isset( $Plugin->data[ $arg ] ) ? $Plugin->data[ $arg ] : '';
        }

        return $Plugin->data;
    }

    /**
     * Get option name for a options in the Wordpress database
     */
    public static function get_option_name()
    {
        return apply_filters("get_{DOMAIN}_option_name", DOMAIN);
    }

        /**
     * Получает url (адресную строку) до плагина
     * @param  string $path путь должен начинаться с / (по аналогии с __DIR__)
     * @return string
     */

    public static function get_plugin_url( $path = '' )
    {
        $url = plugins_url( basename(PLUGIN_DIR) ) . $path;

        return apply_filters( "get_{DOMAIN}_plugin_url", $url, $path );
    }

    /**
     * [get_template description]
     * @param  [type]  $template [description]
     * @param  boolean $slug     [description]
     * @param  array   $data     @todo
     * @return string            [description]
     */
    public static function get_template( $template, $slug = false, $data = array() )
    {
        $filename = '';

        if ($slug) $templates[] = PLUGIN_DIR . '/' . $template . '-' . $slug;
        $templates[] = PLUGIN_DIR . '/' . $template;

        foreach ($templates as $template)
        {
            if( ($filename = $template . '.php') && file_exists($filename) ) {
                break;
            }
            elseif( ($filename = $template) && file_exists($filename) ) {
                break;
            }
        }

        return $filename;
    }

    /**
     * [get_admin_template description]
     * @param  string  $tpl     [description]
     * @param  array   $data    [description]
     * @param  boolean $include [description]
     * @return string
     */
    public static function get_admin_template( $tpl = '', $data = array(), $include = false )
    {
        $filename = static::get_template('admin/template/' . $tpl, false, $data);

        if( $data ) extract($data);

        if( $filename && $include ) {
            include $filename;
        }

        return $filename;
    }

    /**
     * Получает параметр из опции плагина
     * @todo Добавить фильтр
     *
     * @param  string  $prop_name Ключ опции плагина или 'all' (вернуть опцию целиком)
     * @param  mixed   $default   Что возвращать, если параметр не найден
     * @return mixed
     */
    public function get( $prop_name, $default = false )
    {
        $option = $this->get_option();
        if( 'all' === $prop_name ) {
            if( is_array($option) && count($option) ) {
                return $option;
            }

            return $default;
        }

        return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
    }

    /**
     * Установит параметр в опцию плагина
     * @todo Подумать, может стоит сделать $autoload через фильтр, а не параметр
     *
     * @param mixed  $prop_name Ключ опции плагина || array(параметр => значение)
     * @param string $value     значение (если $prop_name не массив)
     * @param string $autoload  Подгружать опцию автоматически @see update_option()
     * @return bool             Совершились ли обновления @see update_option()
     */
    public function set( $prop_name, $value = '', $autoload = null )
    {
        $option = $this->get_option();
        if( ! is_array($prop_name) ) $prop_name = array($prop_name => $value);

        foreach ($prop_name as $prop_key => $prop_value) {
            $option[ $prop_key ] = $prop_value;
        }

        return update_option( static::get_option_name(), $option, $autoload );
    }

    /**
     * Получает настройку из parent::$options || из кэша || из базы данных
     * @param  mixed  $default Что вернуть если опции не существует
     * @return mixed
     */
    private function get_option( $default = array() )
    {
        if( ! $this->options ) {
            $this->options = get_option( static::get_option_name(), $default );
        }

        return apply_filters( "get_{DOMAIN}_option", $this->options );
    }
}
