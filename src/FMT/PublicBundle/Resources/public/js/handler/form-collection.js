(function ($) {
    var FormCollection = function (config) {
        var _self = this;

        var $list = config.container,
            addSelector = $list.data('add-button'),
            removeSelector = $list.data('remove-button');

        _self.$list = $list;
        _self.rowSelector = $list.data('item');
        _self.serialNumberSelector = $list.data('serial-number');
        _self.removeTitle = $list.data('remove-title');
        _self.removeMessage = $list.data('remove-message');

        _self.onAdd = function () {};
        _self.onRemove = function () {};

        $list.find(_self.rowSelector).each(function() {
            var $item = $(this),
                $remove = $item.find(removeSelector);

            _self.addListenerOnRemove($item, $remove);
        });

        $(addSelector).click(function (e) {
            e.preventDefault();

            var counter = _self.getCounter();
            var newItem = $list.attr('data-prototype').replace(/__name__/g, counter);
            counter++;
            $list.data('widget-counter', counter);

            var $newItem = $(newItem),
                $remove = $newItem.find(removeSelector);

            $newItem.appendTo($list);

            _self.addListenerOnRemove($newItem, $remove);
            _self.updateSerialNumbers();
            _self.onAdd();
        });
    };

    FormCollection.prototype.addListenerOnRemove = function ($item, $remove) {
        var _self = this;

        $remove.on('click', function(e) {
            e.preventDefault();

            $.fmt.popup.showPopup({
                title: _self.removeTitle,
                message: _self.removeMessage,
                buttons: {
                    "REMOVE": {
                        'className': 'btn btn-primary',
                        'callback': function (modal) {
                            $item.remove();
                            _self.updateSerialNumbers();
                            _self.onRemove();
                            modal.hide();
                        }
                    }
                }
            });
        });
    };

    FormCollection.prototype.addRow = function () {
        var addSelector = this.$list.data('add-button');
        $(addSelector).click();
    };

    FormCollection.prototype.updateOnAdd = function (args) {
        if (typeof args[0] == "function") {
            this.onAdd = args[0];
        }
    };

    FormCollection.prototype.updateOnRemove = function (args) {
        if (typeof args[0] == "function") {
            this.onRemove = args[0];
        }
    };

    FormCollection.prototype.getLastRow = function () {
        var counter = this.$list.FormCollection('getCounter') - 1;
        return this.$list.find('[data-row="' + counter + '"]');
    };

    FormCollection.prototype.getCounter = function () {
        return this.$list.data('widget-counter') || this.$list.children().length + 1;
    };

    FormCollection.prototype.updateSerialNumbers = function () {
        var _self = this;

        this.$list.find(this.rowSelector).each(function(index) {
            $(this).find(_self.serialNumberSelector).text(index + 1);
        });
    };

    $.fn.FormCollection = function(args) {
        var instance = $(this).data("form-collection-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new FormCollection(options);
            $(this).data("form-collection-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance, Array.from(arguments).splice(1));
        }

        return this;
    };

    $(document).ready(function () {
        $('[data-toggle="form-collection"]').FormCollection();
    });
}) (jQuery);
