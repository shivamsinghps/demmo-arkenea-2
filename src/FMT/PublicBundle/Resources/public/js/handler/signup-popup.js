(function ($) {
    $(document).ready(function () {
        $(".show-signup-popup").on('click', function (e) {
            e.stopPropagation();
            $burgerMenu = $('[data-target="#navbar-collapse"]');

            $("[data-btn=show-signup]").parent().removeClass('open');

            if ($burgerMenu.is(':visible')) {
                $burgerMenu.click();
            }

            var tab = $(this).data("show-signup");
            $(".register-box-body").find("a[data-tab=" + tab + "]").click();
            $("[data-btn=show-signup]").click();
        });
    });
})(jQuery);
