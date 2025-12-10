<?php
class CCP_Frontend_Display {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'display_popup'));
    }
    
    public function enqueue_scripts() {
        if (!$this->should_display_popup()) {
            return;
        }
        
        wp_enqueue_style(
            'ccp-styles',
            CCP_PLUGIN_URL . 'assets/css/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'ccp-scripts',
            CCP_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('ccp-scripts', 'ccp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ccp_nonce')
        ));
    }
    
    private function should_display_popup() {
        // Don't show if cookie is already set

        $consent = $_COOKIE['ccp_consent'] ?? '';

            // if ($consent === 'accepted') {
            //     return false;
            // }elseif( $consent === 'declined') {
            //     return false;
            // }

        if (isset($_COOKIE['ccp_consent'])) {
            return false;
        }
        
        // Check if user is logged in and we should hide for admins
        if (is_user_logged_in() && current_user_can('manage_options')) {
            // Option: You can add setting to hide for admins
            return true; // Change as needed
        }
        
        return true;
    }
    
    public function display_popup() {
        if (!$this->should_display_popup()) {
            return;
        }
        
        $options = get_option('ccp_settings');
        $terms_page_id = isset($options['terms_page']) ? $options['terms_page'] : '';
        $terms_link = '';
        
        if ($terms_page_id) {
            $terms_link = get_permalink($terms_page_id);
        }
        ?>
        <div id="ccp-popup" class="ccp-popup ccp-<?php echo esc_attr($options['position'] ?? 'bottom'); ?>">
            <div class="ccp-content">
                <h2 class="ccp-heading"><?php echo esc_html($options['popup_heading'] ?? ''); ?></h2>
                <p class="ccp-message"><?php echo esc_html($options['popup_text'] ?? ''); ?></p>
                
                <div class="ccp-buttons">
                    <button type="button" class="ccp-button ccp-accept">
                        <?php echo esc_html($options['accept_text'] ?? 'Accept'); ?>
                    </button>
                    <button type="button" class="ccp-button ccp-decline">
                        <?php echo esc_html($options['decline_text'] ?? 'Decline'); ?>
                    </button>
                </div>
                
                <?php if ($terms_link): ?>
                <div class="ccp-terms">
                    <a href="<?php echo esc_url($terms_link); ?>" target="_blank" class="ccp-terms-link">
                        <?php echo esc_html($options['link_text'] ?? 'View Terms & Conditions'); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}