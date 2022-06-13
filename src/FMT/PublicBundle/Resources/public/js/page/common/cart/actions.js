function CartActions(config) {
    let self = this,
        defaults;

    config = config || {};
    defaults = {
        settings: {
            summary: true
        },
        selectors: {
            catalogAreaClass: 'cart',
            actionClass: 'cart-action',
            rowClass: 'rowClass',
            emptyCartNote: 'cart-is-empty',
            summary: {
                wrapper: 'cart-summary',
                subtotal: 'cart-summary-subtotal',
                shipping: 'cart-summary-shipping',
                tax: 'cart-summary-tax',
                fmtFee: 'cart-summary-fmt-fee',
                transactionFee: 'cart-summary-transaction-fee',
                total: 'cart-summary-total',
            }
        },
        routes: {
            add: 'fmt-cart-add',
            remove: 'fmt-cart-remove',
        },
        actionButtonConfig: {
            initialState: 'remove',
            // define the next state. If FALSE, the element is removed from the table
            transitions: {
                remove: false
            },
            selectors: {
                add: 'cart-add',
                remove: 'cart-remove'
            },
            styles: {
                add: ['fa-cart-plus', 'text-green'],
                remove: ['fa-remove', 'text-red']
            },
        }
    };

    self.settings = Object.assign({}, defaults.settings, config.settings);
    self.selectors = Object.assign({}, defaults.selectors, config.selectors);
    self.routes = Object.assign({}, defaults.routes, config.routes);
    self.actionButtonConfig = Object.assign({}, defaults.actionButtonConfig, config.actionButtonConfig);

    self.init();
}

CartActions.prototype.init = function () {
    let self = this,
        actionButton;

    // initializing dom elements
    self.$catalogArea = $('.' + self.selectors.catalogAreaClass);
    self.$emptyCart = self.$catalogArea.find('.' + self.selectors.emptyCartNote);
    self.$summary = {
        wrapper: self.$catalogArea.find('.' + self.selectors.summary.wrapper)
    };

    $.each(self.selectors.summary, function (index, className) {
        self.$summary[index] = self.$catalogArea.find('.' + className);
    });

    // initializing action buttons
    self.controls = [];
    $.each(self.$catalogArea.find('.' + self.selectors.actionClass), function () {
        actionButton = new ActionButton({
            element: this,
            config: self.actionButtonConfig
        });

        self.controls.push(actionButton);
    });

    self.attachEventListeners();

    self.flash = new FlashNotification();
    self.cartHeader = $.fmt.cart;
};

CartActions.prototype.attachEventListeners = function () {
    let self = this;

    $.each(self.controls, function (i, control) {
        control.$element.parent().on('click', function () {
            $.fmt.ajax
                .send({
                    method: 'post',
                    url: Routing.generate(self.routes[control.state], {product: control.product.id}, true)
                })
                .then(function (response) {
                    console.log(response);

                    if (response.success !== true) {
                        self.flash.error(response.messages.error.join('. '));
                        return;
                    }

                    if (control.getTransition() !== false) {
                        // change state of the clicked button
                        control.setState(control.getTransition());
                    } else {
                        // ...or delete row together with th button
                        self.deleteRow(control);
                    }

                    // update counter in header
                    self.cartHeader.setItemsNumber(response.data.summary['itemsCount']);

                    // update cart summary
                    if (self.settings.summary) {
                        self.updateSummary(response.data.summary);
                    }

                    self.flash.success(response.messages.success.join('. '));
                })
                .catch(function (error) {
                    console.log(error);
                    self.flash.error('Sorry. Unexpected error happened');
                });
        });
    });
};

/**
 * Update summary numbers and hide it all if the cart is empty
 *
 * @param summary
 */
CartActions.prototype.updateSummary = function (summary) {
    let self = this;

    $.each(self.$summary, function (index, $element) {
        $element.html(summary[index]);
    });

    if (summary['itemsCount'] === 0) {
        self.$emptyCart.removeClass('hidden');
        self.$summary.wrapper.addClass('hidden');
    } else {
        self.$emptyCart.addClass('hidden');
        self.$summary.wrapper.removeClass('hidden');
    }
};

/**
 * Delete product row together with the button
 *
 * @param control
 */
CartActions.prototype.deleteRow = function (control) {
    let self = this,
        index;

    if (index = self.controls.indexOf(control) === -1) {
        console.error('Control was not found');
        return;
    }

    self.$catalogArea.find('.' + control.rowId).hide();
    self.controls.slice(index, 1);
};

function ActionButton(config) {
    let self = this;

    self.element = config.element;
    self.config = config.config;

    self.init();
}

ActionButton.prototype.init = function () {
    let self = this;

    self.$element = $(self.element);
    self.product = JSON.parse(self.element.dataset.product);
    // string ID to link button with its row
    self.rowId = self.element.dataset['rowId'];

    self.state = self.$element.hasClass(self.config.selectors[self.config.initialState])
        ? self.config.initialState
        : self.config.transitions[self.config.initialState];
};

ActionButton.prototype.setState = function (state) {
    let self = this;

    if (!self.config.transitions.hasOwnProperty(state)) {
        console.error('Unsupported action state: ' + state);
        return;
    }

    if (state === self.state) {
        console.log('State "' + state + '" is already applied');
        return;
    }

    self.applyStateStyles(state);

    self.state = state;
};

ActionButton.prototype.applyStateStyles = function (state) {
    let self = this;

    $.each([self.config.selectors[self.state]].concat(self.config.styles[self.state]), function (i, className) {
        self.$element.removeClass(className);
    });

    $.each([self.config.selectors[state]].concat(self.config.styles[state]), function (i, className) {
        self.$element.addClass(className);
    });
};

/**
 * Get the next state of the button after if is clicked (and it acted successfully)
 *
 * @returns {*}
 */
ActionButton.prototype.getTransition = function () {
    let self = this;

    return self.config.transitions[self.state];
};

function FlashNotification(config) {}

FlashNotification.prototype.success = function (message) {
    $.fmt.flash.addFlash({
        message: message
    });
};

FlashNotification.prototype.error = function (message) {
    $.fmt.flash.addFlash({
        type: $.fmt.flash.FLASH_TYPE_ERROR,
        message: message,
        scrollToAlert: true
    });
};
