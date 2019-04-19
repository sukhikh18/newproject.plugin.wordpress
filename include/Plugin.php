<?php

namespace NikolayS93\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

/**
 * abstract
 */
class Plugin
{
    /**
     * @var array Commented data about plugin in root file
     */
    protected static $data;

    static function uninstall() { delete_option( static::get_option_name() ); }
    static function activate()
    {
        add_option( static::get_option_name(), array() );
    }

    /**
     * Get data about this plugin
     * @param  string|null $arg array key (null for all data)
     * @return mixed
     */
    public static function get_plugin_data( $arg = null )
    {
        /** Fill if is empty */
        if( empty(static::$data) ) {
            static::$data = get_plugin_data(PLUGIN_FILE);
            load_plugin_textdomain( static::$data['TextDomain'], false, basename(PLUGIN_DIR) . '/languages/' );
        }

        /** Get by key */
        if( $arg ) {
            return isset( static::$data[ $arg ] ) ? static::$data[ $arg ] : null;
        }

        /** Get all */
        return static::$data;
    }

    /**
     * Get option name for a options in the Wordpress database
     */
    public static function get_option_name( $context = 'admin' )
    {
        $option_name = DOMAIN;
        if( 'admin' == $context ) $option_name.= '_adm';

        return apply_filters("get_{DOMAIN}_option_name", $option_name, $context);
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
     * @param  string  $prop_name Ключ опции плагина или null (вернуть опцию целиком)
     * @param  mixed   $default   Что возвращать, если параметр не найден
     * @return mixed
     */
    public static function get( $prop_name = null, $default = false, $context = 'admin' )
    {
        $option_name = static::get_option_name($context);

        /**
         * Получает настройку из кэша или из базы данных
         * @link https://codex.wordpress.org/Справочник_по_функциям/get_option
         * @var mixed
         */
        $option = get_option( $option_name, $default );
        $option = apply_filters( "get_{DOMAIN}_option", $option );

        if( !$prop_name || 'all' == $prop_name ) return !empty( $option ) ? $option : $default;

        return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
    }

    /**
     * Установит параметр в опцию плагина
     * @todo Подумать, может стоит сделать $autoload через фильтр, а не параметр
     *
     * @param mixed  $prop_name Ключ опции плагина || array(параметр => значение)
     * @param string $value     значение (если $prop_name не массив)
     * @param string $context
     * @return bool             Совершились ли обновления @see update_option()
     */
    public static function set( $prop_name, $value = '', $context = 'admin' )
    {
        if( !$prop_name ) return;
        if( $value && !(string) $prop_name ) return;
        if( !is_array($prop_name) ) $prop_name = array((string)$prop_name => $value);

        $option = static::get(null, false, $context);

        foreach ($prop_name as $prop_key => $prop_value)
        {
            $option[ $prop_key ] = $prop_value;
        }

        if( !empty($option) ) {
            $option_name = static::get_option_name($context);
            $autoload = null;
            if( 'admin' == $context ) $autoload = 'no';

            return update_option( $option_name, $option, $autoload );
        }

        return false;
    }
}
