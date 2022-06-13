(function ($) {

    var CANCEL_EVENT_NAME = "stripe-checkout-cancel";

    var StripeCheckout = function (options) {
        if (typeof Stripe != "function") {
            throw "Unable to find Stripe JavaScript implementation";
        }

        this.container = options.container;

        var stripe = Stripe(options.token);
        var elements = stripe.elements();
        var card = elements.create("card", {
            classes: {
                base: options.classes || "form-control"
            }
        });

        // Private attributes
        var formName = this.container.attr("name");
        var emailAddress = $('[name="' + formName + '[email]"]', this.container);
        var anonymousSelector = $('[name="' + formName + '[anonymous]"]', this.container);
        var firstName = $('[name="' + formName + '[first_name]"]', this.container);
        var lastName = $('[name="' + formName + '[last_name]"]', this.container);
        var paymentAmount = $('[name="' + formName + '[payment_amount]"]', this.container);
        var paymentProcessor = $('[name="' + formName + '[payment_processor]"]', this.container);
        var cancelButton = $('[name="' + formName + '[cancel]"]', this.container);
        var proceedButton = $('[name="' + formName + '[proceed]"]', this.container);
        var creditcardPlaceholder = options.elements["credit-card-field"];
        var defaultAction = false;
        var donorNameBox = $('#donor-name-box');
        var isAuthorized = donorNameBox.data('is-authorized');

        // Private functions
        var onCancelPressed = function (event) {
            var location = cancelButton.data("location");
            if (location) {
                document.location.href = location;
            } else {
                var cancelEvent = $.Event(CANCEL_EVENT_NAME);
                cancelEvent.original = event;
                cancelButton.trigger(cancelEvent);
            }
        };

        var onAnonymousChanged = function (event) {
            var isDisabled = (1 * $(event.target).val()) === 1;

            if (isDisabled) {
                firstName.data('first-name', firstName.val()).val('').prop('readonly', true);
                lastName.data('last-name', lastName.val()).val('').prop('readonly', true);
            } else {
                firstName.val(firstName.data("first-name") || '').prop('readonly', isAuthorized).focus();
                lastName.val(lastName.data("last-name") || '').prop('readonly', isAuthorized);
            }
        }.bind(this);

        var onFormSubmit = function (event) {
            if (defaultAction) {
                return;
            }

            event.preventDefault();

            cancelButton.prop("disabled", true);
            proceedButton.prop("disabled", true);

            stripe.createToken(card).then(function (result) {
                if (result.error) {

                    // TODO: Add error message
                    console.log(result.error);

                    cancelButton.prop("disabled", false);
                    proceedButton.prop("disabled", false);
                } else {
                    paymentProcessor.val(JSON.stringify(result));
                    defaultAction = true;
                    $(event.target).submit();
                }
            });
        }.bind(this);

        // Public functions
        this.setAmount = function(amount) {
            if (options.checkout) {
                return;
            }

            var isDisabled = amount <= 0;

            paymentAmount.val(isDisabled ? "" : amount);
            proceedButton.prop("disabled", isDisabled);
        };

        // Component initialization
        anonymousSelector.on("change", onAnonymousChanged);
        cancelButton.on("click", onCancelPressed);
        this.container.on("submit", onFormSubmit);

        if (options.checkout) {
            proceedButton.prop("disabled", false);
        }

        card.mount(creditcardPlaceholder[0]);
    };

    $.fmt.CustomComponent("stripe-checkout", StripeCheckout);
}) (jQuery);
