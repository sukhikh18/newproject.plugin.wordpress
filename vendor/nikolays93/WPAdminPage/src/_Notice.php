<?php

namespace NikolayS93\WPAdminPageBeta;

class Notice
{
    static function notice_tpl( $msg )
    {
        if( sizeof(self::$notices) ) {
            foreach (self::$notices as $notice) {
                echo sprintf('<div class="notice notice-%s is-dismissible">%s</div>',
                    esc_attr($notice->status), $notice->message );
            }
        }
    }

    public static function add_notice( $msg, $status = 'success', $fitler = 'the_content' )
    {
        if( is_object($msg) && !empty($msg->message) ) {
            self::$notices[] = (object) array(
                'status' => isset($msg->status) ? $msg->status : $status,
                'message' => isset($msg->filter) ? apply_filters($msg->filter, $msg->message)
                : apply_filters($fitler, $msg->message),
            );
        }
        else {
            $message = $fitler ? apply_filters( $fitler, $msg ) : $msg;

            self::$notices[] = (object) array(
                'status' => $status,
                'message' => $message,
            );
        }
    }
}