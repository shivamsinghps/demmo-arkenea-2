(function ($) {
    $(document).ready(function () {
        var contactBlock = $(".person-info-block");

        var heights = contactBlock.map(function () {
            return $(this).height();
        }).get();

        var maxHeight = Math.max.apply(null, heights);

        contactBlock.height(maxHeight);
    });

    $('.invite-person [data-toggle="tooltip"]').tooltip({
        placement: function () {
            return $(window).width() > 800 ? 'right' : 'left';
        }
    })
})(jQuery);