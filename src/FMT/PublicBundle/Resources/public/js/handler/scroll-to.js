(function ($) {
    var DEFAULT_OFFSET = 100,
        DEFAULT_SCROLL_TIME = 1000;

    var ScrollTo = function (config) {
        if (config.container.length > 0) {
            var $item = config.container.first();

            var offset = config.offset || DEFAULT_OFFSET;
            offset = $item.data('offset') || offset;

            var scrollTime = config.scrollTime || DEFAULT_SCROLL_TIME;
            scrollTime = $item.data('scroll-time') || scrollTime;

            var top = $item.offset().top - offset;
            $('html, body').animate({scrollTop: top >= 0 ? top : 0}, scrollTime);
        }
    };


    $.fn.ScrollTo = function(args) {
        var instance = $(this).data("scroll-to-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new ScrollTo(options);
            $(this).data("scroll-to-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance);
        }

        return this;
    };

    $(document).ready(function () {
        $('[data-toggle="flash"]').ScrollTo();
    });
}) (jQuery);
