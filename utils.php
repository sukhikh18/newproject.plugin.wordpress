<?php

namespace CDevelopers\NSPACE;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

class Utils
{
    private static $options;
    private function __construct() {}
    private function __clone() {}

    /**
     * Получает название опции плагина
     *     Чаще всего это название плагина
     *     Чаще всего оно используется как название страницы настроек
     * @return string
     */
    public static function get_option_name() {

        return apply_filters("get_{DOMAIN}_option_name", DOMAIN);
    }

    /**
     * Получает настройку из self::$options || из кэша || из базы данных
     * @param  mixed  $default Что вернуть если опции не существует
     * @return mixed
     */
    private static function get_option( $default = array() )
    {
        if( ! self::$options )
            self::$options = get_option( self::get_option_name(), $default );

        return apply_filters( "get_{DOMAIN}_option", self::$options );
    }

    /**
     * Записывает ошибку
     * @param  string $msg  Текст ошибки
     * @param  string $path Путь до файла с ошибкой
     */
    public static function write_debug( $msg, $path )
    {
        if( ! defined('WP_DEBUG_LOG') || ! WP_DEBUG_LOG )
            return;

        $plugin_dir = self::get_plugin_dir();
        $path = str_replace($plugin_dir, '', $path);
        $msg = str_replace($plugin_dir, '', $msg);

        $date = new \DateTime();
        $date_str = $date->format(\DateTime::W3C);

        if( $handle = @fopen($plugin_dir . "/debug.log", "a+") ) {
            fwrite($handle, "[{$date_str}] {$msg} ({$path})\r\n");
            fclose($handle);
        }
        elseif (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
            echo sprintf( __('Can not have access the file %s (%s)', DOMAIN),
                __DIR__ . "/debug.log",
                $path );
        }
    }

    /**
     * Загружаем файл если существует
     * @todo Добавить backtrace
     *
     * @param  string  $filename Полный путь до файла
     * @param  array   $args     Аргументы что нужно передать в файл
     * @param  boolean $once     Использовать приставку _once ответ вернет boolean, иначе результат файла
     * @param  boolean $reqire   Может ли система работать дальше без этого файла
     * @return mixed (read $once param)
     */
    public static function load_file_if_exists( $filename, $args = array(), $once = false, $reqire = false )
    {
        if ( ! is_readable( $filename ) ) {
            self::write_debug(sprintf(__('The file %s can not be included', DOMAIN), $filename), __FILE__);
            return false;
        }

        if( $reqire ) $file = ( $once ) ? require_once( $filename ) : require( $filename );
        else          $file = ( $once ) ? include_once( $filename ) : include( $filename );

        return apply_filters( "load_{DOMAIN}_file_if_exists", $file, $filename );
    }

    /**
     * Получаем директорию плагина (на сервере)
     * @param  string $path зарегистрированные переменные (case'ы)
     *                      иначе путь должен начинаться с / (по аналогии с __DIR__)
     * @return string
     */
    public static function get_plugin_dir( $path = '' )
    {
        $dir = PLUGIN_DIR;
        switch ( $path ) {
            case 'includes': $dir .= '/includes'; break;
            case 'libs':     $dir .= '/includes/libs'; break;
            case 'settings': $dir .= '/includes/settings'; break;
        }

        return apply_filters( "get_{DOMAIN}_plugin_dir", $dir, $path );
    }

    /**
     * Получаем url (адресную строку) до плагина
     * @param  string $path путь должен начинаться с / (по аналогии с __DIR__)
     * @return string
     */
    public static function get_plugin_url( $path = '' )
    {
        $url = plugins_url( basename(PLUGIN_DIR) ) . $path;

        return apply_filters( "get_{DOMAIN}_plugin_url", $url, $path );
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
            if( is_array($option) && count($option) )
                return $option;

            return $default;
        }

        return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
    }

    /**
     * Установить параметр в опцию плагина
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

        return update_option( self::get_option_name(), $option, $autoload );
    }

    /**
     * Получить настройки из файла
     * @param  string $filename Название файла в папке настроек ex. 'main.php'
     * @param  array  $args     Параметры что нужно передать в файл настроек
     * @return mixed
     */
    public static function get_settings( $filename, $args = array() ) {

        return self::load_file_if_exists( self::get_plugin_dir('settings') . '/' . $filename, $args );
    }
}