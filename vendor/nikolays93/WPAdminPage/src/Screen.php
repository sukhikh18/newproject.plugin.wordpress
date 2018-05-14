<?php

namespace NikolayS93\WPAdminPageBeta;

class Screen
{
    /** @var String Page slug name */
    private $slug;

    /** @var String Page h2 title */
    private $title;

    /**
     * @var String return from add_submenu_page or add_menu_page (for metabox)
     * temporary for __toString()
     */
    private $wp_screen_name;

    function __toString() {

        return $this->wp_screen_name;
    }

    function __construct( $slug = null, $title = null, $args = array() )
    {
        $this->slug = $slug;
        $this->title = $title;
        if( $args['parent'] ) {
            $this->wp_screen_name = \add_submenu_page(
                $args['parent'],
                $args['title'],
                $args['menu'],
                $args['permissions'],
                $this->slug,
                array($this, '__template'),
                $args['menu_pos']
            );
        }
        else {
            $this->wp_screen_name = \add_menu_page(
                $args['title'],
                $args['menu'],
                $args['permissions'],
                $this->slug,
                array($this, '__template'),
                $args['icon_url'],
                $args['menu_pos']
            );
        }
    }

    function __template()
    {
        $action = apply_filters( $this->slug . '_form_action', 'options.php');
        $method = apply_filters( $this->slug . '_form_method', 'post');
        ?>
        <div class="wrap">

            <h2><?php echo $this->title;?></h2>

            <?php do_action( $this->slug . '_after_title' ); ?>

            <form id="options" enctype="multipart/form-data" action="<?php echo $action; ?>" method="<?php echo $method; ?>">
                <?php do_action( $this->slug . '_before_form_inputs'); ?>

                <div id="poststuff">

                    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                        <div id="post-body-content">
                            <?php
                            do_action( 'before_' . $this->slug . '_inside_page_content');

                            /**
                             * $slug . _inside_page_content hook.
                             *
                             * @hooked array('WPAdminPage', 'page_render') - 10
                             */
                            do_action( $this->slug . '_inside_page_content');

                            do_action( 'after_' . $this->slug . '_inside_page_content');
                            ?>
                        </div>

                        <div id="postbox-container-1" class="postbox-container side-container">
                            <?php
                            /**
                             * $slug . _inside_side_container hook.
                             *
                             * @hooked array('WPAdminPage', 'side_render') - 10
                             */
                            do_action( $this->slug . '_inside_side_container');
                            ?>
                        </div>

                        <div id="postbox-container-2" class="postbox-container normal-container">
                            <?php
                            /**
                             * $slug . _inside_normal_container hook.
                             *
                             * @hooked array('WPAdminPage', 'normal_render') - 10
                             */
                            do_action( $this->slug . '_inside_normal_container');
                            ?>
                        </div>
                        <div id="postbox-container-3" class="postbox-container advanced-container">
                            <?php
                            /**
                             * $slug . _inside_advanced_container hook.
                             *
                             * @hooked array('WPAdminPage', 'advanced_render') - 10
                             */
                            do_action( $this->slug . '_inside_advanced_container');
                            ?>
                        </div>

                    </div> <!-- #post-body -->
                </div> <!-- #poststuff -->

                <?php
                    /* Used to save closed metaboxes and their order */
                    wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
                    wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                    // add hidden settings
                    if($action == 'options.php')
                        settings_fields( $this->slug );
                ?>

                <?php do_action( $this->slug . '_after_form_inputs'); ?>
            </form>

        </div><!-- .wrap -->

        <div class="clear" style="clear: both;"></div>

        <?php do_action( $this->slug . '_after_page_wrap'); ?>

        <?php
    }
}
