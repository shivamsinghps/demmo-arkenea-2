(function ($) {

    $(document).ready(function (event) {
        $('[data-toggle="auto-flash"]').each(function () {
            var type = $(this).data("type"),
                options = {
                    "type": type,
                    "message": $(this).html(),
                };

            $.fmt.flash.addFlash(options);
        });
    });

})(jQuery);