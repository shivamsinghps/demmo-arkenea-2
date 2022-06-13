(function ($) {
    var AjaxRequest = function (config) {
        this.preloaderSelector = config.hasOwnProperty('preloaderSelector')
            ? config.preloaderSelector
            : '.container .preloader';

        if (this.preloaderSelector) {
            this.$preloader = $(this.preloaderSelector);
        }
    };

    AjaxRequest.prototype.send = function (options) {
        var _self = this,
            url = options.url,
            data = options.hasOwnProperty('data') ? options.data : [],
            method = options.hasOwnProperty('method') ? options.method : 'GET',
            dataType = options.hasOwnProperty('dataType') ? options.dataType : 'json';

        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                dataType: dataType,
                data: data,
                type: method,
                cache: false,
                beforeSend: function () {
                    _self.showPreloader();
                },
                success: function (response) {
                    if (typeof response !== 'object') {
                        resolve(response);
                    }

                    if (response.success && response.hasOwnProperty('redirect') && response.redirect !== false) {
                        if (response.redirect == null) {
                            window.location.reload();
                        } else {
                            if (response.hasOwnProperty('data') && response.data.newWindow) {
                                window.open(response.redirect);
                            } else {
                                window.location.href = response.redirect;
                            }
                        }
                    }

                    if (response.success !== true) {
                        console.log(response);
                    }

                    resolve(response);
                },
                error: function (jqXHR) {
                    _self.hidePreloader();
                    reject(jqXHR);
                },
                complete: function () {
                    _self.hidePreloader();
                },
                done: function () {
                    _self.hidePreloader();
                }
            });
        });
    };

    AjaxRequest.prototype.showPreloader = function() {
        if (this.$preloader) {
            this.$preloader.css('visibility', 'visible');
        }
    };

    AjaxRequest.prototype.hidePreloader = function() {
        if (this.$preloader) {
            this.$preloader.css('visibility', 'hidden');
        }
    };


    $.fmt = $.fmt || {};
    $.fmt.ajax = {};

    $.fmt.ajax.send = function (options) {
        var instance = $(this).data("ajax-instance");

        if (!instance) {
            instance = new AjaxRequest(options);
            $(this).data("ajax-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance, arguments.splice(1));
        }

        return instance.send(options);
    };
}) (jQuery);
