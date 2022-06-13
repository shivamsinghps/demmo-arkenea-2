(function ($) {

    var EVENT_NAME = "donate-amount";

    var DonateForm = function (options) {

        var input = $('[name="amount"]', options.container);
        var maxLength = input.data('max-length')
        var txnFee = options.elements["txn-fee"];
        var fmtFee = options.elements["fmt-fee"];
        var total = options.elements["total"];
        var timer = null;
        var validateAmountUrl = $(options.container).data('validate-amount-url');
        var __delayActive = true;
        var requestedValidations = {};

        var triggerEvent = function (amount, commission) {
            var event = $.Event(EVENT_NAME);
            if (amount && commission) {
                event.amount = amount;
                event.commission = commission;
            }
            $(options.container).trigger(event);
        }.bind(this);

        var recalculate = function (validateResponse, amount) {
            amount = +amount;

            var fmtFeeUsd = validateResponse.fmtFee / 100;
            txnFee.text($.fmt.formatting.number(validateResponse.paymentSystemFee / 100, 2, " "));
            fmtFee.text($.fmt.formatting.number(fmtFeeUsd, 2, " "));
            total.text($.fmt.formatting.number((validateResponse.fmtFee + validateResponse.paymentSystemFee) / 100 + amount, 2, " "));

            triggerEvent(amount, fmtFeeUsd);
        }.bind(this);

        input.on("validation", function (event, valid) {
            var setAmountToZero = function () {
                txnFee.text($.fmt.formatting.number(0, 2, " "));
                fmtFee.text($.fmt.formatting.number(0, 2, " "));
                total.text($.fmt.formatting.number(0, 2, " "));

                triggerEvent();
            }
            if (valid) {
                delay(input.val(), recalculate, function () {
                    setAmountToZero();
                });
            } else {
                setAmountToZero();
            }
        });

        input.on("blur", function () {
            input.validate();
        });

        input.on("keyup", function (event) {
            if (timer) {
                clearTimeout(timer);
            }

            timer = setTimeout(function () {
                input.validate();
            }, 300);
        });

        var $amountErrorBlock = $('#amount-error');

        input.on('input', function () {
            this.value = this.value.match(/^\d+\.?\d{0,2}/);
            this.value = this.value.substr(0, maxLength);
            $amountErrorBlock.hide();
        });

        var debounce = function (func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        var delay = debounce(function (amount, successCallback, errorCallback) {
            validateAmount(amount, successCallback, errorCallback);
            this.__delayActive = false;
        }, 700);

        var validateAmount = function (amount, successCallback, errorCallback) {
            input.attr('readonly', true);

            var onResponseReturn = function (response, isEmulatedRequest) {
                if (isEmulatedRequest !== true) {
                    requestedValidations[amount] = {
                        timestamp: Date.now(),
                        response: response
                    }
                }
                if (response.success) {
                    successCallback(response, amount);
                    toggleCustomError(false);
                } else {
                    errorCallback();
                    toggleCustomError(true, response.reason || 'Invalid value');
                }
            };

            if (amount in requestedValidations) {
                if ((Date.now() - requestedValidations[amount].timestamp) < 5000) {
                    onResponseReturn(requestedValidations[amount].response, true);
                    input.attr('readonly', false);
                    return;
                }
            }

            $.fmt.ajax.send({
                url: validateAmountUrl,
                method: "POST",
                data: {amount: amount}
            })
                .then(onResponseReturn)
                .catch(function (error) {
                    toggleCustomError(true, 'Network error');
                    console.log(error);
                })
                .finally(function () {
                    input.attr('readonly', false);
                });

        }.bind(this);

        var toggleCustomError = function (isShow, errorText) {
            var ERROR_INPUT_ID = 'donate_amount_error';
            var $errorSpan = $('#' + ERROR_INPUT_ID);
            var $commonContainer = input.closest('.input-group').parent();

            if (isShow) {
                if ($errorSpan.length === 0) {
                    $commonContainer.append('<span id="' + ERROR_INPUT_ID + '" class="help-block form-error">' + errorText + '</span>');
                } else {
                    $errorSpan.text(errorText);
                }
                $commonContainer.addClass('has-error');
                $commonContainer.removeClass('has-success');
                input.addClass('error');
                input.removeClass('valid');
                $errorSpan.show();
            } else {
                $commonContainer.addClass('has-success');
                $commonContainer.removeClass('has-error');
                input.removeClass('error');
                input.addClass('valid');
                $errorSpan.detach();
            }
        }

    };

    $.fmt.CustomComponent("donate-form", DonateForm);
}) (jQuery);
