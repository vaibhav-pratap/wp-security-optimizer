<?php
declare(strict_types=1);

class WPSOTrustSite {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        if (get_option('wpso_trustsite_enabled')) {
            add_action('wp_footer', [$this, 'add_trustsite_script']);
        }
    }

    public function add_trustsite_script(): void {
        $api_key = get_option('wpso_trustsite_api_key');
        if ($api_key) {
            echo "<script src='https://trustsite.com/api.js?key={$api_key}' async></script>";
        }
    }
}