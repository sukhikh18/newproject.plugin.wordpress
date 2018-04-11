<?php

namespace NikolayS93\WPAdminForm;

$attrs['type'] = esc_attr( $field['type'] );
$attrs['value'] = $field['value'] ? esc_attr( $field['value'] ) : esc_attr( $entry );
$attrs['class'] .= ' input-' . $attrs['type'];

$input .= $label[0];
$input .= '<input ' . Util::get_attributes_text( $attrs ) . '/>';
$input .= $label[1];