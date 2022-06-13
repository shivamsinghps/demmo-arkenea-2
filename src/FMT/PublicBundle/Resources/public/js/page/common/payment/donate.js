(function ($) {
    $(document).ready(function() {
        var donateForm = $('[data-toggle="donate-form"]');
        var checkoutForm = $('[data-toggle="stripe-checkout"]');

        donateForm.on("donate-amount", function(event) {
            checkoutForm.StripeCheckout("setAmount", event.amount);
        });
    });
}) (jQuery);
