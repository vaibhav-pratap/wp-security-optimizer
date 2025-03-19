<?php
declare(strict_types=1);

class WPSecurityOptimizer {
    private static ?self $instance = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->init_security_features();
        $this->init_admin_interface();
    }

    private function init_security_features(): void {
        // Only apply security features if plugin is configured
        if (get_option('wpso_configured')) {
            add_filter('xmlrpc_methods', [$this, 'restrict_xmlrpc_methods'], PHP_INT_MAX);
            add_filter('query_vars', [$this, 'sanitize_query_vars']);
            add_action('init', [$this, 'secure_input_data']);
            add_filter('pre_get_posts', [$this, 'prevent_sql_injection']);
            add_action('wp_loaded', [$this, 'monitor_file_access']);
            add_action('send_headers', [$this, 'add_security_headers']);
            add_filter('login_errors', [$this, 'hide_login_errors']);
            add_action('wp_login_failed', [$this, 'handle_login_failure']);
            add_action('rest_api_init', [$this, 'restrict_rest_api']);
        }
        add_action('admin_notices', [$this, 'display_setup_notice']);
    }

    private function init_admin_interface(): void {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        // Set configured flag when settings are saved
        add_action('update_option_wpso_security_hardening', [$this, 'mark_configured']);
    }

    public function restrict_xmlrpc_methods(array $methods): array {
        return array_intersect_key($methods, array_flip(['wp.getUsersBlogs', 'wp.getProfile', 'wp.getPosts', 'wp.newPost', 'wp.editPost']));
    }

    public function sanitize_query_vars(array $vars): array {
        return array_map([$this, 'deep_sanitize'], $vars);
    }

    public function secure_input_data(): void {
        $_GET = $this->sanitize_input($_GET);
        $_POST = $this->sanitize_input($_POST);
        $_COOKIE = $this->sanitize_input($_COOKIE);
        $_REQUEST = $this->sanitize_input($_REQUEST);
    }

    public function prevent_sql_injection(WP_Query $query): WP_Query {
        if (!is_admin() && $query->is_main_query() && ($search = $query->get('s'))) {
            $query->set('s', preg_replace('/[\'";#\/*\\\\]/', '', $search));
        }
        return $query;
    }

    public function monitor_file_access(): void {
        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
            return; // Always allow wp-login.php
        }

        if (get_option('wpso_security_hardening') && 
            preg_match('/(\.php|\.sql|\.ini|\.htaccess)$/i', $_SERVER['REQUEST_URI']) && 
            !preg_match('/(wp-admin|wp-includes|wp-content)/', $_SERVER['REQUEST_URI'])) {
            wp_die(
                __('Access to this file is restricted by WP Security Optimizer. Please contact the site administrator.', 'wp-security-optimizer'), 
                __('Access Denied', 'wp-security-optimizer'), 
                ['response' => 403]
            );
        }
    }

    public function add_security_headers(): void {
        if (get_option('wpso_security_hardening')) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Content-Security-Policy: upgrade-insecure-requests');
        }
        if (get_option('wpso_page_cache_enabled')) {
            header('Cache-Control: public, max-age=' . get_option('wpso_cache_expiry', 3600));
        }
    }

    public function hide_login_errors(string $error): string {
        if (!get_option('wpso_security_hardening')) {
            return $error; // Show default errors if not configured
        }
        return __('Something went wrong. Please check your credentials or contact support.', 'wp-security-optimizer');
    }

    public function handle_login_failure(string $username): void {
        if (!get_option('wpso_rate_limit_enabled')) return;

        $ip = $_SERVER['REMOTE_ADDR'];
        $transient_key = "wpso_login_attempts_{$ip}";
        $attempts = (int) get_transient($transient_key) + 1;

        set_transient($transient_key, $attempts, 15 * MINUTE_IN_SECONDS);
        if ($attempts >= 5) {
            status_header(429);
            wp_die(
                __('Too many login attempts. Please wait 15 minutes or contact support.', 'wp-security-optimizer'), 
                __('Rate Limit Exceeded', 'wp-security-optimizer'), 
                ['response' => 429]
            );
        }
    }

    public function restrict_rest_api(): void {
        if (!is_user_logged_in() && get_option('wpso_security_hardening')) {
            add_filter('rest_authentication_errors', fn($result) => new WP_Error('rest_disabled', __('REST API restricted to logged-in users.', 'wp-security-optimizer'), ['status' => 403]));
        }
    }

    private function deep_sanitize(mixed $data): mixed {
        return is_array($data) ? array_map([$this, 'deep_sanitize'], $data) : sanitize_text_field(wp_unslash((string) $data));
    }

    private function sanitize_input(mixed $input): mixed {
        if (is_array($input)) {
            return array_map([$this, 'sanitize_input'], $input);
        }
        $input = preg_replace('/(union|select|insert|delete|update|where|drop|truncate|--|\/\*)/i', '', (string) $input);
        return esc_sql(strip_tags(trim($input)));
    }

    public function add_settings_page(): void {
        add_menu_page(
            __('WP Security Optimizer', 'wp-security-optimizer'),
            __('WP Security', 'wp-security-optimizer'),
            'manage_options',
            'wp-security-optimizer',
            [$this, 'render_settings_page'],
            'dashicons-shield',
            80
        );
    }

    public function register_settings(): void {
        $settings = [
            'wpso_recaptcha_enabled', 'wpso_recaptcha_site_key', 'wpso_recaptcha_secret_key',
            'wpso_trustsite_enabled', 'wpso_trustsite_api_key',
            'wpso_social_login_enabled', 'wpso_google_client_id', 'wpso_google_client_secret',
            'wpso_linkedin_client_id', 'wpso_linkedin_client_secret',
            'wpso_facebook_app_id', 'wpso_facebook_app_secret',
            'wpso_github_client_id', 'wpso_github_client_secret',
            'wpso_security_hardening', 'wpso_rate_limit_enabled',
            'wpso_page_cache_enabled', 'wpso_object_cache_enabled',
            'wpso_cache_expiry', 'wpso_cache_exclusions',
            'wpso_otp_login_enabled', 'wpso_otp_email_enabled', 'wpso_otp_phone_enabled',
            'wpso_otp_vendor', 'wpso_otp_twilio_sid', 'wpso_otp_twilio_token', 'wpso_otp_twilio_from',
            'wpso_otp_nexmo_key', 'wpso_otp_nexmo_secret', 'wpso_otp_nexmo_from',
            'wpso_otp_messagebird_key', 'wpso_otp_messagebird_from',
            'wpso_configured'
        ];
        foreach ($settings as $setting) {
            register_setting('wpso_settings', $setting, [
                'type' => $setting === 'wpso_cache_expiry' ? 'integer' : ($setting === 'wpso_configured' ? 'boolean' : 'string'),
                'sanitize_callback' => $setting === 'wpso_cache_expiry' ? 'absint' : 'sanitize_text_field'
            ]);
        }
    }

    public function render_settings_page(): void {
        require_once WPSO_PLUGIN_DIR . 'templates/settings-page.php';
    }

    public function enqueue_admin_assets(string $hook): void {
        if ($hook !== 'toplevel_page_wp-security-optimizer') return;
        wp_enqueue_style('wpso-admin-style', WPSO_PLUGIN_URL . 'assets/css/admin-style.css', [], WPSO_VERSION);
        wp_enqueue_script('wpso-admin-script', WPSO_PLUGIN_URL . 'assets/js/admin-script.js', ['jquery'], WPSO_VERSION, true);
    }

    public function display_setup_notice(): void {
        if (!get_option('wpso_configured') && is_admin()) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . 
                __('WP Security Optimizer is installed but not yet configured. Please visit the <a href="' . admin_url('admin.php?page=wp-security-optimizer') . '">settings page</a> to enable features.', 'wp-security-optimizer') . 
                '</p></div>';
        }
    }

    public function mark_configured(): void {
        update_option('wpso_configured', true);
    }
}