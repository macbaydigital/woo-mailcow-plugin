jQuery(document).ready(function($) {
    $('#billing_username').on('input', function() {
        var username = $(this).val();
        if (username.length < 3) return;

        $.ajax({
            url: woo_mailcow_vars.ajax_url,
            method: 'POST',
            data: {
                action: 'check_username',
                nonce: woo_mailcow_vars.nonce,
                username: username
            },
            success: function(response) {
                if (response.success) {
                    $('#billing_username_field').removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                    $('#username-feedback').remove();
                } else {
                    $('#billing_username_field').removeClass('woocommerce-validated').addClass('woocommerce-invalid');
                    $('#username-feedback').remove();
                    $('#billing_username_field').append('<span id="username-feedback" class="error">' + response.data + '</span>');
                }
            }
        });
    });
});
