(function ($) {

    var DATA_ITEM_SUFFIX = "-instance";

    var ucFirst = function(str) {
        var result = "" + str;
        if (result.length > 1) {
            result = result.substr(0, 1).toUpperCase() + result.substr(1);
        } else {
            result = result.toUpperCase();
        }
        return result;
    };

    var toCamelCase = function (str) {
        var parts = $.map(("" + str).split("-"), function (item) {
            return ucFirst(item.toLowerCase());
        });
        return parts.join("");
    };

    $.fmt = $.fmt || {};

    $.fmt.CustomComponent = function (name, component) {
        var descriptor = $.trim("" + name).toLowerCase();
        var dataItem = descriptor + DATA_ITEM_SUFFIX;
        var method = toCamelCase(descriptor);
        var extension = {};
        extension[method] = function (args) {
            var result = null;
            var container = $(this);
            var instance = container.data(dataItem);
            var argsType = typeof args;

            if (!instance) {
                var options = container.data();
                if (argsType == "object") {
                    options = $.extend(options, args);
                }
                options["elements"] = {};
                $("[data-" + descriptor + "]", container).each(function () {
                    var element = $(this);
                    var names = $.trim("" + element.data(descriptor)).split(" ");
                    $.map(names, function (name) {
                        options["elements"][name] = element;
                    });
                });
                instance = new component($.extend(options, {container: container}));
                container.data(dataItem, instance);
            }

            if (argsType == "string" && args.substr(0, 1) != "_" && typeof instance[args] == "function") {
                result = instance[args].call(instance, Array.prototype.slice.call(arguments).slice(1));
            } else {
                result = container;
            }

            return result;
        };

        $.fn.extend(extension);

        $('[data-toggle="' + descriptor + '"]').each(function () {
           var element = $(this);
            element[method].apply(element);
        });
    };

}) (jQuery);