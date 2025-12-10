<?php
if ( ! class_exists( 'CCP_Admin_Settings' ) ) :

class CCP_Admin_Settings {
    private $option_name = 'ccp_settings';
    private $defaults = array(
        'popup_text'       => 'We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.',
        'popup_heading'    => 'Warning',
        'accept_text'      => 'Accept',
        'decline_text'     => 'Decline',
        'terms_page'       => '',
        'link_text'        => 'View Terms & Conditions',
        'expire_days'      => 30,

        // style defaults
        'bg_color'         => '#ffffff',
        'text_color'       => '#2c3338',
        'border_color'     => '#dddddd',
        'border_width'     => 1,
        'accept_bg'        => '#f59733',
        'accept_text_color'=> '#ffffff',
        'decline_bg'       => '#f54949',
        'decline_text_color'=> '#ffffff',
        'terms_link_color' => '#007cba',
    );

    /** @var string|null Hook suffix returned by add_options_page() */
    private $page_hook = null;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );
        add_action( 'wp_head', array( $this, 'print_frontend_styles' ) );
    }

    public function add_admin_menu() {
        // Add under Settings -> Cookie Consent
        $this->page_hook = add_options_page(
            __( 'Cookie Consent Settings', 'cookie-consent' ),
            __( 'Cookie Consent', 'cookie-consent' ),
            'manage_options',
            'cookie-consent-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'ccp_settings_group', $this->option_name, array( $this, 'sanitize_settings' ) );

        add_settings_section(
            'ccp_main_section',
            __( 'Popup Settings', 'cookie-consent' ),
            null,
            'cookie-consent-settings'
        );

        // Core fields
        $this->add_field( 'popup_text', __( 'Popup Message', 'cookie-consent' ), 'render_textarea_field' );
        $this->add_field( 'popup_heading', __( 'Popup Heading', 'cookie-consent' ), 'render_text_field' );
        $this->add_field( 'accept_text', __( 'Accept Button Text', 'cookie-consent' ), 'render_text_field' );
        $this->add_field( 'decline_text', __( 'Decline Button Text', 'cookie-consent' ), 'render_text_field' );
        $this->add_field( 'terms_page', __( 'Terms & Conditions Page', 'cookie-consent' ), 'render_page_select_field' );
        $this->add_field( 'link_text', __( 'Link Text', 'cookie-consent' ), 'render_text_field' );
        $this->add_field( 'expire_days', __( 'Cookie Expiration (days)', 'cookie-consent' ), 'render_number_field', array( 'min' => 1, 'max' => 365 ) );

        // Style fields
        $this->add_field( 'bg_color', __( 'Popup Background Color', 'cookie-consent' ), 'render_color_field' );
        $this->add_field( 'text_color', __( 'Popup Text Color', 'cookie-consent' ), 'render_color_field' );
        $this->add_field( 'border_color', __( 'Popup Border Color', 'cookie-consent' ), 'render_color_field' );
        $this->add_field( 'border_width', __( 'Popup Border Width (px)', 'cookie-consent' ), 'render_number_field', array( 'min' => 0, 'max' => 20 ) );

        $this->add_field( 'accept_bg', __( 'Accept Button Background', 'cookie-consent' ), 'render_color_field' );
        $this->add_field( 'accept_text_color', __( 'Accept Button Text Color', 'cookie-consent' ), 'render_color_field' );
        $this->add_field( 'decline_bg', __( 'Decline Button Background', 'cookie-consent' ), 'render_color_field' );
        $this->add_field( 'decline_text_color', __( 'Decline Button Text Color', 'cookie-consent' ), 'render_color_field' );

        $this->add_field( 'terms_link_color', __( 'Terms Link Color', 'cookie-consent' ), 'render_color_field' );
    }

    /**
     * Helper to add a field.
     */
    private function add_field( $id, $title, $callback, $extra_args = array() ) {
        $args = wp_parse_args( $extra_args, array(
            'id'      => $id,
            'default' => isset( $this->defaults[ $id ] ) ? $this->defaults[ $id ] : '',
        ) );

        add_settings_field(
            $id,
            $title,
            array( $this, $callback ),
            'cookie-consent-settings',
            'ccp_main_section',
            $args
        );
    }

    public function sanitize_settings( $input ) {
        $out = array();
        $input = is_array( $input ) ? $input : array();
        $input = wp_parse_args( $input, $this->defaults );

        $out['popup_text'] = wp_kses_post( $input['popup_text'] );
        $out['popup_heading'] = sanitize_text_field( $input['popup_heading'] );
        $out['accept_text'] = sanitize_text_field( $input['accept_text'] );
        $out['decline_text'] = sanitize_text_field( $input['decline_text'] );
        $out['link_text'] = sanitize_text_field( $input['link_text'] );
        $out['terms_page'] = intval( $input['terms_page'] );

        $expire = intval( $input['expire_days'] );
        $expire = max( 1, min( 365, $expire ) );
        $out['expire_days'] = $expire;

        // Colors
        $out['bg_color']          = sanitize_hex_color( $input['bg_color'] );
        $out['text_color']        = sanitize_hex_color( $input['text_color'] );
        $out['border_color']      = sanitize_hex_color( $input['border_color'] );
        $out['accept_bg']         = sanitize_hex_color( $input['accept_bg'] );
        $out['accept_text_color'] = sanitize_hex_color( $input['accept_text_color'] );
        $out['decline_bg']        = sanitize_hex_color( $input['decline_bg'] );
        $out['decline_text_color']= sanitize_hex_color( $input['decline_text_color'] );
        $out['terms_link_color']  = sanitize_hex_color( $input['terms_link_color'] );

        // border width
        $bw = intval( $input['border_width'] );
        $bw = max( 0, min( 20, $bw ) );
        $out['border_width'] = $bw;

        return $out;
    }

    // --- Render helpers ---

    private function get_option_value( $id, $default = '' ) {
        $options = get_option( $this->option_name, array() );
        $options = wp_parse_args( $options, $this->defaults );
        return isset( $options[ $id ] ) ? $options[ $id ] : $default;
    }

    public function render_text_field( $args ) {
        $value = $this->get_option_value( $args['id'], $args['default'] );
        ?>
        <input type="text"
               id="ccp_<?php echo esc_attr( $args['id'] ); ?>"
               name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['id'] ); ?>]"
               value="<?php echo esc_attr( $value ); ?>"
               class="regular-text" />
        <?php
    }

    public function render_textarea_field( $args ) {
        $value = $this->get_option_value( $args['id'], $args['default'] );
        ?>
        <textarea id="ccp_<?php echo esc_attr( $args['id'] ); ?>"
                  name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['id'] ); ?>]"
                  rows="4"
                  class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
        <?php
    }

    public function render_number_field( $args ) {
        $min = isset( $args['min'] ) ? intval( $args['min'] ) : 0;
        $max = isset( $args['max'] ) ? intval( $args['max'] ) : 9999;
        $value = $this->get_option_value( $args['id'], $args['default'] );
        ?>
        <input type="number"
               id="ccp_<?php echo esc_attr( $args['id'] ); ?>"
               name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['id'] ); ?>]"
               value="<?php echo esc_attr( intval( $value ) ); ?>"
               min="<?php echo esc_attr( $min ); ?>"
               max="<?php echo esc_attr( $max ); ?>"
               class="small-text" />
        <?php
    }

    public function render_color_field( $args ) {
        $value = $this->get_option_value( $args['id'], $args['default'] );
        ?>
        <input type="text"
               id="ccp_<?php echo esc_attr( $args['id'] ); ?>"
               name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['id'] ); ?>]"
               value="<?php echo esc_attr( $value ); ?>"
               class="ccp-color-field regular-text" />
        <span style="display:inline-block;margin-left:8px;vertical-align:middle;"><?php echo esc_html( $value ); ?></span>
        <?php
    }

    public function render_page_select_field( $args ) {
        $value = $this->get_option_value( $args['id'], $args['default'] );
        $pages = get_pages();
        ?>
        <select id="ccp_<?php echo esc_attr( $args['id'] ); ?>"
                name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['id'] ); ?>]">
            <option value=""><?php _e( '-- Select a page --', 'cookie-consent' ); ?></option>
            <?php foreach ( $pages as $page ) : ?>
                <option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( intval( $value ), $page->ID ); ?>>
                    <?php echo esc_html( $page->post_title ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Cookie Consent Settings', 'cookie-consent' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'ccp_settings_group' );
                do_settings_sections( 'cookie-consent-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue color picker only for our settings page.
     */
    public function admin_enqueue_assets( $hook ) {
        // Only enqueue on our settings page: compare against stored hook suffix
        if ( $this->page_hook && $hook === $this->page_hook ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            // small inline script to init color pickers
            add_action( 'admin_print_footer_scripts', array( $this, 'print_colorpicker_init' ) );
        }
    }

    public function print_colorpicker_init() {
        ?>
        <script>
        (function($){
            $(function(){
                $('.ccp-color-field').each(function(){
                    $(this).wpColorPicker({
                        change: function(event, ui){
                            // update the adjacent span with hex value (optional)
                            $(this).next('span').text($(this).val());
                        }.bind(this)
                    });
                });
            });
        })(jQuery);
        </script>
        <?php
    }

    /**
     * Print frontend styles based on settings.
     */
    public function print_frontend_styles() {
        $opts = get_option( $this->option_name, $this->defaults );
        $opts = wp_parse_args( $opts, $this->defaults );

        // fallback values
        $bg            = sanitize_hex_color( $opts['bg_color'] ) ?: $this->defaults['bg_color'];
        $text          = sanitize_hex_color( $opts['text_color'] ) ?: $this->defaults['text_color'];
        $border_color  = sanitize_hex_color( $opts['border_color'] ) ?: $this->defaults['border_color'];
        $border_width  = intval( $opts['border_width'] );
        $accept_bg     = sanitize_hex_color( $opts['accept_bg'] ) ?: $this->defaults['accept_bg'];
        $accept_text   = sanitize_hex_color( $opts['accept_text_color'] ) ?: $this->defaults['accept_text_color'];
        $decline_bg    = sanitize_hex_color( $opts['decline_bg'] ) ?: $this->defaults['decline_bg'];
        $decline_text  = sanitize_hex_color( $opts['decline_text_color'] ) ?: $this->defaults['decline_text_color'];
        $terms_link    = sanitize_hex_color( $opts['terms_link_color'] ) ?: $this->defaults['terms_link_color'];

        // print minimal safe CSS scoped to .ccp-content (your popup container)
        ?>
        <style type="text/css">
        .ccp-content {
            background-color: <?php echo esc_html( $bg ); ?>;
            color: <?php echo esc_html( $text ); ?>;
            border: <?php echo esc_attr( $border_width ); ?>px solid <?php echo esc_html( $border_color ); ?>;
        }
        .ccp-content .ccp-message {
            color: <?php echo esc_html( $text ); ?>;
        }
        .ccp-button.ccp-accept {
            background: <?php echo esc_html( $accept_bg ); ?>;
            color: <?php echo esc_html( $accept_text ); ?>;
            border-color: <?php echo esc_html( $accept_bg ); ?>;
        }
        .ccp-button.ccp-decline {
            background: <?php echo esc_html( $decline_bg ); ?>;
            color: <?php echo esc_html( $decline_text ); ?>;
            border-color: <?php echo esc_html( $decline_bg ); ?>;
        }
        .ccp-terms-link {
            color: <?php echo esc_html( $terms_link ); ?>;
        }
        .ccp-heading {
            color: <?php echo esc_html( $text ); ?>;
        }
        </style>
        <?php
    }
}



endif;
