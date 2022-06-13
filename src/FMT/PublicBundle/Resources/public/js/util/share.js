(function ($) {

    var DEFAULT_WINDOW_OPTIONS = 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,',
        DEFAULT_WINDOW_WIDTH = '600',
        DEFAULT_WINDOW_HEIGHT = '400';

    if ($.fmt === undefined || $.fmt.social === undefined) {
        $.fmt = $.fmt || {};
        $.fmt.social = {};
    }

    var ShareClass = function (options) {

        if(!options.windowLink){
            throw new Error('Every social network component must have a link for sharing.');
        }

        this.windowWidth = options.windowWidth || DEFAULT_WINDOW_WIDTH;
        this.windowHeight = options.windowHeight || DEFAULT_WINDOW_HEIGHT;
        this.windowOption = options.windowOption || DEFAULT_WINDOW_OPTIONS + 'width=' + DEFAULT_WINDOW_WIDTH + ',height=' + DEFAULT_WINDOW_HEIGHT;
        this.windowLink = options.windowLink;

        this.share = function () {
            var _self = this;
            window.open(_self.windowLink, '', _self.windowOption);
        }
    }

    $.fmt.social.share = function (options) {
        var shareClass = new ShareClass(options);
        shareClass.share();

        return shareClass;
    };

})(jQuery);
