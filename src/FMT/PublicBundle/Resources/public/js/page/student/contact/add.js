(function ($) {
    let ContactForm = function (config) {
        this.$form = $(config.container).first();
        this.$fields = this.$form.find($('.form-group'));
        this.$submitButton = this.$form.find('button[type="submit"]');
        this.$cancelButton = $('#cancel-create');

        this.__init();
    };

    ContactForm.prototype.__init = function () {
        let _self = this;

        _self.$form.on('submit', function (e) {
            e.preventDefault();

            _self.submitForm(_self.$form);
        });

        _self.$cancelButton.on('click', function () {
            _self.hideForm();
            _self.clearForm();
        });
    };

    ContactForm.prototype.submitForm = function (form) {
        let _self = this;

        $.fmt.ajax.send({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize()
        }).then(function (response) {
            _self.removeErrors();

            if (!response.success) {
                $.each(response.data.errors, function (key, value) {
                    let [index] = key.split('-').slice(-1);

                    let field = $("input[name*='" + index +"']");
                    field.parent().addClass('has-error');
                    let errorMessage = '<span class="help-block">' + value + '</span>';

                    field.after(errorMessage);
                });
            } else {
                window.location.href = window.location;
            }
        }).catch(function (error) {
            console.log(error);
        });

        return false;
    };

    ContactForm.prototype.hideForm = function () {
        let _self = this;

        _self.$form.closest('.dropdown').removeClass('open');
    };

    ContactForm.prototype.clearForm = function () {
        this.removeErrors();

        $(this.$form).find('input').each(function() {
            $(this).val('');
        });
    };

    ContactForm.prototype.removeErrors = function () {
        this.$fields.removeClass('has-error');
        this.$form.find($('span.help-block')).remove();
    };

    $.fn.ContactForm = function (args) {
        let instance = $(this).data("contact-form-instance");

        if (!instance) {
            let options = {container: $(this)};
            if (typeof args == "object") {
                options = $.extend(args, options);
            }
            instance = new ContactForm(options);
            $(this).data("contact-form-instance", instance);
        }

        if (typeof args == "string" && typeof instance[args] == "function") {
            return instance[args].call(instance);
        }

        return this;
    };

    $(document).ready(function () {
        $('#create-contact-form').ContactForm();
    });
})(jQuery);
