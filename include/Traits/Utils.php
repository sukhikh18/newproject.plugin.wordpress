<?php

namespace NikolayS93\Plugin\Traits;

use NikolayS93\Plugin as Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

/**
 * Plugin utils (Used on Plugin only)
 */
trait Utils
{
    public static function get_plugin_data()
    {
        $Plugin = static::getInstance();
        return $Plugin->data;
    }

    /**
     * Get option name for a options in the Wordpress database
     */
    public static function get_option_name()
    {
        return apply_filters("get_{Plugin\DOMAIN}_option_name", Plugin\DOMAIN);
    }

    /**
     * Получает настройку из parent::$options || из кэша || из базы данных
     * @param  mixed  $default Что вернуть если опции не существует
     * @return mixed
     */
    private static function get_option( $default = array() )
    {
        $Plugin = static::getInstance();

        if( ! $Plugin->options ) {
            $Plugin->options = get_option( static::get_option_name(), $default );
        }

        return apply_filters( "get_{Plugin\DOMAIN}_option", $Plugin->options );
    }

    /**
     * Получает url (адресную строку) до плагина
     * @param  string $path путь должен начинаться с / (по аналогии с __DIR__)
     * @return string
     */
    public static function get_plugin_url( $path = '' )
    {
        $url = plugins_url( basename(Plugin\PLUGIN_DIR) ) . $path;

        return apply_filters( "get_{Plugin\DOMAIN}_plugin_url", $url, $path );
    }

    /**
     * [get_template description]
     * @param  [type]  $template [description]
     * @param  boolean $slug     [description]
     * @param  array   $data     [description]
     * @return string            [description]
     */
    public static function get_template( $template, $slug = false, $data = array() )
    {
        $filename = '';

        if ($slug) $templates[] = Plugin\PLUGIN_DIR . '/' . $template . '-' . $slug;
        $templates[] = Plugin\PLUGIN_DIR . '/' . $template;

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
    public static function get( $prop_name, $default = false )
    {
        $option = self::get_option();
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
    public static function set( $prop_name, $value = '', $autoload = null )
    {
        $option = self::get_option();
        if( ! is_array($prop_name) ) $prop_name = array($prop_name => $value);

        foreach ($prop_name as $prop_key => $prop_value) {
            $option[ $prop_key ] = $prop_value;
        }

        return update_option( static::get_option_name(), $option, $autoload );
    }
}
