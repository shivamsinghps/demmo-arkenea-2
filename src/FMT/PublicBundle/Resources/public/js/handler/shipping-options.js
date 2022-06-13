(function ($) {
    var ShippingOptions = function (config) {
        this.popup = null;
        this.$button = $(config.container).first();
        this.error_message = this.$button.data('error');
        this.error_wrapper = this.$button.data('error-wrapper');

        this.__init();
    };

    ShippingOptions.prototype.__init = function () {
        var _self = this;

        _self.$button.on('click', function() {
            _self.showPopup($(this).data('url'));
        });
    };

    ShippingOptions.prototype.showPopup = function (url) {
        var _self = this;

        $.fmt.ajax.send({url: url}).then(function (response) {
            var options = $.extend(response.data, {
                buttons: {
                    "Select": {
                        'className': 'btn btn-primary',
                        'callback': function (modal) {
                            var id = _self.popup.$popup.find('[type="radio"]:checked').data('id');

                            _self.$button
                                .data('id', id)
                                .trigger('chooseShippingOptions', id);

                            modal.hide();
                        }
                    }
                }
            });

            _self.popup = $.fmt.popup.showPopup(options);

            var old_id = _self.$button.data('id');
            _self.popup.$popup.find('[type="radio"][data-id="' + old_id + '"]').prop('checked', true);
        }).catch(function (error) {
            _self.showError($.fmt.flash.FLASH_TYPE_ERROR, error, _self.error_message);
        });
    };

    ShippingOptions.prototype.showError = function (type, error, message) {
        var _self = this;

        $.fmt.flash.addFlash({
            "type": type,
            "message": message,
            "targetElement": $(_self.error_wrapper),
            "scrollToAlert": true
        });

        console.log(error);
    };


    $.fn.ShippingOptions = function(args) {
        var instance = $(this).data("shipping-options-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new ShippingOptions(options);
            $(this).data("shipping-options-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance);
        }

        return this;
    };

    $(document).ready(function () {
        $('[data-toggle="shipping-options"]').ShippingOptions();
    });
}) (jQuery);
