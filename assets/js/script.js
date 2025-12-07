jQuery(document).ready(function($) {
    
    // Handle accept button click
    $(document).on('click', '.ccp-accept', function(e) {
        e.preventDefault();
        setConsent('accept');
    });
    
    // Handle decline button click
    $(document).on('click', '.ccp-decline', function(e) {
        e.preventDefault();
        setConsent('decline');
    });
    
    function setConsent(consent) {
        $.ajax({
            url: ccp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ccp_set_consent',
                consent: consent,
                nonce: ccp_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Hide the popup with animation
                    $('#ccp-popup').fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Reload the page to apply cookie-based changes if needed
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 500);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    }
    
    // Optional: Auto-hide after 5 seconds (uncomment if needed)
    // setTimeout(function() {
    //     if ($('#ccp-popup').length) {
    //         $('#ccp-popup').fadeIn(300);
    //     }
    // }, 5000);
    
});