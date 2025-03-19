<?php
/*
Plugin Name: WP Security Optimizer
Description: Advanced WordPress security with social login, caching, and dynamic OTP login
Version: 3.3.0
Author: Vaibhav
Author URI: https://exiverlabs.co.in
License: GPL-2.0+
Text Domain: wp-security-optimizer
Requires PHP: 8.0
Requires at least: 6.0
Tested up to: 6.5
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check compatibility
if (version_compare(PHP_VERSION, '8.0.0', '<') || version_compare(get_bloginfo('version'), '6.0', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>WP Security Optimizer requires PHP 8.0+ and WordPress 6.0+. Please upgrade your environment.</p></div>';
    });
    return;
}

// Define constants
define('WPSO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPSO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPSO_VERSION', '3.3.0');

// Include required classes
require_once WPSO_PLUGIN_DIR . 'includes/class-wp-security-optimizer.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-recaptcha-integration.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-trustsite-integration.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-social-login.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-social-providers.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-cache-manager.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-otp-login.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-otp-vendors.php';

// Initialize the plugin
function wp_security_optimizer_init(): void {
    WPSecurityOptimizer::get_instance();
    WPSORecaptcha::get_instance();
    WPSOTrustSite::get_instance();
    WPSOSocialLogin::get_instance();
    WPSOCacheManager::get_instance();
    WPSOOTPLogin::get_instance();
}
add_action('plugins_loaded', 'wp_security_optimizer_init');

// Activation hook
register_activation_hook(__FILE__, function(): void {
    $defaults = [
        'wpso_recaptcha_enabled' => false,
        'wpso_trustsite_enabled' => false,
        'wpso_social_login_enabled' => false,
        'wpso_google_client_id' => '',
        'wpso_google_client_secret' => '',
        'wpso_linkedin_client_id' => '',
        'wpso_linkedin_client_secret' => '',
        'wpso_facebook_app_id' => '',
        'wpso_facebook_app_secret' => '',
        'wpso_github_client_id' => '',
        'wpso_github_client_secret' => '',
        'wpso_security_hardening' => true,
        'wpso_rate_limit_enabled' => true,
        'wpso_page_cache_enabled' => false,
        'wpso_object_cache_enabled' => false,
        'wpso_cache_expiry' => 3600,
        'wpso_cache_exclusions' => '',
        'wpso_otp_login_enabled' => false,
        'wpso_otp_email_enabled' => true,
        'wpso_otp_phone_enabled' => false,
        'wpso_otp_vendor' => 'twilio',
        'wpso_otp_twilio_sid' => '',
        'wpso_otp_twilio_token' => '',
        'wpso_otp_twilio_from' => '',
        'wpso_otp_nexmo_key' => '',
        'wpso_otp_nexmo_secret' => '',
        'wpso_otp_nexmo_from' => '',
        'wpso_otp_messagebird_key' => '',
        'wpso_otp_messagebird_from' => ''
    ];
    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }
});