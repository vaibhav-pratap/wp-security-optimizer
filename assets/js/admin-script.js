jQuery(document).ready(function($) {
    // Dark Mode Toggle
    $('#toggle-dark-mode').on('click', function() {
        $('body').toggleClass('wpso-dark-mode');
        localStorage.setItem('wpsoDarkMode', $('body').hasClass('wpso-dark-mode') ? 'enabled' : 'disabled');
    });
    if (localStorage.getItem('wpsoDarkMode') === 'enabled') {
        $('body').addClass('wpso-dark-mode');
    }

    // CAPTCHA Vendor Switch
    $('#captcha_vendor').on('change', function() {
        $('.captcha-settings').hide();
        $('#' + $(this).val() + '-settings').show();
    });

    // Social Login Toggle
    $('.social-toggle').on('change', function() {
        const settingsId = '#' + $(this).attr('id').replace('_enabled', '-settings');
        $(settingsId).toggle(this.checked);
    });

    // OTP Vendor Switch
    $('#otp_vendor').on('change', function() {
        $('.otp-settings').hide();
        $('#' + $(this).val() + '-settings').show();
    });

    // SMTP Vendor Switch
    $('#smtp_vendor').on('change', function() {
        $('.smtp-settings').hide();
        $('#' + $(this).val() + '-settings').show();
    });
});