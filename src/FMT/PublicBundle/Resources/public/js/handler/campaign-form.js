(function ($) {
    var CampaignForm = function (config) {
        this.popup = null;
        this.$form = $(config.container).first();
        this.$list = $('[data-toggle="form-collection"]');
        this.$browseButton = $('[data-toggle="book-search"]');
        this.$shippingButton = $('[data-toggle="shipping-options"]');

        this.url = this.$form.data('url-recalculate');
        this.error_message = this.$form.data('error');
        this.error_wrapper = this.$form.data('error-wrapper');

        this.statusAvailable = this.$form.data('status-available');
        this.statusOutOfStock = this.$form.data('status-out-of-stock');
        this.statusOutOfStockMessage = this.$form.data('status-out-of-stock-message');

        this.__init();
    };

    CampaignForm.prototype.__init = function () {
        var _self = this;

        _self.$list.find('[data-row]').each(function () {
            _self.addExchangeListener($(this));
        });

        _self.$list.FormCollection('updateOnRemove', function () {
            _self.changeForm();
        });

        _self.$shippingButton.on('chooseShippingOptions', function(e, id) {
            _self.$form.find('[data-field="shipping-option"]').val(id);

            _self.changeForm();
        });

        _self.$browseButton.on('click', function() {
            _self.selectRowForExchanging();
        });

        _self.$browseButton.on('addBook', function(e, item, withClassField) {
            _self.addBook(item, withClassField);
        });

        _self.$form.on('formChange', function() {
            _self.changeForm();
        });
    };

    CampaignForm.prototype.addBook = function (item, withClassField) {
        var _self = this;

        _self.popup = $.fmt.popup.hidePopup();

        var className = '';

        if (withClassField) {
            var $breadcrumb = _self.popup.$popup.find('.breadcrumb');
            var departmentName = $breadcrumb.children().eq(0).find('a').data('name');
            var courseName = $breadcrumb.children().eq(1).find('a').data('real-name');
            className = departmentName + ' ' + courseName;
        }

        var $row = null;

        _self.$list.find('[data-row]').each(function () {
            if ($(this).data('exchange') == 1) {
                $row = $(this);
                return true;
            }
        });

        var alreadyExists = false;

        _self.$list.find('[data-field="isbn"]').each(function () {
            if ($(this).attr('value') === item.data('isbn').toString()) {
                alreadyExists = true;

                return false;
            }
        })

        if (alreadyExists) {
            _self.showError($.fmt.flash.FLASH_TYPE_ERROR, null, 'You can not choose several identical books');

            return;
        }

        if (!$row) {
            _self.$list.FormCollection('addRow');
            $row = _self.$list.FormCollection('getLastRow');
        }

        var outOfStock = false;
        var status = _self.statusAvailable;
        if (item.data('calculatedInventory') == 0) {
            outOfStock = true;
            status = _self.statusOutOfStock;
        }

        $row.find('[data-field="family-id"]').val(item.data('family-id'));
        $row.find('[data-field="sku"]').val(item.data('sku'));
        $row.find('[data-field="title"]').val(item.data('name'));
        $row.find('[data-field="author"]').val(item.data('author'));
        $row.find('[data-field="class"]').val(className);
        $row.find('[data-field="isbn"]').val(item.data('isbn'));
        $row.find('[data-field="price"]').val(item.data('price'));
        $row.find('[data-field="state"]').val(item.data('state'));
        $row.find('[data-field="status"]').val(status);

        var title = item.data('name');

        if (item.data('state')) {
            var state = item.data('state');
            title += ' (' + state.substr(0,1).toUpperCase() + state.substr(1).toLowerCase() + ')';
        }

        if (outOfStock) {
            title += '<p class="text-danger">' + _self.statusOutOfStockMessage + '</p>';
        }

        $row.find('.title').html(title);
        $row.find('.author').text(item.data('author'));
        $row.find('.class').text(className);
        $row.find('.isbn').text(item.data('isbn'));
        $row.find('.price').text(item.data('converted-price'));

        _self.addExchangeListener($row);

        _self.$form.trigger('formChange');
    };

    CampaignForm.prototype.changeForm = function () {
        var _self = this;

        $.fmt.ajax.send({
            url: _self.url,
            data: _self.$form.serialize(),
            method: _self.$form.attr('method')
        }).then(function (response) {
            var updateFields = [
                'estimated-shipping',
                'estimated-cost',
                'funded-total',
                'purchased-total',
                'allowed-donate-amount',
            ];

            $.each(updateFields, function (i, fieldName) {
                _self.$form.find('[data-field="' + fieldName + '"]').text(response.data[fieldName + '-price']);
            });

            if (response.success !== true && response.data.errors) {
                var bookError = response.data.errors.books;

                if (bookError) {
                    _self.showError($.fmt.flash.FLASH_TYPE_ERROR, bookError, bookError);
                }
            }
            if (response.success !== true && response.hasOwnProperty('messages')) {
                _self.showError($.fmt.flash.FLASH_TYPE_ERROR, error, _self.error_message);
            }
        }).catch(function (error) {
            _self.showError($.fmt.flash.FLASH_TYPE_ERROR, error, _self.error_message);
        });
    };

    CampaignForm.prototype.addExchangeListener = function ($row) {
        var _self = this;

        $row.on('click', '.exchange-book', function(e) {
            e.preventDefault();

            _self.selectRowForExchanging($row);

            _self.$browseButton.trigger('click');
        });
    };

    CampaignForm.prototype.selectRowForExchanging = function ($row) {
        var _self = this;

        if ($row) {
            $row.data('exchange', 1);
        }
    };

    CampaignForm.prototype.showError = function (type, error, message) {
        var _self = this;

        $.fmt.flash.addFlash({
            "type": type,
            "message": message,
            "targetElement": $(_self.error_wrapper),
            "scrollToAlert": true
        });

        console.log(error);
    };


    $.fn.CampaignForm = function(args) {
        var instance = $(this).data("campaign-form-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new CampaignForm(options);
            $(this).data("campaign-form-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance);
        }

        return this;
    };

    $(document).ready(function () {
        $('[data-toggle="campaign-form"]').CampaignForm();
    });
}) (jQuery);
