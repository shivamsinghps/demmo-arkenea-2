(function ($) {

    var SUCCESS_ELEMENT_CLASS = 'has-success';
    var ERROR_ELEMENT_CLASS = 'has-error';

    $(document).ready(function () {
        var form = $('.confirm-password-form[data-form-target="validatior"]');
        if (form.length) {
            $.formUtils.addValidator({
                name: 'custom_regexp',
                validatorFunction: function (value, $el, config, language, $form, context) {
                    var repeatedPassword = form.find('[data-validation="custom_repeat_passsword"]');

                    if (context !== 'blur.revalidated') {
                        repeatedPassword.validateInputOnBlur(language, config, false, 'blur.revalidated');
                    }
                    var specialCharacter = false;
                    var upper = false;
                    var lower = false;
                    var number = false;

                    for (var i = 0; i < value.length; i++) {
                        if (/\d/s.test(value[i])) {
                            number = true;

                            continue;
                        }

                        if (value[i].toLowerCase() === value[i].toUpperCase()) {
                            specialCharacter = true;

                            continue;
                        }

                        if (value[i].toLowerCase() === value[i]) {
                            lower = true;
                        } else {
                            upper = true;
                        }
                    }
                    var validationArr = [
                        validatePassword(value.length >= 8, 'length'),
                        validatePassword(/[A-Z]+/s.test(value), 'upper'),
                        validatePassword(/[a-z]+/s.test(value), 'lower'),
                        validatePassword(/\d+/s.test(value), 'number'),
                        validatePassword(/[\W]+/s.test(value), 'special_character'),
                        validatePassword(repeatedPassword.val() === value && value !== '', 'both'),
                    ];

                    validationArr = validationArr.filter(function (currentValue) {
                        return currentValue;
                    });

                    return validationArr.length === 6;
                },
            });
            $.formUtils.addValidator({
                name: 'custom_repeat_passsword',
                validatorFunction: function (value, $el, config, language, $form, context) {
                    var passwordInput = form.find('[data-validation="custom_regexp"]');

                    if (context !== 'blur.revalidated') {
                        passwordInput.validateInputOnBlur(language, config, false, 'blur.revalidated');
                    }

                    return validatePassword(passwordInput.val() === value && value !== '', 'both');
                },
            });

            $.validate({ errorMessageClass: 'hide' });
        }
    });

    function validatePassword(isValid, type) {
        var elementClass = isValid ? SUCCESS_ELEMENT_CLASS : ERROR_ELEMENT_CLASS;

        $('[data-requirment="' + type + '"]').removeClass(SUCCESS_ELEMENT_CLASS).removeClass(ERROR_ELEMENT_CLASS).addClass(elementClass);

        return isValid;
    }
})(jQuery);