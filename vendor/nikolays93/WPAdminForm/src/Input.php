<?php

namespace NikolayS93\WPAdminForm;

class Input
{
    private $html;

    function __toString() {

        return $this->html;
    }

    public function __construct( &$field, $active = array(), $args = array() )
    {
        $field = self::_input_parse_defaults( $field );
        $field = self::_default_to_placeholder( $field );

        $field['id'] = str_replace('][', '_', $field['id']);
        $entry = self::_parse_entry($field, $active, $field['value']);

        $this->html = self::_input_template( $field, $entry, $args );
    }


    private static function _is_checked( $field, $active )
    {
        // if( $active === false && $value )
          // return true;

        if( $active === 'false' || $active === 'off' || $active === '0' )
            return false;

        if( $active === 'true'  || $active === 'on'  || $active === '1' )
            return true;

        if( $active || $field['default'] ){
            if( $field['value'] ){
                if( is_array($active) ){
                    if( in_array($field['value'], $active) )
                        return true;
                }
                else {
                    if( $field['value'] == $active || $field['value'] === true )
                        return true;
                }
            }
            else {
                if( $active || ($active === false && $field['default']) )
                    return true;
            }
        }

        return false;
    }

    private static function _parse_entry($field, $active)
    {
        if( in_array($field['type'], array('checkbox', 'radio')) ) {
            $active = self::_is_checked( $field, $active );
        }
        elseif( $field['type'] == 'select' && !$active ) {
            $active = $field['default'];
        }

        return $active;
    }

    private static function _input_parse_defaults( $field )
    {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => isset($field['desc']) ? $field['desc'] : '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => '',
            'name'              => $field['id'],
            // 'class'             => array(),
            'label_class'       => array('label'),
            'input_class'       => array(),
            'options'           => array(),
            'custom_attributes' => array(),
            // 'validate'          => array(),
            'default'           => '',
            'before'            => '',
            'after'             => '',
            'check_active'      => false,
            'value'             => '',
        );

        return wp_parse_args( $field, $defaults );
    }

    private static function _default_to_placeholder( $field )
    {
        if( $field['default'] && ! in_array($field['type'], array('checkbox', 'select', 'radio')) ) {
            $field['placeholder'] = $field['default'];
        }

        return $field;
    }

    private static function _get_label_arr( $field, $args )
    {
        if( isset($args['is_table']) && $args['is_table'] )
            return array('', '');

        $strLabelClass = is_array($field['label_class']) ? implode(' ', $field['label_class']) : $field['label_class'];
        $label = array(
            sprintf('<label for="%s" class="%s"><span>%s</span>',
                esc_attr($field['id']),
                esc_attr($strLabelClass),
                $field['label']
            ),
        '</label>');

        return $label;
    }

    private static function _input_template( $field, $entry, $args )
    {
        $attrs = array();
        $attrs['name'] = esc_attr( $field['name'] );
        $attrs['id'] = esc_attr( $field['id'] );
        $attrs['class'] = esc_attr( is_array($field['input_class']) ?
            implode(' ', $field['input_class']) : $field['input_class'] );

        if( $field['value'] )
            $attrs['value'] = ('html' == $field['type']) ? $field['value'] : esc_attr( $field['value'] );

        if( $field['placeholder'] )
            $attrs['placeholder'] = esc_attr( $field['placeholder'] );

        if( $field['maxlength'] )
            $attrs['maxlength'] = absint( $field['maxlength'] );

        if( $field['autocomplete'] )
            $attrs['autocomplete'] = esc_attr( $field['autocomplete'] );

        if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
            foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
                $attrs[ esc_attr( $attribute ) ] = esc_attr( $attribute_value );
            }
        }

        $label = self::_get_label_arr( $field, $args );

        $input = '';
        switch ( $field['type'] ) {
            case 'html':
                $input .= $attrs['value'];
            break;

            case 'textarea':
            case 'checkbox':
            case 'select':
                include( __DIR__ . '/templates/'.$field['type'].'.php' );
            break;

            // @todo:
            case 'radio': break;
            case 'fieldset': break;

            default:
                include( __DIR__ . '/templates/default.php' );
            break;
        }

        return $input;
    }
}
