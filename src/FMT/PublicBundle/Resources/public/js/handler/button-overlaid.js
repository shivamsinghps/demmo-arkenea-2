(function ($) {
    $(document).ready(function () {
        var buttonOverlaid = $('[data-overlaid="button_overlaid"]');
        setTimeout(function () {
            buttonOverlaid.toggle();
        }, 3000);
    });
}) (jQuery);
