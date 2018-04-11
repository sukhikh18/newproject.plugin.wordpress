<?php

namespace NikolayS93\WPAdminForm;

if( !empty( $field['custom_attributes']['rows'] ) ) $attrs['rows'] = 5;
if( !empty( $field['custom_attributes']['cols'] ) ) $attrs['cols'] = 40;

$input .= $label[0];
$input .= '<textarea ' . Util::get_attributes_text( $attrs ) . '>';
$input .= esc_textarea( $entry );
$input .= '</textarea>';
$input .= $label[1];