<?php
declare(strict_types=1);

class WPSOOTPLogin {
    private static ?self $instance = null;
    private WPSOOTPVendors $vendors;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->vendors = new WPSOOTPVendors();
        if (get_option('wpso_otp_login_enabled')) {
            add_action('login_form', [$this, 'render_otp_form']);
            add_action('init', [$this, 'handle_otp_request']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        }
    }

    public function render_otp_form(): void {
        include WPSO_PLUGIN_DIR . 'templates/otp-login-form.php';
    }

    public function handle_otp_request(): void {
        if (!isset($_POST['wpso_otp_action'])) return;

        if ($_POST['wpso_otp_action'] === 'request_otp') {
            $this->request_otp();
        } elseif ($_POST['wpso_otp_action'] === 'verify_otp') {
            $this->verify_otp();
        }
    }

    private function request_otp(): void {
        check_admin_referer('wpso_otp_request');

        $identifier = sanitize_text_field($_POST['wpso_otp_identifier'] ?? '');
        if (empty($identifier)) {
            wp_die(__('Please enter an email or phone number.', 'wp-security-optimizer'), 400);
        }

        $user = $this->get_user_by_identifier($identifier);
        if (!$user) {
            wp_die(__('No user found with this email/phone.', 'wp-security-optimizer'), 404);
        }

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        set_transient("wpso_otp_{$user->ID}", $otp, 5 * MINUTE_IN_SECONDS);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL) && get_option('wpso_otp_email_enabled')) {
            $this->send_email_otp($user->user_email, $otp);
        } elseif (get_option('wpso_otp_phone_enabled')) {
            $this->vendors->send_phone_otp($identifier, $otp);
        }

        wp_safe_redirect(add_query_arg('otp_sent', '1', wp_login_url()));
        exit;
    }

    private function verify_otp(): void {
        check_admin_referer('wpso_otp_verify');

        $identifier = sanitize_text_field($_POST['wpso_otp_identifier'] ?? '');
        $otp = sanitize_text_field($_POST['wpso_otp_code'] ?? '');

        $user = $this->get_user_by_identifier($identifier);
        if (!$user || !$otp) {
            wp_die(__('Invalid request.', 'wp-security-optimizer'), 400);
        }

        $stored_otp = get_transient("wpso_otp_{$user->ID}");
        if ($stored_otp === $otp) {
            delete_transient("wpso_otp_{$user->ID}");
            wp_set_auth_cookie($user->ID);
            wp_safe_redirect(home_url());
            exit;
        }

        wp_die(__('Invalid OTP. Please try again.', 'wp-security-optimizer'), 401);
    }

    private function get_user_by_identifier(string $identifier): ?WP_User {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return get_user_by('email', $identifier);
        }
        return get_users(['meta_key' => 'phone_number', 'meta_value' => $identifier, 'number' => 1])[0] ?? null;
    }

    private function send_email_otp(string $email, string $otp): void {
        $subject = __('Your Login OTP', 'wp-security-optimizer');
        $message = sprintf(__('Your one-time password is: %s. It expires in 5 minutes.', 'wp-security-optimizer'), $otp);
        wp_mail($email, $subject, $message);
    }

    public function enqueue_assets(): void {
        wp_enqueue_style('wpso-frontend-style', WPSO_PLUGIN_URL . 'assets/css/frontend-style.css', [], WPSO_VERSION);
        wp_enqueue_script('wpso-frontend-script', WPSO_PLUGIN_URL . 'assets/js/frontend-script.js', ['jquery'], WPSO_VERSION, true);
    }
}