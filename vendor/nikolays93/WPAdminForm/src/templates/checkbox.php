<?php

namespace NikolayS93\WPAdminForm;

if( empty($attrs['value']) ) $attrs['value'] = 'on';
if( empty($attrs['checked']) ) {
    if( ! $attrs['checked'] = checked( $entry, true, false ) )
        unset($attrs['checked']);
}

$attrs['type'] = esc_attr( $field['type'] );
$attrs['class'] .= ' input-checkbox';

// if $clear_value === false dont use defaults (couse default + empty value = true)
if( isset($clear_value) || false !== ($clear_value = self::$clear_value) ) {
    $input .= sprintf('<input type="hidden" name="%s" value="%s">',
        $attrs['name'], $clear_value) . "\n";
}

$input .= '<input ' . Util::get_attributes_text( $attrs ) . '/>';
$input .= $label[0] . $label[1];