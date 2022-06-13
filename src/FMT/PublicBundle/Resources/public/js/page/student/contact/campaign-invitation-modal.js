(function ($) {
    $(document).ready(function () {
        var $submitButton = $('.campaign-invitation-modal button[type="submit"]');
        var $contactDeleteButton = $('.delete-contact');
        var $errorWrapper = $('.content')

        function showFlash(type, message) {
            $('.box-body .alert-dismissible').remove();
            $.fmt.flash.addFlash({
                "type": type,
                "message": message,
                "targetElement": $errorWrapper,
                "scrollToAlert": true
            });
        }

        function showFormErrors(form, errors) {
            hideFormErrors(form);
            $.each(errors, function (key, value) {
                var errorWrap = form.find("." + key + "-error-wrap");
                var errorMessage = '<span class="help-block-error">' + value + '</span>';
                errorWrap.append(errorMessage);
            });
        }

        function hideFormErrors(form) {
            form.find('.help-block-error').remove();
        }

        $submitButton.on('click', function (e) {
            e.preventDefault();

            var form = $(this).closest('.campaign-invitation-modal').find('form[name="campaign_invitation"]');

            $.fmt.ajax.send({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize()
            }).then(function (response) {
                if (response.success) {
                    window.location.href = window.location;
                } else {
                    showFormErrors(form, response.data.errors);

                    if (response.data.message) {
                        showFlash($.fmt.flash.FLASH_TYPE_ERROR, response.data.message);
                    }
                }
            }).catch(function (error) {
                console.log(error);
            });
        });

        $contactDeleteButton.on('click', function (e) {
            e.preventDefault();

            var campaignModal = $(this).closest('.campaign-delete-modal');

            $.fmt.ajax.send({
                url: $(this).data('href'),
                method: 'POST'
            }).then(function (response) {
                if (response.success) {
                    window.location.href = window.location;
                } else {
                    showFlash($.fmt.flash.FLASH_TYPE_ERROR, response.data.message);
                    campaignModal.modal('toggle');
                }
            });
        })

        $('[name="campaign_invitation[isPersonalNoteNeeded]"]').change(function () {
            var personalNoteInput = $(this).closest('form[name="campaign_invitation"]')
                .find('textarea[name="campaign_invitation[personalNote]"]');
            if (this.value === '1') {
                personalNoteInput.prop('readonly', false);
            } else {
                personalNoteInput.val('');
                personalNoteInput.prop('readonly', true);
            }
        });

        $('.campaign-invitation-modal').on('show.bs.modal', function (e) {
            $('[data-toggle="tooltip"]').tooltip('hide');
        });

    });
})(jQuery);
