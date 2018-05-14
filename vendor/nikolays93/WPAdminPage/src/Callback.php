<?php

namespace NikolayS93\WPAdminPageBeta;

class Callback
{
    const ERR = array(
        'E'    => 'Callback is empty',
        'F_NE' => 'Function in not exists',
        'M_E'  => 'Method is empty',
        'M_NE' => 'Method is not exists',
    );

    private $callback = array(
        'err' => array(),
        'cb'  => '',
    );

    function get()
    {
        if( empty($this->callback['err']) )
            return $this->callback['cb'];

        return false;
    }

    function get_error() {

        return current( $callback['err'] );
    }

    function get_errors() {

        return $callback['err'];
    }

    function __construct( $callback = null ) {
        if( !$callback ) {
            $this->callback['err'][] = self::ERR['E'];
        }

        if( is_string($callback) && !function_exists($callback) ) {
            $this->callback['err'][] = self::ERR['F_NE'];
        }

        if( is_array($callback) ) {
            if( !isset($callback[1]) ) {
                $this->callback['err'][] = self::ERR['M_E'];
            }

            if( !method_exists($callback[0], $callback[1]) ) {
                $this->callback['err'][] = self::ERR['M_NE'];
            }
        }

        $this->callback['cb'] = $callback;
    }
}