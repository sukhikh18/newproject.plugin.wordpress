<?php

namespace NikolayS93\WPAdminPageBeta;

class Preset
{
    static $page_args = array(
        'parent'      => 'options-general.php',
        'title'       => '',
        'menu'        => 'New Page',
        'menu_pos'    => 50,
        'permissions' => 'manage_options',
        'tab_sections'=> null,
        'columns'     => 1,
        'icon_url'    => '',
    );

    /**
     * Empty callback arg placeholder
     * @return die with error if WP_DEBUG
     */
    static function not_set_callback()
    {
        if( WP_DEBUG ) {
            wp_die( "Callback param not defined! @see more https://github.com/nikolays93/WPAdminPage" );
        }
    }

    /**
     * Validate registred options
     *
     * @param  _POST $inputs post data for update
     * @return array $inputs filtred data for save
     */
    static function validate_options( $inputs )
    {
        $inputs = Util::array_map_recursive( 'sanitize_text_field', $inputs );
        $inputs = Util::array_filter_recursive($inputs);

        return $inputs;
    }

    static function parse_page_args( $args )
    {
        self::$page_args['validate'] = array(__CLASS__, 'validate_options');

        $args = wp_parse_args( $args, self::$page_args );

        return $args;
    }
}
