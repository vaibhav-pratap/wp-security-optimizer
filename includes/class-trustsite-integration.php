<?php
declare(strict_types=1);

class WPSOTrustSite {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        if (get_option('wpso_trustsite_enabled')) {
            add_action('wp_footer', [$this, 'add_badge']);
            add_filter('wp_login', [$this, 'log_event'], 10, 2);
        }
    }

    public function add_badge(): void {
        if ($api_key = get_option('wpso_trustsite_api_key')) {
            printf('<script src="https://trustsite.com/api/badge.js" data-api-key="%s" async></script>', esc_attr($api_key));
        }
    }

    public function log_event(string $user_login, WP_User $user): string {
        if ($api_key = get_option('wpso_trustsite_api_key')) {
            wp_remote_post('https://trustsite.com/api/event', [
                'body' => wp_json_encode([
                    'api_key' => $api_key,
                    'event' => 'user_login',
                    'user_id' => $user->ID,
                    'timestamp' => current_time('mysql')
                ]),
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 5
            ]);
        }
        return $user_login;
    }
}