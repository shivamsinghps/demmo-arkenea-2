(function ($) {
    var ProfileDisable = function (config) {
        this.popup = null;
        this.$button = $(config.container).first();
        this.url = this.$button.data('url');
        this.title = this.$button.data('title');
        this.message = this.$button.data('message') || '';
        this.button_name = this.$button.data('button-name');
        this.error_message = this.$button.data('error');
        this.error_wrapper = this.$button.data('error-wrapper');

        this.__init();
    }

    ProfileDisable.prototype.__init = function () {
        var _self = this;

        _self.$button.on('click', function () {
            _self.showPopup($(this).data('url'));
        });
    };

    ProfileDisable.prototype.showPopup = function () {
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
                                $.fmt.popup.hidePopup();
                                $('body').css('pointerEvents', 'none');
                                _self.showSuccess(response.data.message);

                                window.location.href = response.data.redirect;
                            } else {
                                var errorMessage = response.data.message ? response.data.message : _self.error_message;
                                $.fmt.popup.hidePopup();
                                _self.showError(response, errorMessage);
                            }
                        }).catch(function (error) {
                            $.fmt.popup.hidePopup();
                            _self.showError(error, _self.error_message);
                        });
                    }
                }
            }
        };

        _self.popup = $.fmt.popup.showPopup(options);
    };

    ProfileDisable.prototype.showError = function (error, message) {
        var _self = this;

        $.fmt.flash.addFlash({
            type: $.fmt.flash.FLASH_TYPE_ERROR,
            message: message,
            targetElement: $(_self.error_wrapper),
            scrollToAlert: true
        });

        console.error(error);
    };

    ProfileDisable.prototype.showSuccess = function (message) {
        var _self = this;

        $.fmt.flash.addFlash({
            type: $.fmt.flash.FLASH_TYPE_SUCCESS,
            message: message,
            targetElement: $(_self.error_wrapper),
            scrollToAlert: true
        })
    };

    $.fn.ProfileDisable = function (args) {
        var instance = $(this).data('profile-disable-instance');

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args === 'object') {
                options = $.extend(args, options);
            }
            instance = new ProfileDisable(options);
            $(this).data('profile-disable-instance', instance);
        }

        if (typeof args === "string" && typeof instance[args] === "function") {
            return instance[args].call(instance);
        }

        return this;
    }

    $(document).ready(function () {
        $('#disable-account').ProfileDisable();
    })
})(jQuery);
