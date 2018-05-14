<?php

namespace NikolayS93\WPAdminPageBeta;

class Metabox
{
    private $metabox;

    function __construct( $handle, $label, Callback $render_cb, $position = 'side', $priority = 'high' ) {
        if( $cb = $render_cb->get() ) {
            $this->metabox = array(
                'handle'    => $handle,
                'label'     => $label,
                'render_cb' => $cb,
                'position'  => $position,
                'priority'  => $priority,
            );
        }
        else {
            // $callback->get_error();
            // $callback->get_errors();
        }
    }

    function init_on( $screen )
    {
        if( !isset($this->metabox['handle']) ) return;

        $this->metabox['screen'] = $screen;
        add_action( 'add_meta_boxes', array($this, '__box_action') );
    }

    function __box_action()
    {
        $m = $this->metabox;
        add_meta_box( $m['handle'], $m['label'], $m['render_cb'], $m['screen'], $m['position'], $m['priority']);
    }
}

