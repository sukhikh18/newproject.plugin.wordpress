<?php

namespace NikolayS93\WPAdminForm;

class Field extends Form
{
    private $html;

    function __toString() {

        return $this->html;
    }

    function __construct( $field, $input, $args = array() )
    {
        $html = array();
        $required = self::_get_required_string( $field );
        
        $template = self::_get_field_template($field, $input, $args, 
            self::_get_description_string( $field ));

        if( ! $args['is_table'] ){
            $html[] = sprintf('<section id="%1$s-wrap">%2$s</section>',
                esc_attr( $field['id'] ),
                $template
            );
        }
        elseif( 'hidden' === $field['type'] ){
            $this->hiddens[] = $input;
        }
        elseif( $field['type'] == 'html' ){
            $html[] = $args['form_wrap'][1];
            $html[] = $input;
            $html[] = $args['form_wrap'][0];
        }
        else {
            $lc = is_array($field['label_class']) ?
                implode( ' ', $field['label_class'] ) : $field['label_class'];

            $html[] = sprintf('<tr id="%s">',
                esc_attr( $field['id'] ) );

            $html[] = sprintf('  <%1$s class="%4$s">%2$s%3$s</%1$s>',
                $args['label_tag'],
                $field['label'],
                $required,
                $lc ? esc_attr($lc) : 'label');

            $html[] = "  <td>";
            $html[] = "    " . $template;
            $html[] = "  </td>";
            $html[] = "</tr>";
        }

        $this->html = implode("\n", $html);
    }

    private static function _get_field_template($field, $input, $args, $desc)
    {
        $template = $field['before'];
        $template.= $args['item_wrap'][0];
        $template.= $input;
        $template.= $args['item_wrap'][1];
        $template.= $field['after'];
        $template.= $desc;

        return $template;
    }

    private static function _get_required_string( &$field )
    {
        if ( $field['required'] ) {
            $field['class'][] = 'required';
            $required = '<abbr style="color:#f00;" class="required" title="' . esc_attr__( 'required' ) . '">*</abbr>';
        } else {
            $required = '';
        }
    }

    private static function _get_description_string( $field )
    {
        $desc = '';
        if( $field['description'] ){
            /**
             * @todo: display hover
             */
            if( isset($this->args['hide_desc']) && $this->args['hide_desc'] === true )
                $desc = "<div class='description' style='display: none;'>{$field['description']}</div>";
            else
                $desc = "<span class='description'>{$field['description']}</span>";
        }

        return $desc;
    }
}