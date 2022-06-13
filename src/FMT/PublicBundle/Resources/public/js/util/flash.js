(function ($) {

    // Data attributes which are used in flash component
    // Template with this attributes is located in src/FMT/PublicBundle/Resources/views/common/_flash_messages.htm.twig
    // data-icon-type - icon template with current message type
    // data-flash-title - message title
    // data-flash-message - message body

    var DEFAULT_HIDE_TIMEOUT = 17000,
        DEFAULT_TEMPLATE_SELECTOR = '.flash-message-template',
        DEFAULT_TARGET_ELEMENT = '.container > .content',
        DEFAULT_TEMPLATE_ICON_SELECTOR = '.flash-message-template-icon',
        DEFAULT_CLASS_MAPPING = {
            'error': 'alert alert-danger alert-dismissible',
            'info': 'alert alert-info alert-dismissible',
            'warning': 'alert alert-warning alert-dismissible',
            'success': 'alert alert-success alert-dismissible',
        },
        DEFAULT_SCROLL_TO_ALERT = false;

    if ($.fmt === undefined || $.fmt.flash === undefined) {
        $.fmt = $.fmt || {};
        $.fmt.flash = {};
        $.fmt.flash.FLASH_TYPE_INFO = 'info';
        $.fmt.flash.FLASH_TYPE_WARNING = 'warning';
        $.fmt.flash.FLASH_TYPE_SUCCESS = 'success';
        $.fmt.flash.FLASH_TYPE_ERROR = 'error';
    }

    var FlashClass = function (options) {

        this.type = options.type || $.fmt.flash.FLASH_TYPE_SUCCESS;
        this.message = options.message || '';
        this.autohide = (options.autohide || options.autohide === false) ? options.autohide : DEFAULT_HIDE_TIMEOUT;
        this.template = options.template || DEFAULT_TEMPLATE_SELECTOR;
        this.targetElement = options.targetElement || DEFAULT_TARGET_ELEMENT;
        this.iconBlock = options.iconBlock || DEFAULT_TEMPLATE_ICON_SELECTOR;
        this.allowedAutoHideType = options.allowedAutoHideType || [
            $.fmt.flash.FLASH_TYPE_INFO,
            $.fmt.flash.FLASH_TYPE_WARNING,
            $.fmt.flash.FLASH_TYPE_SUCCESS
        ];
        this.scrollToAlert = options.scrollToAlert || DEFAULT_SCROLL_TO_ALERT;

        this.show = function () {
            var _self = this;

            if (!this.message) {
                throw new Error('Empty message!');
            }

            var templateIcon = $(this.iconBlock).find('[data-icon-type="' + this.type + '"]').clone();
            var template = $(this.template).clone();
            template.removeClass().addClass(DEFAULT_CLASS_MAPPING[this.type]);
            var templateHeader = template.find('[data-flash-title="true"]');
            templateHeader.text(this.type);
            templateHeader.prepend(templateIcon);
            template.find('[data-flash-message="true"]').html(this.message);

            $(this.targetElement).prepend(template);

            if (this.autohide !== false && !isNaN(Number(this.autohide)) && this.allowedAutoHideType.indexOf(this.type) != -1) {
                setTimeout(function () {
                    _self.hide(template);
                }, this.autohide)
            }

            if (this.scrollToAlert) {
                template.ScrollTo();
            }
        };

        this.hide = function (element) {
            element.remove();
        };
    };

    $.fmt.flash.addFlash = function (options) {
        var flashClass = new FlashClass(options);
        flashClass.show();

        return flashClass;
    };

    $.fmt.flash.removeFlash = function () {
        $('.alert.alert-dismissible').remove();
    };
})(jQuery);
