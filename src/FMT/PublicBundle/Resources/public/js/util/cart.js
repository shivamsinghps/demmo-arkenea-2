function Cart() {
    let self = this;

    self.selectors = {
        cartHeaderClass: 'global-cart-header',
        itemsNumberCounter: 'items-counter'
    };

    self.init();
}

Cart.prototype.init = function () {
    let self = this;

    self.$element = $('.' + self.selectors.cartHeaderClass);
    self.$itemsNumberCounter = self.$element.find('.' + self.selectors.itemsNumberCounter)
};

Cart.prototype.setItemsNumber = function (number) {
    let self = this;

    self.$itemsNumberCounter.html(number);

    if (number > 0) {
        self.$itemsNumberCounter.removeClass('hidden');
    } else {
        self.$itemsNumberCounter.addClass('hidden');
    }
};


(function ($) {

    let cart = new Cart();

    // related template: src/FMT/PublicBundle/Resources/views/common/cart_header_html.twig

    if ($.fmt === undefined || $.fmt.cart === undefined) {
        $.fmt = $.fmt || {};
        $.fmt.cart = {};
    }

    $.fmt.cart.setItemsNumber = function (number) {
        cart.setItemsNumber(number);
    }

})(jQuery);
