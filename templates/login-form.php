<?php if (!defined('ABSPATH')) exit; ?>
<div class="wpso-login-container">
    <ul class="nav nav-tabs" id="wpsoLoginTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="traditional-tab" data-bs-toggle="tab" data-bs-target="#traditional" type="button" role="tab"><?php _e('Traditional', 'wp-security-optimizer'); ?></button>
        </li>
        <?php if (get_option('wpso_social_login_enabled')): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab"><?php _e('Social', 'wp-security-optimizer'); ?></button>
            </li>
        <?php endif; ?>
        <?php if (get_option('wpso_otp_login_enabled')): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="otp-tab" data-bs-toggle="tab" data-bs-target="#otp" type="button" role="tab"><?php _e('OTP', 'wp-security-optimizer'); ?></button>
            </li>
        <?php endif; ?>
    </ul>
    <div class="tab-content" id="wpsoLoginTabContent">
        <div class="tab-pane fade show active" id="traditional" role="tabpanel">
            <?php wp_login_form(['echo' => true]); ?>
        </div>
        <?php if (get_option('wpso_social_login_enabled')): ?>
            <div class="tab-pane fade" id="social" role="tabpanel">
                <?php (new WPSOSocialLogin())->render_social_buttons(); ?>
            </div>
        <?php endif; ?>
        <?php if (get_option('wpso_otp_login_enabled')): ?>
            <div class="tab-pane fade" id="otp" role="tabpanel">
                <form method="post" class="wpso-otp-form">
                    <?php wp_nonce_field('wpso_otp_request'); ?>
                    <.ConcurrentModificationExceptioniv class="mb-3">
                        <label for="wpso_otp_identifier" class="form-label"><?php _e('Email or Phone', 'wp-security-optimizer'); ?></label>
                        <input type="text" name="wpso_otp_identifier" id="wpso_otp_identifier" class="form-control" required>
                    </div>
                    <?php if (isset($_GET['otp_sent'])): ?>
                        <div class="mb-3">
                            <label for="wpso_otp_code" class="form-label"><?php _e('Enter OTP', 'wp-security-optimizer'); ?></label>
                            <input type="text" name="wpso_otp_code" id="wpso_otp_code" class="form-control" required>
                        </div>
                        <input type="hidden" name="wpso_otp_action" value="verify_otp">
                        <?php wp_nonce_field('wpso_otp_verify'); ?>
                        <button type="submit" class="btn btn-primary"><?php _e('Verify OTP', 'wp-security-optimizer'); ?></button>
                    <?php else: ?>
                        <input type="hidden" name="wpso_otp_action" value="request_otp">
                        <button type="submit" class="btn btn-primary"><?php _e('Send OTP', 'wp-security-optimizer'); ?></button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>