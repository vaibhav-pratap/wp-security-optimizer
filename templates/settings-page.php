<div class="wrap">
    <h1><?php _e('WP Security Optimizer', 'wp-security-optimizer'); ?></h1>
    <?php if (isset($_GET['cache_cleared'])): ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Cache cleared successfully!', 'wp-security-optimizer'); ?></p></div>
    <?php endif; ?>
    <?php if (!get_option('wpso_configured')): ?>
        <div class="notice notice-info is-dismissible"><p><?php _e('Please configure and save settings to enable plugin features.', 'wp-security-optimizer'); ?></p></div>
    <?php endif; ?>
    <form method="post" action="options.php">
        <?php 
        settings_fields('wpso_settings');
        do_settings_sections('wpso_settings');
        ?>

        <h2 class="title"><?php _e('Security Settings', 'wp-security-optimizer'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Security Hardening', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_security_hardening" value="1" <?php checked(1, get_option('wpso_security_hardening')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable Login Rate Limiting', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_rate_limit_enabled" value="1" <?php checked(1, get_option('wpso_rate_limit_enabled')); ?>></td>
            </tr>
        </table>

        <!-- Rest of the settings remain unchanged -->
        <h2 class="title"><?php _e('reCAPTCHA Settings', 'wp-security-optimizer'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable reCAPTCHA', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_recaptcha_enabled" value="1" <?php checked(1, get_option('wpso_recaptcha_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Site Key', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_recaptcha_site_key" value="<?php echo esc_attr(get_option('wpso_recaptcha_site_key')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Secret Key', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_recaptcha_secret_key" value="<?php echo esc_attr(get_option('wpso_recaptcha_secret_key')); ?>" class="regular-text"></td>
            </tr>
        </table>

        <h2 class="title"><?php _e('Trust Site Settings', 'wp-security-optimizer'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Trust Site', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_trustsite_enabled" value="1" <?php checked(1, get_option('wpso_trustsite_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('API Key', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_trustsite_api_key" value="<?php echo esc_attr(get_option('wpso_trustsite_api_key')); ?>" class="regular-text"></td>
            </tr>
        </table>

        <h2 class="title"><?php _e('Social Login Settings', 'wp-security-optimizer'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Social Login', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_social_login_enabled" value="1" <?php checked(1, get_option('wpso_social_login_enabled')); ?>></td>
            </tr>
            <?php foreach (['google' => 'Google', 'linkedin' => 'LinkedIn', 'facebook' => 'Facebook', 'github' => 'GitHub'] as $key => $label): ?>
                <tr>
                    <th scope="row"><?php echo esc_html($label . ' Client ID'); ?></th>
                    <td><input type="text" name="wpso_<?php echo $key; ?>_client_id" value="<?php echo esc_attr(get_option("wpso_{$key}_client_id")); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html($label . ' Client Secret'); ?></th>
                    <td><input type="text" name="wpso_<?php echo $key; ?>_client_secret" value="<?php echo esc_attr(get_option("wpso_{$key}_client_secret")); ?>" class="regular-text"></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2 class="title"><?php _e('Cache Settings', 'wp-security-optimizer'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Page Cache', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_page_cache_enabled" value="1" <?php checked(1, get_option('wpso_page_cache_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable Object Cache', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_object_cache_enabled" value="1" <?php checked(1, get_option('wpso_object_cache_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Cache Expiry (seconds)', 'wp-security-optimizer'); ?></th>
                <td><input type="number" name="wpso_cache_expiry" value="<?php echo esc_attr(get_option('wpso_cache_expiry', 3600)); ?>" min="60" class="small-text"> <?php _e('Default: 3600 (1 hour)', 'wp-security-optimizer'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Cache Exclusions', 'wp-security-optimizer'); ?></th>
                <td>
                    <textarea name="wpso_cache_exclusions" rows="5" cols="50" class="large-text"><?php echo esc_textarea(get_option('wpso_cache_exclusions')); ?></textarea>
                    <p class="description"><?php _e('Enter URL patterns to exclude from caching (one per line, e.g., /shop/*)', 'wp-security-optimizer'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Clear Cache', 'wp-security-optimizer'); ?></th>
                <td>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wpso_clear_cache'), 'wpso_clear_cache'); ?>" class="button"><?php _e('Clear All Cache', 'wp-security-optimizer'); ?></a>
                </td>
            </tr>
        </table>

        <h2 class="title"><?php _e('OTP Login Settings', 'wp-security-optimizer'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable OTP Login', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_otp_login_enabled" value="1" <?php checked(1, get_option('wpso_otp_login_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable Email OTP', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_otp_email_enabled" value="1" <?php checked(1, get_option('wpso_otp_email_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable Phone OTP', 'wp-security-optimizer'); ?></th>
                <td><input type="checkbox" name="wpso_otp_phone_enabled" value="1" <?php checked(1, get_option('wpso_otp_phone_enabled')); ?>></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('OTP Vendor', 'wp-security-optimizer'); ?></th>
                <td>
                    <select name="wpso_otp_vendor">
                        <?php
                        $vendors = (new WPSOOTPVendors())->get_available_vendors();
                        foreach ($vendors as $vendor) {
                            printf('<option value="%s" %s>%s</option>', $vendor, selected($vendor, get_option('wpso_otp_vendor'), false), ucfirst($vendor));
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <!-- Twilio Settings -->
            <tr>
                <th scope="row"><?php _e('Twilio SID', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_twilio_sid" value="<?php echo esc_attr(get_option('wpso_otp_twilio_sid')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Twilio Auth Token', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_twilio_token" value="<?php echo esc_attr(get_option('wpso_otp_twilio_token')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Twilio From Number', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_twilio_from" value="<?php echo esc_attr(get_option('wpso_otp_twilio_from')); ?>" class="regular-text"></td>
            </tr>
            <!-- Nexmo Settings -->
            <tr>
                <th scope="row"><?php _e('Nexmo API Key', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_nexmo_key" value="<?php echo esc_attr(get_option('wpso_otp_nexmo_key')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Nexmo API Secret', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_nexmo_secret" value="<?php echo esc_attr(get_option('wpso_otp_nexmo_secret')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Nexmo From Number', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_nexmo_from" value="<?php echo esc_attr(get_option('wpso_otp_nexmo_from')); ?>" class="regular-text"></td>
            </tr>
            <!-- MessageBird Settings -->
            <tr>
                <th scope="row"><?php _e('MessageBird API Key', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_messagebird_key" value="<?php echo esc_attr(get_option('wpso_otp_messagebird_key')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('MessageBird From Number', 'wp-security-optimizer'); ?></th>
                <td><input type="text" name="wpso_otp_messagebird_from" value="<?php echo esc_attr(get_option('wpso_otp_messagebird_from')); ?>" class="regular-text"></td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>