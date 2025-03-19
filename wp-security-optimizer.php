<?php
/*
Plugin Name: WP Security Optimizer
Description: Advanced WordPress security with social login, caching, OTP, CAPTCHA, and SMTP features
Version: 3.4.0
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
define('WPSO_VERSION', '3.4.0');

// Include required classes
require_once WPSO_PLUGIN_DIR . 'includes/class-wp-security-optimizer.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-captcha-integration.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-trustsite-integration.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-social-login.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-social-providers.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-cache-manager.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-otp-login.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-otp-vendors.php';
require_once WPSO_PLUGIN_DIR . 'includes/class-smtp-vendors.php';

// Initialize the plugin
function wp_security_optimizer_init(): void {
    WPSecurityOptimizer::get_instance();
    WPSOCaptcha::get_instance();
    WPSOTrustSite::get_instance();
    WPSOSocialLogin::get_instance();
    WPSOCacheManager::get_instance();
    WPSOOTPLogin::get_instance();
    WPSOSmtpVendors::get_instance();
}
add_action('plugins_loaded', 'wp_security_optimizer_init');

// Activation hook
register_activation_hook(__FILE__, function(): void {
    $defaults = [
        'wpso_captcha_vendor' => 'recaptcha',
        'wpso_recaptcha_enabled' => false,
        'wpso_recaptcha_site_key' => '',
        'wpso_recaptcha_secret_key' => '',
        'wpso_hcaptcha_enabled' => false,
        'wpso_hcaptcha_site_key' => '',
        'wpso_hcaptcha_secret_key' => '',
        'wpso_turnstile_enabled' => false,
        'wpso_turnstile_site_key' => '',
        'wpso_turnstile_secret_key' => '',
        'wpso_trustsite_enabled' => false,
        'wpso_trustsite_api_key' => '',
        'wpso_social_login_enabled' => false,
        'wpso_google_enabled' => false,
        'wpso_google_client_id' => '',
        'wpso_google_client_secret' => '',
        'wpso_linkedin_enabled' => false,
        'wpso_linkedin_client_id' => '',
        'wpso_linkedin_client_secret' => '',
        'wpso_facebook_enabled' => false,
        'wpso_facebook_app_id' => '',
        'wpso_facebook_app_secret' => '',
        'wpso_github_enabled' => false,
        'wpso_github_client_id' => '',
        'wpso_github_client_secret' => '',
        'wpso_security_hardening' => false,
        'wpso_rate_limit_enabled' => false,
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
        'wpso_otp_messagebird_from' => '',
        'wpso_otp_plivo_sid' => '',
        'wpso_otp_plivo_token' => '',
        'wpso_otp_plivo_from' => '',
        'wpso_otp_clickatell_key' => '',
        'wpso_otp_clickatell_from' => '',
        'wpso_otp_textmagic_key' => '',
        'wpso_otp_textmagic_from' => '',
        'wpso_otp_smsglobal_key' => '',
        'wpso_otp_smsglobal_secret' => '',
        'wpso_otp_smsglobal_from' => '',
        'wpso_otp_bulkvs_key' => '',
        'wpso_otp_bulkvs_secret' => '',
        'wpso_otp_bulkvs_from' => '',
        'wpso_otp_telesign_key' => '',
        'wpso_otp_telesign_secret' => '',
        'wpso_otp_telesign_from' => '',
        'wpso_otp_bandwidth_key' => '',
        'wpso_otp_bandwidth_secret' => '',
        'wpso_otp_bandwidth_from' => '',
        'wpso_smtp_enabled' => false,
        'wpso_smtp_vendor' => 'brevo',
        'wpso_smtp_brevo_key' => '',
        'wpso_smtp_brevo_host' => 'smtp-relay.brevo.com',
        'wpso_smtp_brevo_port' => 587,
        'wpso_smtp_mailgun_key' => '',
        'wpso_smtp_mailgun_domain' => '',
        'wpso_smtp_sendgrid_key' => '',
        'wpso_smtp_amazon_access_key' => '',
        'wpso_smtp_amazon_secret_key' => '',
        'wpso_smtp_amazon_region' => 'us-east-1',
        'wpso_smtp_postmark_token' => '',
        'wpso_configured' => false
    ];
    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }
    if (is_user_logged_in()) {
        wp_set_auth_cookie(get_current_user_id(), true);
    }
});