<?php
class CCP_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('Cookie Consent Settings', 'cookie-consent'),
            __('Cookie Consent', 'cookie-consent'),
            'manage_options',
            'cookie-consent-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function register_settings() {
        register_setting('ccp_settings_group', 'ccp_settings');
        
        add_settings_section(
            'ccp_main_section',
            __('Popup Settings', 'cookie-consent'),
            null,
            'cookie-consent-settings'
        );
        
        // Popup Text
        add_settings_field(
            'popup_text',
            __('Popup Message', 'cookie-consent'),
            array($this, 'render_textarea_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'popup_text',
                'default' => __('We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.', 'cookie-consent')
            )
        );    
        add_settings_field(
            'popup_heading',
            __('Popup Heading', 'cookie-consent'),
            array($this, 'render_heading_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'popup_heading',
                'default' => __('Warning', 'cookie-consent')
            )
        );
        
        // Accept Button Text
        add_settings_field(
            'accept_text',
            __('Accept Button Text', 'cookie-consent'),
            array($this, 'render_text_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'accept_text',
                'default' => __('Accept', 'cookie-consent')
            )
        );
        
        // Decline Button Text
        add_settings_field(
            'decline_text',
            __('Decline Button Text', 'cookie-consent'),
            array($this, 'render_text_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'decline_text',
                'default' => __('Decline', 'cookie-consent')
            )
        );
        
        // Terms Page Link
        add_settings_field(
            'terms_page',
            __('Terms & Conditions Page', 'cookie-consent'),
            array($this, 'render_page_select_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'terms_page',
                'default' => ''
            )
        );
        
        // Link Text
        add_settings_field(
            'link_text',
            __('Link Text', 'cookie-consent'),
            array($this, 'render_text_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'link_text',
                'default' => __('View Terms & Conditions', 'cookie-consent')
            )
        );
        
        // Expiration Days
        add_settings_field(
            'expire_days',
            __('Cookie Expiration (days)', 'cookie-consent'),
            array($this, 'render_number_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'expire_days',
                'default' => 30,
                'min' => 1,
                'max' => 365
            )
        );
          add_settings_field(
            'transapi_key',
            __('Google Translate Api key', 'cookie-consent'),
            array($this, 'google_api_key_field'),
            'cookie-consent-settings',
            'ccp_main_section',
            array(
                'id' => 'transapi_key',
                'default' => ''
            )
        );
        // Position
        // add_settings_field(
        //     'position',
        //     __('Popup Position', 'cookie-consent'),
        //     array($this, 'render_select_field'),
        //     'cookie-consent-settings',
        //     'ccp_main_section',
        //     array(
        //         'id' => 'position',
        //         'options' => array(
        //             'bottom' => __('Bottom', 'cookie-consent'),
        //             'bottom-left' => __('Bottom Left', 'cookie-consent'),
        //             'bottom-right' => __('Bottom Right', 'cookie-consent'),
        //             'top' => __('Top', 'cookie-consent')
        //         ),
        //         'default' => 'bottom'
        //     )
        // );
    }
    public function google_api_key_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <input type="text" 
               id="ccp_<?php echo esc_attr($args['id']); ?>" 
               name="ccp_settings[<?php echo esc_attr($args['id']); ?>]" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" />
        <p class="description"><?php _e('Enter your Google Translate API key here.', 'cookie-consent'); ?></p>
        <?php
    }
    
    public function render_heading_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <input type="text" 
               id="ccp_<?php echo esc_attr($args['id']); ?>" 
               name="ccp_settings[<?php echo esc_attr($args['id']); ?>]" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" />
        <?php
    }
    public function render_text_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <input type="text" 
               id="ccp_<?php echo esc_attr($args['id']); ?>" 
               name="ccp_settings[<?php echo esc_attr($args['id']); ?>]" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" />
        <?php
    }
    
    public function render_textarea_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <textarea id="ccp_<?php echo esc_attr($args['id']); ?>" 
                  name="ccp_settings[<?php echo esc_attr($args['id']); ?>]" 
                  rows="3" 
                  class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php
    }
    
    public function render_number_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <input type="number" 
               id="ccp_<?php echo esc_attr($args['id']); ?>" 
               name="ccp_settings[<?php echo esc_attr($args['id']); ?>]" 
               value="<?php echo esc_attr($value); ?>" 
               min="<?php echo esc_attr($args['min']); ?>" 
               max="<?php echo esc_attr($args['max']); ?>" 
               class="small-text" />
        <?php
    }
    
    public function render_select_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <select id="ccp_<?php echo esc_attr($args['id']); ?>" 
                name="ccp_settings[<?php echo esc_attr($args['id']); ?>]">
            <?php foreach ($args['options'] as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    public function render_page_select_field($args) {
        $options = get_option('ccp_settings');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        $pages = get_pages();
        ?>
        <select id="ccp_<?php echo esc_attr($args['id']); ?>" 
                name="ccp_settings[<?php echo esc_attr($args['id']); ?>]">
            <option value=""><?php _e('-- Select a page --', 'cookie-consent'); ?></option>
            <?php foreach ($pages as $page): ?>
                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($value, $page->ID); ?>>
                    <?php echo esc_html($page->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Cookie Consent Settings', 'cookie-consent'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ccp_settings_group');
                do_settings_sections('cookie-consent-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}