(function ($) {
    $(document).ready(function () {

        //back to login form
        $(document).on('click', '[data-toggle="forgot-link"]', function () {
            var login = $('[data-login-form-username="username"]').val();
            var wrapper = $('[data-toggle="forgot-wrapper"]');
            wrapper.find('[data-reset-password-form-username="username"]').val(login);
            wrapper.toggleClass('hidden');
        });

        $(document).on('submit', '[data-reset-password-form="form"]', function () {
            var form = $(this);
            $.fmt.ajax.send({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize()
            }).then(function (response) {
                if (!response.success) {
                    // var responseHtml = $(response.form).html();
                    form.find('[data-reset-password-form-input-wrapper="wrapper"]').remove();
                    form.prepend(response.form);

                    var errorTemplate = form.next('[data-reset-password-form-error-template="template"]').clone();
                    var inputWrapper = form.find('[data-reset-password-form-input-wrapper="wrapper"]');
                    inputWrapper.find('.form-error').remove();
                    inputWrapper.append(errorTemplate);
                    errorTemplate.toggleClass('hide');
                }
            }).catch(function (error) {
                console.log(error);
            });

            return false;
        });
    });
})(jQuery);
