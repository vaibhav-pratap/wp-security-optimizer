<?php
declare(strict_types=1);

class WPSOSocialProviders {
    private array $configs = [
        'google' => [
            'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'api_url' => 'https://www.googleapis.com/oauth2/v3/userinfo',
            'scope' => 'email profile openid'
        ],
        'linkedin' => [
            'auth_url' => 'https://www.linkedin.com/oauth/v2/authorization',
            'token_url' => 'https://www.linkedin.com/oauth/v2/accessToken',
            'api_url' => 'https://api.linkedin.com/v2/userinfo',
            'scope' => 'profile email openid'
        ],
        'facebook' => [
            'auth_url' => 'https://www.facebook.com/v19.0/dialog/oauth',
            'token_url' => 'https://graph.facebook.com/v19.0/oauth/access_token',
            'api_url' => 'https://graph.facebook.com/me?fields=id,name,email,first_name,last_name',
            'scope' => 'email public_profile'
        ],
        'github' => [
            'auth_url' => 'https://github.com/login/oauth/authorize',
            'token_url' => 'https://github.com/login/oauth/access_token',
            'api_url' => 'https://api.github.com/user',
            'scope' => 'user:email read:user'
        ]
    ];

    public function redirect_to_auth(string $provider): never {
        $config = $this->configs[$provider];
        $client_id = get_option("wpso_{$provider}_client_id");
        $redirect_uri = add_query_arg('wpso_social', $provider, wp_login_url());
        
        $url = add_query_arg([
            'client_id' => $client_id,
            'redirect_uri' => urlencode($redirect_uri),
            'response_type' => 'code',
            'scope' => $config['scope'],
            'state' => wp_create_nonce('wpso_social_' . $provider)
        ], $config['auth_url']);
        
        wp_redirect($url);
        exit;
    }

    public function authenticate(string $provider, string $code): array|WP_Error {
        if (!wp_verify_nonce($_GET['state'] ?? '', 'wpso_social_' . $provider)) {
            return new WP_Error('invalid_state', __('Security check failed', 'wp-security-optimizer'));
        }

        $config = $this->configs[$provider];
        $client_id = get_option("wpso_{$provider}_client_id");
        $client_secret = get_option("wpso_{$provider}_client_secret");
        $redirect_uri = add_query_arg('wpso_social', $provider, wp_login_url());

        $token_response = wp_remote_post($config['token_url'], [
            'body' => [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'code' => $code,
                'grant_type' => 'authorization_code'
            ],
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 10
        ]);

        if (is_wp_error($token_response)) return $token_response;
        $token_data = json_decode(wp_remote_retrieve_body($token_response), true);
        if (empty($token_data['access_token'])) return new WP_Error('token_failed', __('Failed to get access token', 'wp-security-optimizer'));

        $user_response = wp_remote_get($config['api_url'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token_data['access_token'],
                'Accept' => 'application/json'
            ],
            'timeout' => 10
        ]);

        if (is_wp_error($user_response)) return $user_response;
        $user_data = json_decode(wp_remote_retrieve_body($user_response), true);

        return $this->normalize_user_data($provider, $user_data);
    }

    private function normalize_user_data(string $provider, array $data): array {
        return match ($provider) {
            'google' => [
                'email' => $data['email'],
                'name' => $data['name'],
                'first_name' => $data['given_name'],
                'last_name' => $data['family_name']
            ],
            'linkedin' => [
                'email' => $data['email'],
                'name' => $data['name'],
                'first_name' => $data['given_name'],
                'last_name' => $data['family_name']
            ],
            'facebook' => [
                'email' => $data['email'],
                'name' => $data['name'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name']
            ],
            'github' => [
                'email' => $data['email'],
                'name' => $data['name'] ?: $data['login'],
                'first_name' => ($name = $data['name']) ? explode(' ', $name)[0] : '',
                'last_name' => $name ? end(explode(' ', $name)) : ''
            ]
        };
    }
}