<?php

namespace CDevelopers\NSPACE;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * Class Name: WP_Admin_Forms
 * Description: Render a custom admin forms.
 * Version: 1.0.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

class WP_Admin_Forms {
    static $clear_value = false;
    protected $inputs, $args, $is_table, $active;
    protected $hiddens = array();

    public function __construct($data = null, $is_table = true, $args = null)
    {
        if( ! is_array($data) )
            $data = array();

        if( ! is_array($args) )
            $args = array();

        if( isset($data['id']) || isset($data['name']) )
            $data = array($data);

        $args = self::parse_defaults($args, $is_table);
        if( $args['admin_page'] || $args['sub_name'] )
            $data = self::admin_page_options( $data, $args['admin_page'], $args['sub_name'] );

        $this->fields = $data;
        $this->args = $args;
        $this->is_table = $is_table;
    }

    public function render( $return=false )
    {
        $this->get_active();

        $html = $this->args['form_wrap'][0];
        foreach ($this->fields as $field) {
            if ( ! isset($field['id']) && ! isset($field['name']) )
                continue;

            // &$field
            $input = self::render_input( $field, $this->active, $this->is_table );
            $html .= self::_field_template( $field, $input, $this->is_table );
        }
        $html .= $this->args['form_wrap'][1];
        $result = $html . "\n" . implode("\n", $this->hiddens);
        if( $return )
            return $result;

        echo $result;
    }

    public function set_active( $active )
    {
        $this->active = $active;
    }

    public static function render_input( &$field, $active = array(), $for_table = false )
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

        $field = wp_parse_args( $field, $defaults );

        if( $field['default'] && ! in_array($field['type'], array('checkbox', 'select', 'radio')) ) {
            $field['placeholder'] = $field['default'];
        }

        $active = is_string($active) ? array($field['id'] => $active) : $active;
        $field['id'] = str_replace('][', '_', $field['id']);
        $entry = self::parse_entry($field, $active, $field['value']);

        return self::_input_template( $field, $entry, $for_table );
    }

    public function get_active()
    {
        if( ! $this->active ) {
            $this->active = $this->_active();
        }

        return $this->active;
    }

    /**
     * EXPEREMENTAL!
     * Get ID => Default values from $render_data
     * @param  array() $render_data
     * @return array(array(ID=>default),ar..)
     */
    public static function defaults( $render_data ){
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

    /**
     * EXPEREMENTAL!
     *
     * @return array installed options
     */
    private function _active()
    {
        if( $this->args['postmeta'] ){
            global $post;

            if( ! $post instanceof WP_Post ) {
                return false;
            }

            $active = array();
            if( $sub_name = $this->args['sub_name'] ) {
                $active = get_post_meta( $post->ID, $sub_name, true );
            }
            else {
                foreach ($this->fields as $field) {
                    $active[ $field['id'] ] = get_post_meta( $post->ID, $field['id'], true );
                }
            }
        }
        else {
            $active = get_option( $this->args['admin_page'], array() );

            if( $sub_name = $this->args['sub_name'] ) {
                $active = isset($active[ $sub_name ]) ? $active[ $sub_name ] : false;
            }
        }

        /** if active not found */
        if( ! is_array($active) || $active === array() ) {
            return false;
        }

        /**
         * @todo: add recursive handle
         */
        $result = array();
        foreach ($active as $key => $value) {
            if( is_array($value) ){
                foreach ($value as $key2 => $value2) {
                    $result[$key . '_' . $key2] = $value2;
                }
            }
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /******************************** Templates *******************************/
    private function _field_template( $field, $input, $for_table )
    {
        // if ( $field['required'] ) {
        //     $field['class'][] = 'required';
        //     $required = ' <abbr class="required" title="' . esc_attr__( 'required' ) . '">*</abbr>';
        // } else {
        //     $required = '';
        // }

        $html = array();

        $desc = '';
        if( $field['description'] ){
            if( isset($this->args['hide_desc']) && $this->args['hide_desc'] === true )
                $desc = "<div class='description' style='display: none;'>{$field['description']}</div>";
            else
                $desc = "<span class='description'>{$field['description']}</span>";
        }

        $template = $field['before'] . $this->args['item_wrap'][0];
        $template.= $input;
        $template.= $this->args['item_wrap'][1] . $field['after'];
        $template.= $desc;

        if( ! $this->is_table ){
            $html[] = '<section id="'.$field['id'].'-wrap">' . $template . '</section>';
        }
        elseif( $field['type'] == 'hidden' ){
            $this->hiddens[] = $input;
        }
        elseif( $field['type'] == 'html' ){
            $html[] = $this->args['form_wrap'][1];
            $html[] = $input;
            $html[] = $this->args['form_wrap'][0];
        }
        else {
            $lc = is_array($field['label_class']) ? implode( ' ', $field['label_class'] ) : $field['label_class'];
            $html[] = "<tr id='{$field['id']}'>";
            // @todo : add required symbol
            $html[] = "  <{$this->args['label_tag']} class='label'>";
            $html[] = "    {$field['label']}";
            $html[] = "  </{$this->args['label_tag']}>";

            $html[] = "  <td>";
            $html[] = "    " . $template;
            $html[] = "  </td>";
            $html[] = "</tr>";
        }

        return implode("\n", $html);
    }

    static function get_attributes_text( $attributes )
    {
        $attribute_text = '';
        foreach ($attributes as $attribute_key => $attribute) {
            $attribute_text .= sprintf('%s="%s"', $attribute_key, $attribute);
        }

        return $attribute_text;
    }

    static function get_select_options( $options, $entry = '' )
    {
        $result = '';
        foreach ( $options as $key => $text ) {
            if( is_string( $text ) ) {
                $result .= sprintf('<option value="%s"%s>%s</option>',
                    esc_attr( $key ),
                    selected( $entry, $key, false ),
                    esc_attr( $text ) );
                $result .= "\n";
            }
            else {
                $result .= sprintf('<optgroup label="%s">', $key);
                $result .= "\n";

                foreach ($text as $sub_key => $sub_text) {
                    $result .= sprintf('<option value="%s"%s>%s</option>',
                        esc_attr( $sub_key ),
                        selected( $entry, $sub_key, false ),
                        esc_attr( $sub_text ) );
                    $result .= "\n";
                }

                $result .= '</optgroup>';
                $result .= "\n";
            }
        }

        return $result;
    }

    private static function _input_template( $field, $entry, $for_table = false )
    {
        $attributes = array();
        $attributes['name'] = esc_attr( $field['name'] );
        $attributes['id'] = esc_attr( $field['id'] );
        $attributes['class'] = esc_attr( is_array($field['input_class']) ?
            implode(' ', $field['input_class']) : $field['input_class'] );

        if( $field['value'] )
            $attributes['value'] = ('html' == $field['type']) ? $field['value'] : esc_attr( $field['value'] );

        if( $field['placeholder'] )
            $attributes['placeholder'] = esc_attr( $field['placeholder'] );

        if( $field['maxlength'] )
            $attributes['maxlength'] = absint( $field['maxlength'] );

        if( $field['autocomplete'] )
            $attributes['autocomplete'] = esc_attr( $field['autocomplete'] );

        if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
            foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
                $attributes[ esc_attr( $attribute ) ] = esc_attr( $attribute_value );
            }
        }

        $label = array('', '');
        if( ! $for_table && $field['label'] ) {
            $label = array(
                sprintf('<label for="%s" class="%s"><span>%s</span>',
                    esc_attr($field['id']),
                    esc_attr(is_array($field['label_class']) ? implode(' ', $field['label_class']) : $field['label_class']),
                    $field['label']
                ),
            '</label>');
        }

        $input = '';
        switch ( $field['type'] ) {
            case 'html':
                $input .= $attributes['value'];
            break;

            case 'textarea':
                if( !empty( $field['custom_attributes']['rows'] ) ) $attributes['rows'] = 5;
                if( !empty( $field['custom_attributes']['cols'] ) ) $attributes['cols'] = 40;

                $input .= $label[0];
                $input .= '<textarea ' . self::get_attributes_text( $attributes ) . '>';
                $input .= esc_textarea( $entry );
                $input .= '</textarea>';
                $input .= $label[1];
            break;

            case 'checkbox':
                if( empty($attributes['value']) ) $attributes['value'] = 'on';
                if( empty($attributes['checked']) ) {
                    if( ! $attributes['checked'] = checked( $entry, true, false ) )
                        unset($attributes['checked']);
                }

                $attributes['type'] = esc_attr( $field['type'] );
                $attributes['class'] .= ' input-checkbox';

                // if $clear_value === false dont use defaults (couse default + empty value = true)
                if( isset($clear_value) || false !== ($clear_value = self::$clear_value) ) {
                    $input .= sprinft('<input type="hidden" name="%s" value="%s">',
                        $attributes['name'], $clear_value) . "\n";
                }

                $input .= '<input ' . self::get_attributes_text( $attributes ) . '/>';
                $input .= $label[0] . $label[1];
            break;

            case 'select':
                if ( ! empty( $field['options'] ) ) {
                    if ( '' === current($field['options']) ) {
                        if ( empty($attributes['placeholder']) )
                            $attributes['placeholder'] = $text ? $text : __( 'Choose an option' );
                    }

                    $input .= $label[0];
                    $input .= '<select ' . self::get_attributes_text( $attributes ) . '>';
                    $input .= self::get_select_options($field['options'], $entry);
                    $input .= '</select>';
                    $input .= $label[1];
                }
            break;

            // @todo:
            case 'radio': break;
            case 'fieldset': break;

            default:
                $attributes['type'] = esc_attr( $field['type'] );
                $attributes['value'] = $field['value'] ? esc_attr( $field['value'] ) : esc_attr( $entry );
                $attributes['class'] .= ' input-' . $attributes['type'];

                $input .= $label[0];
                $input .= '<input ' . self::get_attributes_text( $attributes ) . '/>';
                $input .= $label[1];
            break;
        }
        return $input;
    }

    /********************************** Utils *********************************/
    private static function parse_defaults($args, $is_table)
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

        if( ( isset($args['admin_page']) && $args['admin_page'] !== false ) ||
            !isset($args['admin_page']) && is_admin() && !empty($_GET['page']) )
            $defaults['admin_page'] = $_GET['page'];

        $args = wp_parse_args( $args, $defaults );

        if( ! is_array($args['item_wrap']) )
            $args['item_wrap'] = array('', '');

        if( ! is_array($args['form_wrap']) )
            $args['form_wrap'] = array('', '');

        if( false === $is_table )
            $args['label_tag'] = 'label';

        return $args;
    }

    private static function parse_entry($field, $active)
    {
        if( ! is_array($active) || sizeof($active) < 1 )
            return false;

        $active_key = $field['check_active'] ? $field[$field['check_active']] : str_replace('[]', '', $field['name']);
        $active_value = isset($active[$active_key]) ? $active[$active_key] : false;

        if($field['type'] == 'checkbox' || $field['type'] == 'radio'){
            $entry = self::is_checked( $field, $active_value );
        }
        elseif($field['type'] == 'select'){
            $entry = ($active_value) ? $active_value : $field['default'];
        }
        else {
            // if text, textarea, number, email..
            $entry = $active_value;
        }
        return $entry;
    }

    private static function is_checked( $field, $active )
    {
        // if( $active === false && $value )
          // return true;

        $checked = ( $active === false ) ? false : true;
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
                if( $active || (!$checked && $field['default']) )
                    return true;
            }
        }

        return false;
    }

    private static function admin_page_options( $fields, $option_name, $sub_name = false )
    {
        foreach ($fields as &$field) {
            if ( ! isset($field['id']) && ! isset($field['name']) )
                continue;

            if( $option_name ) {
                if( isset($field['name']) ) {
                    $field['name'] = ($sub_name) ?
                        "{$option_name}[{$sub_name}][{$field['name']}]" : "{$option_name}[{$field['name']}]";
                }
                else {
                    $field['name'] = ($sub_name) ?
                        "{$option_name}[{$sub_name}][{$field['id']}]" : "{$option_name}[{$field['id']}]";
                }

                if( !isset($field['check_active']) )
                    $field['check_active'] = 'id';
            }
        }

        return $fields;
    }
}
