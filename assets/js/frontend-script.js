jQuery(document).ready(function($) {
    // Ensure tabs work correctly on the login page
    $('#wpsoLoginTabs .nav-link').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Auto-focus the first input in the active tab
    $('.tab-pane.active .form-control:first').focus();
});