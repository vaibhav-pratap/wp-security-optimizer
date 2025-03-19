jQuery(document).ready(function($) {
    $('.wpso-social-button').on('click', function() {
        $(this).addClass('loading');
    });

    $('.wpso-login-tabs a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        
        $('.wpso-login-tabs a').removeClass('active');
        $(this).addClass('active');
        
        $('.wpso-login-pane').removeClass('active');
        $(target).addClass('active');
    });
});