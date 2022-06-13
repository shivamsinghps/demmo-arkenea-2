(function ($) {

    // Data attributes which are used in popup component
    // Template with this attributes is located in src/FMT/PublicBundle/Resources/views/common/_modal_html.twig
    // data-popup-title - popup title element
    // data-popup-body - popup body element for messages
    // data-popup-footer - popup footer element with buttons
    // data-type - popup footer button

    var DEFAULT_TEMPLATE_SELECTOR = '.default-modal-popup',
        DEFAULT_CLASS_MAPPING = {
            'default': 'modal fade',
            'success': 'modal modal-success fade',
            'info': 'modal modal-info',
            'warning': 'modal modal-warning fade',
            'error': 'modal modal-danger fade',
        },
        DEFAULT_BUTTON_CLASS_MAPPING = {
            'default': 'btn-default',
            'success': 'btn-outline',
        };

    if ($.fmt === undefined || $.fmt.popup === undefined) {
        $.fmt = $.fmt || {};
        $.fmt.popup = {};
        $.fmt.popup.MODAL_TYPE_SUCCESS = 'success';
        $.fmt.popup.MODAL_TYPE_DEFAULT = 'default';
        $.fmt.popup.MODAL_TYPE_INFO = 'info';
        $.fmt.popup.MODAL_TYPE_WARNING = 'warning';
        $.fmt.popup.MODAL_TYPE_ERROR = 'error';
    }

    var PopupClass = function () {
        this.type = $.fmt.popup.MODAL_TYPE_DEFAULT;
        this.title = '';
        this.message = '';
        this.buttons = {};
        this.template = DEFAULT_TEMPLATE_SELECTOR;
        this.$popup = {};

        this.show = function (options) {
            this.type = options.type || this.type;
            this.title = options.title || this.title;
            this.message = options.message || this.message;
            this.buttons = options.buttons || this.buttons;
            this.template = options.template || this.template;

            if (!this.message) {
                throw new Error('Empty message!');
            }

            if (typeof options == 'object' && Object.keys(options).length) {
                for (var classOption in this) {
                    if (options.hasOwnProperty(classOption)) {
                        this[classOption] = options[classOption];
                    } else if (typeof this[classOption] == 'object') {
                        this[classOption] = {};
                    }
                }
            }

            var defaultBtnClass = this.type == $.fmt.popup.MODAL_TYPE_DEFAULT ?
                DEFAULT_BUTTON_CLASS_MAPPING[this.type] :
                DEFAULT_BUTTON_CLASS_MAPPING[$.fmt.popup.MODAL_TYPE_SUCCESS];

            this.$popup = $(this.template);
            this.$popup.addClass(DEFAULT_CLASS_MAPPING[this.type]);
            this.$popup.find('[data-popup-title="true"]').text(this.title);
            this.$popup.find('[data-popup-body="true"]').html(this.message);
            this.$popup.find('[data-popup-footer="true"]').find('[data-type="modal-button"]').removeClass('btn-default').addClass(defaultBtnClass);
            this.$popup.find('[data-popup-footer="true"]').find('button').not(':first').remove();

            if (Object.keys(this.buttons).length) {
                for (var button in this.buttons) {
                    this.addButton(button, this.buttons[button]);
                }
            }
            this.buttons = {};
            this.$popup.modal('show');
        };

        this.hide = function (options) {
            options = options || {};

            if (options.hasOwnProperty('type')) {
                this.type = options.type;
            }

            var currentPopupClass = '.' + DEFAULT_CLASS_MAPPING[this.type].split(' ').join('.');
            $(currentPopupClass).modal('hide');
        };

        this.addButton = function (buttonText, button) {
            var _self = this,
                buttonClass = button.className || 'btn btn-default',
                buttonTemplate = this.$popup.find('[data-type="modal-button"]:first').clone();
            buttonTemplate.removeAttr('data-type').removeAttr('data-dismiss').removeClass();
            buttonTemplate.text(buttonText).addClass(buttonClass);

            if (typeof button.callback == 'function') {
                buttonTemplate.on('click', function () {
                    button.callback.call(button.callback, _self);
                });
            }

            this.$popup.find('.modal-footer').append(buttonTemplate);
        };
    };

    $.fmt.popup.showPopup = function (options) {
        var instance = $(this).data("popup-instance");

        if (!instance) {
            instance = new PopupClass();
            $(this).data("popup-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance, arguments.splice(1));
        }

        instance.show(options);

        return instance;
    };

    $.fmt.popup.hidePopup = function (options) {
        var instance = $(this).data("popup-instance");

        if (instance) {
            options = options || {};
            instance.hide(options);
        }

        return instance;
    };

})(jQuery);
