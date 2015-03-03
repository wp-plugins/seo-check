var $ = jQuery; //TODO FIX THIS HACK!
(function ($) {
    $(window).load(function () {
        console.log("processErReport2");
        if (typeof is_processed !== "undefined" && !is_processed) {
            processErReport();
        }
    });
})(jQuery);