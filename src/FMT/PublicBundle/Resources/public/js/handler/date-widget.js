(function ($) {
    var DateWidget = function (config) {
        this.container = config.container;
        this.minDate = config.minDate.utc(true).startOf('date') || false;

        this.__init();
    };

    DateWidget.prototype.__init = function () {
        var _self = this,
            options = {useCurrent: false};

        moment.updateLocale('en', {
            week: { dow: 1 }
        });

        this.container.each(function(index, input) {
            var format = $(input).data('date-format');
            if (format) {
                options.format = format;
            }

            if (_self.minDate) {
                options.enableOnReadonly = false;
            }

            $(input).datepicker(options);

            if (_self.minDate) {
                var dateMin = $(input).datepicker('getDate');

                dateMin = dateMin ? moment.min(_self.minDate, moment(dateMin).utc(true)) : _self.minDate;

                $(input).datepicker('setStartDate', dateMin.toDate());
            }
        });


        var startElements = $(this.container).filter('[data-start="1"]');
        var endElements = $(this.container).filter($('[data-end="1"]'));

        if (startElements.length && endElements.length) {
            var updateDateRangePicker = function($startElement, $endElement) {
                var dateStart = $startElement.datepicker('getDate'),
                    dateEnd = $endElement.datepicker('getDate'),
                    momentStart = moment(dateStart).utc(true),
                    momentEnd = moment(dateEnd).utc(true);

                var newStartMaxDate = false;
                var newEndMinDate = _self.minDate;

                if (dateStart && !dateEnd) {
                    newEndMinDate = _self.minDate ? moment.max(momentStart, _self.minDate) : momentStart;
                }
                else if (!dateStart && dateEnd) {
                    newStartMaxDate = momentEnd;
                }
                else if (dateStart && dateEnd) {
                    newStartMaxDate = momentEnd;
                    newEndMinDate = _self.minDate ? moment.max(momentStart, _self.minDate) : momentStart;
                }

                if (newStartMaxDate) {
                    newStartMaxDate = newStartMaxDate.toDate();
                }

                if (newEndMinDate) {
                    newEndMinDate = newEndMinDate.toDate();
                }

                $startElement.datepicker('setEndDate', newStartMaxDate);
                $endElement.datepicker('setStartDate', newEndMinDate);
            };

            startElements.each(function (index) {
                var $startElement = $(startElements[index]);
                var $endElement = $(endElements[index]);

                updateDateRangePicker($startElement, $endElement);

                var dateStart = $startElement.datepicker('getDate');
                if (dateStart && dateStart < _self.minDate) {
                    $startElement.attr('readonly', true);
                }

                $startElement.datepicker().on('changeDate', function() {
                    updateDateRangePicker($startElement, $endElement);
                });

                $endElement.datepicker().on('changeDate', function() {
                    updateDateRangePicker($startElement, $endElement);
                });
            });
        }
    };

    $.fn.DateWidget = function(args) {
        var instance = $(this).data("date-widget-instance");

        if (!instance) {
            var options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new DateWidget(options);
            $(this).data("date-widget-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance, arguments.splice(1));
        }

        return this;
    };
}) (jQuery);
