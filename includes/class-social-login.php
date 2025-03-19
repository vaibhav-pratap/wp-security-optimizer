<?php
declare(strict_types=1);

class WPSOSocialLogin {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        if (get_option('wpso_social_login_enabled')) {
            add_action('login_form', [$this, 'render_social_buttons']);
            add_action('init', [$this, 'handle_social_callback']);
        }
    }

    public function render_social_buttons(): void {
        $providers = (new WPSOSocialProviders())->get_enabled_providers();
        foreach ($providers as $provider => $config) {
            $url = $config['auth_url'] . '?' . http_build_query([
                'client_id' => get_option("wpso_{$provider}_client_id"),
                'redirect_uri' => home_url('/wp-login.php?action=wpso_social_callback'),
                'response_type' => 'code',
                'scope' => $config['scope']
            ]);
            echo "<a href='{$url}' class='btn btn-{$provider} mb-2 w-100'><i class='bi bi-{$provider}'></i> " . sprintf(__('Login with %s', 'wp-security-optimizer'), ucfirst($provider)) . "</a>";
        }
    }

    public function handle_social_callback(): void {
        if (isset($_GET['action']) && $_GET['action'] === 'wpso_social_callback' && isset($_GET['code'])) {
            // Placeholder for social login callback logic
            wp_redirect(home_url());
            exit;
        }
    }
}