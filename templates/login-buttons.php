<div class="wpso-social-login-buttons">
    <?php if (get_option('wpso_google_client_id')): ?>
        <a href="<?php echo esc_url(add_query_arg('wpso_social', 'google', wp_login_url())); ?>" class="wpso-social-button google">
            <i class="fab fa-google"></i> <?php _e('Sign in with Google', 'wp-security-optimizer'); ?>
        </a>
    <?php endif; ?>
    <?php if (get_option('wpso_linkedin_client_id')): ?>
        <a href="<?php echo esc_url(add_query_arg('wpso_social', 'linkedin', wp_login_url())); ?>" class="wpso-social-button linkedin">
            <i class="fab fa-linkedin-in"></i> <?php _e('Sign in with LinkedIn', 'wp-security-optimizer'); ?>
        </a>
    <?php endif; ?>
    <?php if (get_option('wpso_facebook_app_id')): ?>
        <a href="<?php echo esc_url(add_query_arg('wpso_social', 'facebook', wp_login_url())); ?>" class="wpso-social-button facebook">
            <i class="fab fa-facebook-f"></i> <?php _e('Sign in with Facebook', 'wp-security-optimizer'); ?>
        </a>
    <?php endif; ?>
    <?php if (get_option('wpso_github_client_id')): ?>
        <a href="<?php echo esc_url(add_query_arg('wpso_social', 'github', wp_login_url())); ?>" class="wpso-social-button github">
            <i class="fab fa-github"></i> <?php _e('Sign in with GitHub', 'wp-security-optimizer'); ?>
        </a>
    <?php endif; ?>
</div>