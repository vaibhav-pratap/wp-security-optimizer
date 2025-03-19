<?php
declare(strict_types=1);

class WPSOSocialLogin {
    private static ?self $instance = null;
    private WPSOSocialProviders $providers;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->providers = new WPSOSocialProviders();
        if (get_option('wpso_social_login_enabled')) {
            add_action('login_form', [$this, 'render_login_buttons']);
            add_action('init', [$this, 'handle_callback']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        }
    }

    public function render_login_buttons(): void {
        include WPSO_PLUGIN_DIR . 'templates/login-buttons.php';
    }

    public function handle_callback(): void {
        if (!isset($_GET['wpso_social']) || !($provider = sanitize_text_field($_GET['wpso_social']))) return;
        
        if (isset($_GET['code'])) {
            $user_data = $this->providers->authenticate($provider, $_GET['code']);
            if (is_wp_error($user_data)) {
                wp_die($user_data->get_error_message(), __('Authentication Error', 'wp-security-optimizer'), ['response' => 400]);
            }

            $user = $this->login_or_register($user_data);
            if ($user instanceof WP_User) {
                wp_set_auth_cookie($user->ID);
                wp_safe_redirect(home_url());
                exit;
            }
        } else {
            $this->providers->redirect_to_auth($provider);
        }
    }

    private function login_or_register(array $user_data): ?WP_User {
        $email = sanitize_email($user_data['email']);
        $user = get_user_by('email', $email);
        
        if (!$user) {
            $username = sanitize_user($user_data['name']);
            $user_id = wp_create_user($username, wp_generate_password(), $email);
            if (is_wp_error($user_id)) return null;
            $user = get_user_by('id', $user_id);
            update_user_meta($user->ID, 'first_name', sanitize_text_field($user_data['first_name']));
            update_user_meta($user->ID, 'last_name', sanitize_text_field($user_data['last_name']));
        }
        
        return $user;
    }

    public function enqueue_assets(): void {
        wp_enqueue_style('wpso-frontend-style', WPSO_PLUGIN_URL . 'assets/css/frontend-style.css', [], WPSO_VERSION);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');
        wp_enqueue_script('wpso-frontend-script', WPSO_PLUGIN_URL . 'assets/js/frontend-script.js', ['jquery'], WPSO_VERSION, true);
    }
}