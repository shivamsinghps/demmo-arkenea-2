(function ($) {

    $(document).ready(function () {

        $(document).on('click', '[data-social="share"]', function (event) {
            event.preventDefault();

            var href = $(this).attr('href');

            $.fmt.social.share({
                windowLink: href,
            });
        });
    });
    
})(jQuery);

