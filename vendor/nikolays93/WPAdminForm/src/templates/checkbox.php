<?php

namespace NikolayS93\WPAdminFormBeta;

if( empty($attrs['value']) ) $attrs['value'] = 'on';
if( empty($attrs['checked']) ) {
    if( ! $attrs['checked'] = checked( $entry, true, false ) )
        unset($attrs['checked']);
}

$attrs['type'] = esc_attr( $field['type'] );
$attrs['class'] = 'input-checkbox';

// if $args['clear'] === false dont use defaults (couse default + empty value = true)
if( false !== $args['clear'] ) {
    $input .= sprintf('<input type="hidden" name="%s" value="%s">',
        $attrs['name'],
        $args['clear']) . "\n";
}

$input .= '<input ' . Util::get_attributes_text( $attrs ) . '/>';
$input .= $label[0] . $label[1];
