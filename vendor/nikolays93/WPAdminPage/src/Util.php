<?php

namespace NikolayS93\WPAdminPageBeta;

class Util
{
    public static function array_filter_recursive($input)
    {
        foreach ($input as &$value) {
            if ( is_array($value) )
                $value = self::array_filter_recursive($value);
        }

        return array_filter($input);
    }

    public static function array_map_recursive($callback, $array)
    {
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
        };

        return array_map($func, $array);
    }
}
