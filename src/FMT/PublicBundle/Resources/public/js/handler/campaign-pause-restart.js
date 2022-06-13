(function ($) {
    var CampaignPauseRestart = function (config) {
        this.popup = null;
        this.$button = $(config.container).first();
        this.url = this.$button.data('url');
        this.title = this.$button.data('title');
        this.message = this.$button.data('message');
        this.button_name = this.$button.data('button-name');
        this.error_message = this.$button.data('error');
        this.error_wrapper = this.$button.data('error-wrapper');

        this.__init();
    };

    CampaignPauseRestart.prototype.__init = function () {
        var _self = this;

        _self.$button.on('click', function () {
            _self.showPopup($(this).data('url'));
        });
    };

    CampaignPauseRestart.prototype.showPopup = function () {
        var _self = this;

        var options = {
            title: _self.title,
            message: _self.message,
            buttons: {
                [_self.button_name]: {
                    'className': 'btn btn-primary',
                    'callback': function (modal) {
                        $.fmt.ajax.send({url: _self.url, method: "POST"}).then(function (response) {
                            if (response.success) {
                                window.location.href = window.location;
                            } else {
                                $.fmt.popup.hidePopup();
                                _self.showError($.fmt.flash.FLASH_TYPE_ERROR, response.messages, _self.error_message);
                            }
                        }).catch(function (error) {
                            $.fmt.popup.hidePopup();
                            _self.showError($.fmt.flash.FLASH_TYPE_ERROR, error, _self.error_message);
                        });
                    }
                }
            }
        };

        _self.popup = $.fmt.popup.showPopup(options);
    };

    CampaignPauseRestart.prototype.showError = function (type, error, message) {
        var _self = this;

        $.fmt.flash.addFlash({
            "type": type,
            "message": message,
            "targetElement": $(_self.error_wrapper),
            "scrollToAlert": true
        });

        console.log(error);
    };

    $.fn.CampaignPauseRestart = function (args) {
        var instance = $(this).data("campaign-pause-restart-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new CampaignPauseRestart(options);
            $(this).data("campaign-pause-restart-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance);
        }

        return this;
    };

    $(document).ready(function () {
        $('[data-toggle="campaign-pause-restart"]').CampaignPauseRestart();
    });
})(jQuery);
