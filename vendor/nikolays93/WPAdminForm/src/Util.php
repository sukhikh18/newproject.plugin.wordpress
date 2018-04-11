<?php

namespace NikolayS93\WPAdminForm;

class Util
{
    public static function get_attributes_text( $attributes )
    {
        $attribute_text = '';
        foreach ($attributes as $attribute_key => $attribute) {
            $attribute_text .= sprintf('%s="%s"', $attribute_key, $attribute);
        }

        return $attribute_text;
    }

    public static function get_select_options( $options, $entry = '' )
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
}
