(function ($) {
    var BookSearch = function (config) {
        this.popup = null;
        this.$button = $(config.container).first();
        this.error_message = this.$button.data('error');
        this.not_found_message = this.$button.data('not-found');
        this.error_wrapper = this.$button.data('error-wrapper');

        this.__init();
    };

    BookSearch.prototype.__init = function () {
        var _self = this;

        _self.$button.on('click', function() {
            _self.getCatalog($(this).data('url'));
        });
    };

    BookSearch.prototype.getCatalog = function (url, $section) {
        var _self = this;

        $.fmt.ajax.send({url: url}).then(function (response) {
            if (_self.popup && $section) {
                // prepare new breadcrumbs
                var $cloneLi = _self.popup.$popup.find('.breadcrumb li').clone();

                if ($section.closest('.breadcrumb').length > 0) {
                    $cloneLi.each(function (index, value) {
                        if ($(value).find('a').data('url') == $section.data('url')) {
                            var position = index + 1;
                            $cloneLi.splice(position, $cloneLi.length - position);
                            return false;
                        }
                    });
                } else {
                    var $newLi = $cloneLi.first().clone();
                    $newLi.empty().append($section);
                }
            }

            _self.popup = $.fmt.popup.showPopup(response.data);

            if ($section) {
                var $breadcrumbs = _self.popup.$popup.find('.breadcrumb');
                $breadcrumbs.empty().append($cloneLi);

                if ($newLi) {
                    $breadcrumbs.append($newLi);
                }
            }

            _self.addPopupListener();
        }).catch(function (error) {
            _self.showError($.fmt.flash.FLASH_TYPE_ERROR, error, _self.error_message);
        });
    };

    BookSearch.prototype.addPopupListener = function () {
        var _self = this;

        _self.popup.$popup.find('.modal-body #catalog').off('click', 'a').on('click', 'a', function(e) {
            e.preventDefault();

            if ($(this).data('url')) {
                // get next section
                _self.getCatalog($(this).data('url'), $(this));
            } else {
                _self.$button.trigger('addBook', [$(this), true]);
            }
        });

        _self.popup.$popup.find('.modal-body #search #isbn-label').tooltip();

        var $search_input = _self.popup.$popup.find('.modal-body #search #isbn');
        var url = $search_input.data('url');
        $search_input.autoComplete({
            minChars: 10,
            delay: 200,
            source: function (term, response) {
                if (term.length === 10 || term.length === 13) {
                    $search_input.prop('disabled', true);
                    $.fmt.ajax.send({url: url, data: {isbn: term}}).then(function (request) {
                        response(modifyResponseDataIfEmpty('data' in request ? request.data : []));
                    }).catch(function (error) {
                        _self.showError($.fmt.flash.FLASH_TYPE_WARNING, error, _self.not_found_message);
                    }).finally(function () {
                        $search_input.prop('disabled', false).focus();
                    });
                }
            },
            renderItem: function (item){
                var template = $('[data-autocomplete="template"]').clone();
                template.html(item.label);

                $.each(item, function (key, val) {
                    template.attr("data-" + key, val);
                });

                return template.prop('outerHTML');
            },
            onSelect: function (e, term, item) {
                if (item.data('not-a-result') === true) {
                    return;
                }
                _self.$button.trigger('addBook', [item, false]);
            }
        });

        var modifyResponseDataIfEmpty = function (data) {
            if (!data || data.length == 0) {
                data = [{
                    label: 'Oh.... It looks like nothing found...',
                    'not-a-result': true
                }];
            }
            return data;
        }

        $search_input.bind('paste', function() {
            $search_input.trigger('keyup.autocomplete');
        });
    };

    BookSearch.prototype.showError = function (type, error, message) {
        var _self = this;

        var options = {
            "type": type,
            "message": message
        };

        if (_self.popup) {
            options.targetElement = _self.popup.$popup.find('.modal-body');
        } else {
            options.targetElement = $(_self.error_wrapper);
            options.scrollToAlert = true;
        }

        $.fmt.flash.addFlash(options);

        console.log(error);
    };


    $.fn.BookSearch = function(args) {
        var instance = $(this).data("book-search-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new BookSearch(options);
            $(this).data("book-search-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance, Array.from(arguments).splice(1));
        }

        return this;
    };

    $(document).ready(function () {
        $('[data-toggle="book-search"]').BookSearch();
    });
}) (jQuery);
