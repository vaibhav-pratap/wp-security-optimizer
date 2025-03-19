<?php
declare(strict_types=1);

class WPSOCacheManager {
    private static ?self $instance = null;
    private string $cache_dir;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/wpso/';
        $this->init_cache_features();
    }

    private function init_cache_features(): void {
        if (get_option('wpso_page_cache_enabled')) {
            add_action('template_redirect', [$this, 'handle_page_cache'], 1);
            add_action('wp_update_nav_menu', [$this, 'clear_page_cache']);
            add_action('save_post', [$this, 'clear_page_cache']);
            add_action('comment_post', [$this, 'clear_page_cache']);
        }
        if (get_option('wpso_object_cache_enabled')) {
            add_action('init', [$this, 'init_object_cache']);
        }
        add_action('admin_post_wpso_clear_cache', [$this, 'clear_all_cache']);
    }

    public function handle_page_cache(): void {
        if (is_admin() || is_user_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'GET') return;

        $exclusions = array_filter(array_map('trim', explode("\n", get_option('wpso_cache_exclusions', ''))));
        $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        foreach ($exclusions as $exclude) {
            if (fnmatch($exclude, $current_path)) return;
        }

        $cache_key = md5($_SERVER['REQUEST_URI']);
        $cache_file = $this->cache_dir . $cache_key . '.html';

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < (int) get_option('wpso_cache_expiry', 3600)) {
            echo file_get_contents($cache_file);
            exit;
        }

        ob_start(function($buffer) use ($cache_file) {
            if (!is_dir($this->cache_dir)) {
                wp_mkdir_p($this->cache_dir);
            }
            file_put_contents($cache_file, $buffer);
            return $buffer;
        });
    }

    public function init_object_cache(): void {
        if (!wp_using_ext_object_cache()) {
            global $_wp_using_ext_object_cache;
            $_wp_using_ext_object_cache = true;

            add_filter('pre_cache_alloptions', function($alloptions) {
                wp_cache_set('alloptions', $alloptions, 'options', get_option('wpso_cache_expiry', 3600));
                return $alloptions;
            });

            add_filter('pre_option_', function($value, $option) {
                return wp_cache_get($option, 'options') ?: $value;
            }, 10, 2);

            add_action('update_option', function($option) {
                wp_cache_delete($option, 'options');
            });
        }
    }

    public function clear_page_cache(): void {
        if (is_dir($this->cache_dir)) {
            array_map('unlink', glob($this->cache_dir . '*.html'));
        }
    }

    public function clear_all_cache(): void {
        check_admin_referer('wpso_clear_cache');
        $this->clear_page_cache();
        wp_cache_flush();
        wp_safe_redirect(add_query_arg('cache_cleared', '1', wp_get_referer()));
        exit;
    }
}