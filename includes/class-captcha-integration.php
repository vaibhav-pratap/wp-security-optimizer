<?php
declare(strict_types=1);

class WPSORecaptcha {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        if (get_option('wpso_recaptcha_enabled')) {
            add_action('login_form', [$this, 'add_recaptcha']);
            add_filter('wp_authenticate_user', [$this, 'verify_recaptcha'], 10, 2);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        }
    }

    public function add_recaptcha(): void {
        if ($site_key = get_option('wpso_recaptcha_site_key')) {
            printf('<div class="g-recaptcha" data-sitekey="%s"></div>', esc_attr($site_key));
        }
    }

    public function verify_recaptcha(WP_User|WP_Error $user, string $password): WP_User|WP_Error {
        if (!isset($_POST['g-recaptcha-response'])) {
            return new WP_Error('recaptcha_missing', __('Please complete the reCAPTCHA', 'wp-security-optimizer'));
        }
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => get_option('wpso_recaptcha_secret_key'),
                'response' => sanitize_text_field($_POST['g-recaptcha-response']),
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]
        ]);
        if (is_wp_error($response) || !($result = json_decode(wp_remote_retrieve_body($response)))->success) {
            return new WP_Error('recaptcha_failed', __('reCAPTCHA verification failed', 'wp-security-optimizer'));
        }
        return $user;
    }

    public function enqueue_scripts(): void {
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, ['in_footer' => true]);
    }
}