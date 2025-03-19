<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1 class="mb-4"><?php _e('WP Security Optimizer', 'wp-security-optimizer'); ?></h1>
    <?php if (isset($_GET['cache_cleared'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php _e('Cache cleared successfully!', 'wp-security-optimizer'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!get_option('wpso_configured')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php _e('Please configure and save settings to enable plugin features.', 'wp-security-optimizer'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="options.php" id="wpso-settings-form">
        <?php settings_fields('wpso_settings'); ?>
        <div class="row">
            <div class="col-md-3">
                <div class="nav flex-column nav-pills" id="wpsoTabs" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="true">
                        <i class="bi bi-shield-lock me-2"></i><?php _e('Security', 'wp-security-optimizer'); ?>
                    </button>
                    <button class="nav-link" id="captcha-tab" data-bs-toggle="pill" data-bs-target="#captcha" type="button" role="tab" aria-controls="captcha" aria-selected="false">
                        <i class="bi bi-check-circle me-2"></i><?php _e('CAPTCHA', 'wp-security-optimizer'); ?>
                    </button>
                    <button class="nav-link" id="trustsite-tab" data-bs-toggle="pill" data-bs-target="#trustsite" type="button" role="tab" aria-controls="trustsite" aria-selected="false">
                        <i class="bi bi-award me-2"></i><?php _e('Trust Site', 'wp-security-optimizer'); ?>
                    </button>
                    <button class="nav-link" id="social-tab" data-bs-toggle="pill" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">
                        <i class="bi bi-person-circle me-2"></i><?php _e('Social Login', 'wp-security-optimizer'); ?>
                    </button>
                    <button class="nav-link" id="cache-tab" data-bs-toggle="pill" data-bs-target="#cache" type="button" role="tab" aria-controls="cache" aria-selected="false">
                        <i class="bi bi-speedometer me-2"></i><?php _e('Cache', 'wp-security-optimizer'); ?>
                    </button>
                    <button class="nav-link" id="otp-tab" data-bs-toggle="pill" data-bs-target="#otp" type="button" role="tab" aria-controls="otp" aria-selected="false">
                        <i class="bi bi-phone me-2"></i><?php _e('OTP Login', 'wp-security-optimizer'); ?>
                    </button>
                    <button class="nav-link" id="smtp-tab" data-bs-toggle="pill" data-bs-target="#smtp" type="button" role="tab" aria-controls="smtp" aria-selected="false">
                        <i class="bi bi-envelope me-2"></i><?php _e('SMTP', 'wp-security-optimizer'); ?>
                    </button>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-secondary w-100" id="toggle-dark-mode">
                        <i class="bi bi-moon me-2"></i><?php _e('Toggle Dark Mode', 'wp-security-optimizer'); ?>
                    </button>
                </div>
            </div>

            <div class="col-md-9">
                <div class="tab-content" id="wpsoTabContent">
                    <div class="tab-pane fade show active" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <h2><?php _e('Security Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_security_hardening" id="security_hardening" value="1" <?php checked(1, get_option('wpso_security_hardening')); ?>>
                            <label class="form-check-label" for="security_hardening"><?php _e('Enable Security Hardening', 'wp-security-optimizer'); ?></label>
                            <small class="form-text text-muted"><?php _e('Restricts XML-RPC, adds security headers, and more.', 'wp-security-optimizer'); ?></small>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_rate_limit_enabled" id="rate_limit_enabled" value="1" <?php checked(1, get_option('wpso_rate_limit_enabled')); ?>>
                            <label class="form-check-label" for="rate_limit_enabled"><?php _e('Enable Login Rate Limiting', 'wp-security-optimizer'); ?></label>
                            <small class="form-text text-muted"><?php _e('Limits login attempts to 5 per 15 minutes.', 'wp-security-optimizer'); ?></small>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="captcha" role="tabpanel" aria-labelledby="captcha-tab">
                        <h2><?php _e('CAPTCHA Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="mb-3">
                            <label for="captcha_vendor" class="form-label"><?php _e('CAPTCHA Vendor', 'wp-security-optimizer'); ?></label>
                            <select class="form-select" name="wpso_captcha_vendor" id="captcha_vendor">
                                <option value="recaptcha" <?php selected('recaptcha', get_option('wpso_captcha_vendor')); ?>>Google reCAPTCHA</option>
                                <option value="hcaptcha" <?php selected('hcaptcha', get_option('wpso_captcha_vendor')); ?>>hCaptcha</option>
                                <option value="turnstile" <?php selected('turnstile', get_option('wpso_captcha_vendor')); ?>>Cloudflare Turnstile</option>
                            </select>
                        </div>
                        <div id="recaptcha-settings" class="captcha-settings" style="display: <?php echo get_option('wpso_captcha_vendor') === 'recaptcha' ? 'block' : 'none'; ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="wpso_recaptcha_enabled" id="recaptcha_enabled" value="1" <?php checked(1, get_option('wpso_recaptcha_enabled')); ?>>
                                <label class="form-check-label" for="recaptcha_enabled"><?php _e('Enable reCAPTCHA', 'wp-security-optimizer'); ?></label>
                            </div>
                            <div class="mb-3">
                                <label for="recaptcha_site_key" class="form-label"><?php _e('Site Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_recaptcha_site_key" id="recaptcha_site_key" value="<?php echo esc_attr(get_option('wpso_recaptcha_site_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="recaptcha_secret_key" class="form-label"><?php _e('Secret Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_recaptcha_secret_key" id="recaptcha_secret_key" value="<?php echo esc_attr(get_option('wpso_recaptcha_secret_key')); ?>">
                            </div>
                        </div>
                        <div id="hcaptcha-settings" class="captcha-settings" style="display: <?php echo get_option('wpso_captcha_vendor') === 'hcaptcha' ? 'block' : 'none'; ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="wpso_hcaptcha_enabled" id="hcaptcha_enabled" value="1" <?php checked(1, get_option('wpso_hcaptcha_enabled')); ?>>
                                <label class="form-check-label" for="hcaptcha_enabled"><?php _e('Enable hCaptcha', 'wp-security-optimizer'); ?></label>
                            </div>
                            <div class="mb-3">
                                <label for="hcaptcha_site_key" class="form-label"><?php _e('Site Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_hcaptcha_site_key" id="hcaptcha_site_key" value="<?php echo esc_attr(get_option('wpso_hcaptcha_site_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="hcaptcha_secret_key" class="form-label"><?php _e('Secret Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_hcaptcha_secret_key" id="hcaptcha_secret_key" value="<?php echo esc_attr(get_option('wpso_hcaptcha_secret_key')); ?>">
                            </div>
                        </div>
                        <div id="turnstile-settings" class="captcha-settings" style="display: <?php echo get_option('wpso_captcha_vendor') === 'turnstile' ? 'block' : 'none'; ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="wpso_turnstile_enabled" id="turnstile_enabled" value="1" <?php checked(1, get_option('wpso_turnstile_enabled')); ?>>
                                <label class="form-check-label" for="turnstile_enabled"><?php _e('Enable Cloudflare Turnstile', 'wp-security-optimizer'); ?></label>
                            </div>
                            <div class="mb-3">
                                <label for="turnstile_site_key" class="form-label"><?php _e('Site Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_turnstile_site_key" id="turnstile_site_key" value="<?php echo esc_attr(get_option('wpso_turnstile_site_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="turnstile_secret_key" class="form-label"><?php _e('Secret Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_turnstile_secret_key" id="turnstile_secret_key" value="<?php echo esc_attr(get_option('wpso_turnstile_secret_key')); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="trustsite" role="tabpanel" aria-labelledby="trustsite-tab">
                        <h2><?php _e('Trust Site Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_trustsite_enabled" id="trustsite_enabled" value="1" <?php checked(1, get_option('wpso_trustsite_enabled')); ?>>
                            <label class="form-check-label" for="trustsite_enabled"><?php _e('Enable Trust Site', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="mb-3">
                            <label for="trustsite_api_key" class="form-label"><?php _e('API Key', 'wp-security-optimizer'); ?></label>
                            <input type="text" class="form-control" name="wpso_trustsite_api_key" id="trustsite_api_key" value="<?php echo esc_attr(get_option('wpso_trustsite_api_key')); ?>">
                        </div>
                    </div>

                    <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                        <h2><?php _e('Social Login Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_social_login_enabled" id="social_login_enabled" value="1" <?php checked(1, get_option('wpso_social_login_enabled')); ?>>
                            <label class="form-check-label" for="social_login_enabled"><?php _e('Enable Social Login', 'wp-security-optimizer'); ?></label>
                        </div>
                        <?php foreach (['google' => 'Google', 'linkedin' => 'LinkedIn', 'facebook' => 'Facebook', 'github' => 'GitHub'] as $key => $label): ?>
                            <div class="mb-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input social-toggle" type="checkbox" name="wpso_<?php echo $key; ?>_enabled" id="<?php echo $key; ?>_enabled" value="1" <?php checked(1, get_option("wpso_{$key}_enabled")); ?>>
                                    <label class="form-check-label" for="<?php echo $key; ?>_enabled"><?php echo esc_html("Enable {$label}"); ?></label>
                                </div>
                                <div class="social-settings ms-4" id="<?php echo $key; ?>-settings" style="display: <?php echo get_option("wpso_{$key}_enabled") ? 'block' : 'none'; ?>;">
                                    <div class="mb-3">
                                        <label for="<?php echo $key; ?>_client_id" class="form-label"><?php echo esc_html("{$label} Client ID"); ?></label>
                                        <input type="text" class="form-control" name="wpso_<?php echo $key; ?>_client_id" id="<?php echo $key; ?>_client_id" value="<?php echo esc_attr(get_option("wpso_{$key}_client_id")); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="<?php echo $key; ?>_client_secret" class="form-label"><?php echo esc_html("{$label} Client Secret"); ?></label>
                                        <input type="text" class="form-control" name="wpso_<?php echo $key; ?>_client_secret" id="<?php echo $key; ?>_client_secret" value="<?php echo esc_attr(get_option("wpso_{$key}_client_secret")); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="tab-pane fade" id="cache" role="tabpanel" aria-labelledby="cache-tab">
                        <h2><?php _e('Cache Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_page_cache_enabled" id="page_cache_enabled" value="1" <?php checked(1, get_option('wpso_page_cache_enabled')); ?>>
                            <label class="form-check-label" for="page_cache_enabled"><?php _e('Enable Page Cache', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_object_cache_enabled" id="object_cache_enabled" value="1" <?php checked(1, get_option('wpso_object_cache_enabled')); ?>>
                            <label class="form-check-label" for="object_cache_enabled"><?php _e('Enable Object Cache', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="mb-3">
                            <label for="cache_expiry" class="form-label"><?php _e('Cache Expiry (seconds)', 'wp-security-optimizer'); ?></label>
                            <input type="number" class="form-control" name="wpso_cache_expiry" id="cache_expiry" value="<?php echo esc_attr(get_option('wpso_cache_expiry', 3600)); ?>" min="60">
                            <small class="form-text text-muted"><?php _e('Default: 3600 (1 hour)', 'wp-security-optimizer'); ?></small>
                        </div>
                        <div class="mb-3">
                            <label for="cache_exclusions" class="form-label"><?php _e('Cache Exclusions', 'wp-security-optimizer'); ?></label>
                            <textarea class="form-control" name="wpso_cache_exclusions" id="cache_exclusions" rows="5"><?php echo esc_textarea(get_option('wpso_cache_exclusions')); ?></textarea>
                            <small class="form-text text-muted"><?php _e('Enter URL patterns to exclude (one per line, e.g., /shop/*)', 'wp-security-optimizer'); ?></small>
                        </div>
                        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wpso_clear_cache'), 'wpso_clear_cache'); ?>" class="btn btn-outline-danger"><?php _e('Clear All Cache', 'wp-security-optimizer'); ?></a>
                    </div>

                    <div class="tab-pane fade" id="otp" role="tabpanel" aria-labelledby="otp-tab">
                        <h2><?php _e('OTP Login Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_otp_login_enabled" id="otp_login_enabled" value="1" <?php checked(1, get_option('wpso_otp_login_enabled')); ?>>
                            <label class="form-check-label" for="otp_login_enabled"><?php _e('Enable OTP Login', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_otp_email_enabled" id="otp_email_enabled" value="1" <?php checked(1, get_option('wpso_otp_email_enabled')); ?>>
                            <label class="form-check-label" for="otp_email_enabled"><?php _e('Enable Email OTP', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_otp_phone_enabled" id="otp_phone_enabled" value="1" <?php checked(1, get_option('wpso_otp_phone_enabled')); ?>>
                            <label class="form-check-label" for="otp_phone_enabled"><?php _e('Enable Phone OTP', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="mb-3">
                            <label for="otp_vendor" class="form-label"><?php _e('OTP Vendor', 'wp-security-optimizer'); ?></label>
                            <select class="form-select" name="wpso_otp_vendor" id="otp_vendor">
                                <?php foreach ((new WPSOOTPVendors())->get_available_vendors() as $vendor): ?>
                                    <option value="<?php echo $vendor; ?>" <?php selected($vendor, get_option('wpso_otp_vendor')); ?>><?php echo ucfirst($vendor); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php $otp_vendors = new WPSOOTPVendors(); ?>
                        <?php foreach ($otp_vendors->get_available_vendors() as $vendor): ?>
                            <div class="otp-settings" id="<?php echo $vendor; ?>-settings" style="display: <?php echo get_option('wpso_otp_vendor') === $vendor ? 'block' : 'none'; ?>;">
                                <?php if (in_array('sid', $otp_vendors->vendors[$vendor]['fields'])): ?>
                                    <div class="mb-3">
                                        <label for="otp_<?php echo $vendor; ?>_sid" class="form-label"><?php echo ucfirst($vendor); ?> SID</label>
                                        <input type="text" class="form-control" name="wpso_otp_<?php echo $vendor; ?>_sid" id="otp_<?php echo $vendor; ?>_sid" value="<?php echo esc_attr(get_option("wpso_otp_{$vendor}_sid")); ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if (in_array('key', $otp_vendors->vendors[$vendor]['fields'])): ?>
                                    <div class="mb-3">
                                        <label for="otp_<?php echo $vendor; ?>_key" class="form-label"><?php echo ucfirst($vendor); ?> API Key</label>
                                        <input type="text" class="form-control" name="wpso_otp_<?php echo $vendor; ?>_key" id="otp_<?php echo $vendor; ?>_key" value="<?php echo esc_attr(get_option("wpso_otp_{$vendor}_key")); ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if (in_array('token', $otp_vendors->vendors[$vendor]['fields'])): ?>
                                    <div class="mb-3">
                                        <label for="otp_<?php echo $vendor; ?>_token" class="form-label"><?php echo ucfirst($vendor); ?> Auth Token</label>
                                        <input type="text" class="form-control" name="wpso_otp_<?php echo $vendor; ?>_token" id="otp_<?php echo $vendor; ?>_token" value="<?php echo esc_attr(get_option("wpso_otp_{$vendor}_token")); ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if (in_array('secret', $otp_vendors->vendors[$vendor]['fields'])): ?>
                                    <div class="mb-3">
                                        <label for="otp_<?php echo $vendor; ?>_secret" class="form-label"><?php echo ucfirst($vendor); ?> Secret</label>
                                        <input type="text" class="form-control" name="wpso_otp_<?php echo $vendor; ?>_secret" id="otp_<?php echo $vendor; ?>_secret" value="<?php echo esc_attr(get_option("wpso_otp_{$vendor}_secret")); ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="otp_<?php echo $vendor; ?>_from" class="form-label"><?php echo ucfirst($vendor); ?> From Number</label>
                                    <input type="text" class="form-control" name="wpso_otp_<?php echo $vendor; ?>_from" id="otp_<?php echo $vendor; ?>_from" value="<?php echo esc_attr(get_option("wpso_otp_{$vendor}_from")); ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                        <h2><?php _e('SMTP Settings', 'wp-security-optimizer'); ?></h2>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="wpso_smtp_enabled" id="smtp_enabled" value="1" <?php checked(1, get_option('wpso_smtp_enabled')); ?>>
                            <label class="form-check-label" for="smtp_enabled"><?php _e('Enable SMTP', 'wp-security-optimizer'); ?></label>
                        </div>
                        <div class="mb-3">
                            <label for="smtp_vendor" class="form-label"><?php _e('SMTP Vendor', 'wp-security-optimizer'); ?></label>
                            <select class="form-select" name="wpso_smtp_vendor" id="smtp_vendor">
                                <?php foreach ((new WPSOSmtpVendors())->get_available_vendors() as $vendor): ?>
                                    <option value="<?php echo $vendor; ?>" <?php selected($vendor, get_option('wpso_smtp_vendor')); ?>><?php echo ucfirst($vendor); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="brevo-settings" class="smtp-settings" style="display: <?php echo get_option('wpso_smtp_vendor') === 'brevo' ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label for="smtp_brevo_key" class="form-label"><?php _e('Brevo API Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_brevo_key" id="smtp_brevo_key" value="<?php echo esc_attr(get_option('wpso_smtp_brevo_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="smtp_brevo_host" class="form-label"><?php _e('Brevo Host', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_brevo_host" id="smtp_brevo_host" value="<?php echo esc_attr(get_option('wpso_smtp_brevo_host', 'smtp-relay.brevo.com')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="smtp_brevo_port" class="form-label"><?php _e('Brevo Port', 'wp-security-optimizer'); ?></label>
                                <input type="number" class="form-control" name="wpso_smtp_brevo_port" id="smtp_brevo_port" value="<?php echo esc_attr(get_option('wpso_smtp_brevo_port', 587)); ?>">
                            </div>
                        </div>
                        <div id="mailgun-settings" class="smtp-settings" style="display: <?php echo get_option('wpso_smtp_vendor') === 'mailgun' ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label for="smtp_mailgun_key" class="form-label"><?php _e('Mailgun API Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_mailgun_key" id="smtp_mailgun_key" value="<?php echo esc_attr(get_option('wpso_smtp_mailgun_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="smtp_mailgun_domain" class="form-label"><?php _e('Mailgun Domain', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_mailgun_domain" id="smtp_mailgun_domain" value="<?php echo esc_attr(get_option('wpso_smtp_mailgun_domain')); ?>">
                            </div>
                        </div>
                        <div id="sendgrid-settings" class="smtp-settings" style="display: <?php echo get_option('wpso_smtp_vendor') === 'sendgrid' ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label for="smtp_sendgrid_key" class="form-label"><?php _e('SendGrid API Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_sendgrid_key" id="smtp_sendgrid_key" value="<?php echo esc_attr(get_option('wpso_smtp_sendgrid_key')); ?>">
                            </div>
                        </div>
                        <div id="amazon-settings" class="smtp-settings" style="display: <?php echo get_option('wpso_smtp_vendor') === 'amazon' ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label for="smtp_amazon_access_key" class="form-label"><?php _e('Amazon SES Access Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_amazon_access_key" id="smtp_amazon_access_key" value="<?php echo esc_attr(get_option('wpso_smtp_amazon_access_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="smtp_amazon_secret_key" class="form-label"><?php _e('Amazon SES Secret Key', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_amazon_secret_key" id="smtp_amazon_secret_key" value="<?php echo esc_attr(get_option('wpso_smtp_amazon_secret_key')); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="smtp_amazon_region" class="form-label"><?php _e('Amazon SES Region', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_amazon_region" id="smtp_amazon_region" value="<?php echo esc_attr(get_option('wpso_smtp_amazon_region', 'us-east-1')); ?>">
                            </div>
                        </div>
                        <div id="postmark-settings" class="smtp-settings" style="display: <?php echo get_option('wpso_smtp_vendor') === 'postmark' ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label for="smtp_postmark_token" class="form-label"><?php _e('Postmark Server Token', 'wp-security-optimizer'); ?></label>
                                <input type="text" class="form-control" name="wpso_smtp_postmark_token" id="smtp_postmark_token" value="<?php echo esc_attr(get_option('wpso_smtp_postmark_token')); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php submit_button(__('Save Settings', 'wp-security-optimizer'), 'btn btn-primary mt-3', 'submit', false); ?>
            </div>
        </div>
    </form>
</div>