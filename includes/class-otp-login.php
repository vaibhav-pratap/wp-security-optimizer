<?php
declare(strict_types=1);

class WPSOOTPLogin {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        if (get_option('wpso_otp_login_enabled')) {
            add_action('init', [$this, 'handle_otp_request']);
        }
    }

    public function handle_otp_request(): void {
        if (isset($_POST['wpso_otp_action']) && wp_verify_nonce($_POST['_wpnonce'], 'wpso_otp_request')) {
            $identifier = sanitize_text_field($_POST['wpso_otp_identifier']);
            $otp = sprintf("%06d", mt_rand(100000, 999999));
            set_transient("wpso_otp_{$identifier}", $otp, 5 * MINUTE_IN_SECONDS);
            if (get_option('wpso_otp_email_enabled') && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                wp_mail($identifier, __('Your OTP Code', 'wp-security-optimizer'), sprintf(__('Your OTP is: %s', 'wp-security-optimizer'), $otp));
            } elseif (get_option('wpso_otp_phone_enabled')) {
                (new WPSOOTPVendors())->send_phone_otp($identifier, $otp);
            }
            wp_redirect(add_query_arg('otp_sent', '1', wp_login_url()));
            exit;
        } elseif (isset($_POST['wpso_otp_action']) && $_POST['wpso_otp_action'] === 'verify_otp' && wp_verify_nonce($_POST['_wpnonce'], 'wpso_otp_verify')) {
            $identifier = sanitize_text_field($_POST['wpso_otp_identifier']);
            $otp = sanitize_text_field($_POST['wpso_otp_code']);
            if ($otp === get_transient("wpso_otp_{$identifier}")) {
                $user = get_user_by('email', $identifier) ?: get_user_by('login', $identifier);
                if ($user) {
                    wp_set_auth_cookie($user->ID);
                    wp_redirect(admin_url());
                    exit;
                }
            }
            wp_die(__('Invalid OTP.', 'wp-security-optimizer'));
        }
    }
}