<?php
class CCP_Cookie_Handler {
    
    public function __construct() {
        add_action('wp_ajax_ccp_set_consent', array($this, 'handle_consent'));
        add_action('wp_ajax_nopriv_ccp_set_consent', array($this, 'handle_consent'));
    }
    
    public function handle_consent() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ccp_nonce')) {
            wp_die('Security check failed');
        }
        
        $consent = isset($_POST['consent']) ? $_POST['consent'] : '';
        $options = get_option('ccp_settings');
        $expire_days = isset($options['expire_days']) ? intval($options['expire_days']) : 30;
        
        if ($consent === 'accept') {
            // Set cookie for 30 days
            setcookie('ccp_consent', 'accepted', time() + (86400 * $expire_days), '/');
            wp_send_json_success(array('message' => 'Cookie accepted'));
        } elseif ($consent === 'decline') {
            // Set cookie for session only
            setcookie('ccp_consent', 'declined', 0, '/');
            wp_send_json_success(array('message' => 'Cookie declined'));
        } else {
            wp_send_json_error(array('message' => 'Invalid consent value'));
        }
    }
}