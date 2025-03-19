<?php
declare(strict_types=1);

class WPSOSocialProviders {
    private array $providers = [
        'google' => ['auth_url' => 'https://accounts.google.com/o/oauth2/auth', 'scope' => 'email profile'],
        'linkedin' => ['auth_url' => 'https://www.linkedin.com/oauth/v2/authorization', 'scope' => 'r_liteprofile r_emailaddress'],
        'facebook' => ['auth_url' => 'https://www.facebook.com/v13.0/dialog/oauth', 'scope' => 'email'],
        'github' => ['auth_url' => 'https://github.com/login/oauth/authorize', 'scope' => 'user:email']
    ];

    public function get_enabled_providers(): array {
        $enabled = [];
        foreach ($this->providers as $provider => $config) {
            if (get_option("wpso_{$provider}_enabled")) {
                $enabled[$provider] = $config;
            }
        }
        return $enabled;
    }
}