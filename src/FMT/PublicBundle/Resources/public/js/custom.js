(function ($) {
    //ajax authentication
    $('#loginForm').on('submit', function () {
        var form = $(this);

        $.fmt.ajax.send({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize()
        }).then(function (response) {
            if (response.success) {
                window.location.href = response.location;
            } else {
                form.find('.box-body').find('.has-error-message').remove();
                form.find('.has-input').removeClass('has-error').addClass('has-error');
                form.find('.box-body').append('<div class="form-group has-error has-error-message"><span class="help-block">' + response.message + '</span></div>');
            }
        }).catch(function (error) {
            console.log(error);
        });

        return false;
    });

    $('#tab_1').on('submit', 'form[name="registration_donor"]', function () {
        ajaxCall($(this));

        return false;
    });

    $('#tab_2').on('submit', 'form[name="registration_student"]', function () {
        ajaxCall($(this));

        return false;
    });

    $('#registration-box').on('submit', 'form', function () {
        ajaxCall($(this));

        return false;
    });

    function ajaxCall(form) {
        $.fmt.ajax.send({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize()
        }).then(function (response) {
            if (!response.success) {
                form.parent().html(response.form);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    //init validation
    $.validate();

    // fix hiding of dropdowns in case of clicking inside it
    $('.dropdown-menu.no-hide-click-inside').on('click', function (event) {
        var events = $._data(document, 'events') || {};
        events = events.click || [];
        for (var i = 0; i < events.length; i++) {
            if (events[i].selector) {
                //Check if the clicked element matches the event selector
                if ($(event.target).is(events[i].selector)) {
                    events[i].handler.call(event.target, event);
                }

                // Check if any of the clicked element parents matches the
                // delegated event selector (Emulating propagation)
                $(event.target).parents(events[i].selector).each(function () {
                    events[i].handler.call(this, event);
                });
            }
        }
        event.stopPropagation(); //Always stop propagation
    });

    $(document).on('click', '[data-btn=show-forgot-password]', function () {
        $('[data-btn=show-log-in]').dropdown('toggle');

        var login = $('#registration_donor_login').val();
        $('[data-reset-password-form-username="username"]').val(login);
        $('.login-form-wrapper').addClass('hidden');
        $('.reset-password-form-wrapper').removeClass('hidden');
    });

    var classesForSmallDevices = 'dropdown user user-menu';
    function resize() {
        let $elementsToChange = $('#navbar-collapse>li');
        $(window).width() < 768 ?
            $elementsToChange.removeClass(classesForSmallDevices) :
            $elementsToChange.addClass(classesForSmallDevices);
    }
    resize();
    $(window).resize(resize);

    // initialize all tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

})(jQuery);
