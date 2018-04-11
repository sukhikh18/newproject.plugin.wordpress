<?php

namespace NikolayS93\WPAdminForm;

class Preset
{
    // field
    public static function get( $render_data )
    {
        $defaults = array();
        if( empty($render_data) ) {
            return $defaults;
        }

        if( isset($render_data['id']) ) {
            $render_data = array($render_data);
        }

        foreach ($render_data as $input) {
            if(isset($input['default']) && $input['default']){
                $input['id'] = str_replace('][', '_', $input['id']);
                $defaults[$input['id']] = $input['default'];
            }
        }

        return $defaults;
    }

    // form
    public static function parse_args($args, $is_table)
    {
        $defaults = array(
            'admin_page'  => true, // set true for auto detect
            'item_wrap'   => array('<p>', '</p>'),
            'form_wrap'   => array('', ''),
            'label_tag'   => 'th',
            'hide_desc'   => false,
            'postmeta'    => false,
            'sub_name'    => '',
        );

        if( $is_table )
            $defaults['form_wrap'] = array('<table class="table form-table"><tbody>', '</tbody></table>');

        if( !isset( $args['admin_page'] ) && !empty($_GET['page']) ) {
            $defaults['admin_page'] = $_GET['page'];
        }
        elseif( !empty( $args['admin_page'] ) && is_string( $args['admin_page'] ) ) {
            $defaults['admin_page'] = $args['admin_page'];
        }

        $args = wp_parse_args( $args, $defaults );

        if( ! is_array($args['item_wrap']) )
            $args['item_wrap'] = array('', '');

        if( ! is_array($args['form_wrap']) )
            $args['form_wrap'] = array('', '');

        if( false === $is_table )
            $args['label_tag'] = 'label';

        return $args;
    }
}