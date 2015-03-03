jQuery(document).ready(function() {
    jQuery(function() {
        jQuery("#sat_error-modal").dialog({
            modal: true,
            width: 450,
            buttons: {
                Ok: function() {
                    jQuery(this).dialog("close");
                }
            }
        });
    });
});