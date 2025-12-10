<?php
/**
 * Plugin Name: WP Cookies Plugin
 * Plugin URI: https://kiyaya.nl/
 * Description: A customizable cookie consent popup for GDPR compliance
 * Version: 1.4.1
 * Author: KIYAYA
 * License: GPL v2 or later
 * Text Domain: cookie-consent
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CCP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CCP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CCP_PLUGIN_DIR . 'includes/admin-settings.php';
require_once CCP_PLUGIN_DIR . 'includes/frontend-display.php';
require_once CCP_PLUGIN_DIR . 'includes/cookie-handler.php';
//require_once CCP_PLUGIN_DIR . 'includes/coockie-translation.php';


// Initialize the plugin
class CookieConsentPopup {
    
    public function __construct() {
        // Initialize hooks
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('cookie-consent', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize components
        new CCP_Admin_Settings();
        new CCP_Frontend_Display();
        new CCP_Cookie_Handler();
        //new CCP_Simple_Translation();
    }
    
}

// Instantiate the plugin
new CookieConsentPopup();

