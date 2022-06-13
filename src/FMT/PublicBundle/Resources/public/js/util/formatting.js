(function ($) {
    $.fmt = $.fmt || {};
    $.fmt.formatting = {
        number: function (value, decimal, separator) {
            var dec = decimal || 2;
            var sep = separator || "";

            var mult = Math.pow(10, dec);
            var result = "" +Math.ceil(value * mult);

            while (result.length < dec) {
                result = "0" + result;
            }
            var tail = result.substr(-dec);
            result = result.substr(0, result.length - dec)

            if (result == "") {
                result = "0";
            }

            if (sep != "" && result.length > 3) {
                var formatted = [];
                while (result.length > 0) {
                    formatted.splice(0, 0, result.substr(-3));
                    result = result.substr(0, result.length - 3);
                }
                result = formatted.join(sep);
            }

            return result + "." + tail;
        }
    };
}) (jQuery);