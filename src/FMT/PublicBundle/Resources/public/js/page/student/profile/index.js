(function ($) {

    var AVATAR_MAX_SIZE = 1024,
        AVATAR_SIZE_SUFFIX = 'KB',
        AVATAR_MIN_WIDTH = '280',
        AVATAR_MIN_HEIGHT = '252',
        AVATAR_ALLOWED_TYPE = [
            'image/png',
            'image/jpeg',
            'image/bmp',
        ],
        ERROR_MESSAGES = {
            'type': $('[data-type-error-message]').data('type-error-message'),
            'size': $('[data-size-error-message]').data('size-error-message')
                .replace('{{ limit }}', AVATAR_MAX_SIZE)
                .replace('{{ suffix }}', AVATAR_SIZE_SUFFIX),
            'width': $('[data-width-error-message]').data('width-error-message')
                .replace('{{ min_width }}', AVATAR_MIN_WIDTH),
            'height': $('[data-height-error-message]').data('height-error-message')
                .replace('{{ min_height }}', AVATAR_MIN_HEIGHT),
        };

    $(document).on('change', ':file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });

    function setupModal() {
        const $modal = $('.modal')
        $modal.find('.modal-header').text('Crop avatar')
        $modal.find('.modal-body').html('<div style="width: 100%"><img class="cropper-image" style="width: 100%" src="" alt=""/></div>')
        $modal.find('.modal-footer').prepend('<button type="button" style="color: white" class="btn btn-primary btn-default pull-left apply-button" data-dismiss="modal" data-type="modal-button">APPLY</button>')
        $modal.find('.modal-footer .apply-button').click(e => {
            const canvas = $('.cropper-image').cropper('getCroppedCanvas', {minWidth: 280, minHeight: 252})
            const dataUrl = canvas.toDataURL()
            $('.avatar-wrapper img:first-child').attr('src', dataUrl);
            canvas.toBlob(blob => {
                const $file = $(':file')
                const dt = new DataTransfer()
                dt.items.add(new File([blob], $file.data('filename')))

                $file.prop('files', dt.files)
            })
        })
        $modal.on('hidden.bs.modal', () => {
            $('.cropper-image').cropper('destroy');
        })
    }

    setupModal()

    function setupAvatarPreview(input) {
        if (input.files && input.files[0]) {
            if (!$('.avatar-wrapper img').length) {
                $('.avatar-wrapper').html('<img src="" alt=""/>');
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                const $cropperImage = $('.cropper-image')
                $cropperImage.attr('src', e.target.result);

                const interval = setInterval(() => {
                    if ($cropperImage.width() > 0 && $cropperImage.height() > 0) {
                        clearInterval(interval);
                        showCropper($cropperImage);
                    }
                }, 20);
            }
            reader.readAsDataURL(input.files[0]);
            $('.modal').modal('show')
        }
    }

    $(document).ready(function () {
        $(':file').on('fileselect', function (event, numFiles, label) {
            if (validate(event.delegateTarget.files[0])) {
                setupAvatarPreview(this);
            }

            this.dataset.filename = event.delegateTarget.files[0].name
            this.value = ''
        });

        toggleSocialVisibility(false);
        $('[data-visibility]').on('change', function () {
            toggleSocialVisibility(true);
        });
    });

    function showCropper($cropperImage) {
        const ratio = $cropperImage.height() / $cropperImage.get(0).naturalHeight

        $cropperImage.cropper({
            viewMode: 3,
            aspectRatio: 10 / 9,
            minCropBoxWidth: 280 * ratio,
            minCropBoxHeight: 252 * ratio,
            zoomable: false,
            rotatable: false,
            responsive: false,
            toggleDragModeOnDblclick: false,
        })
    }

    function getFileSizeInKilobytes(bytes) {
        return bytes / 1024;
    }

    function getFileSizeInMegabytes(bytes) {
        return bytes / (1024 * 1024);
    }

    function validate(file) {
        var isValidType = AVATAR_ALLOWED_TYPE.indexOf(file.type) != -1 ? true : 'type',
            isValidSize = getFileSizeInKilobytes(file.size) <= AVATAR_MAX_SIZE ? true : 'size',
            uploadBtnWrap = $('[data-upload-block="true"]'),
            errorContainer = $('[data-error="error"]'),
            errorContainerParent = $('[data-error="error"]').parent();

        uploadBtnWrap.removeClass('has-error');
        errorContainerParent.removeClass('has-error');
        $('.help-block').text('')

        if (isValidType !== true) {
            uploadBtnWrap.addClass('has-error');
            errorContainerParent.addClass('has-error');
            errorContainer.text(ERROR_MESSAGES[isValidType]);
            return false;
        }

        if (isValidSize !== true) {
            uploadBtnWrap.addClass('has-error');
            errorContainerParent.addClass('has-error');
            errorContainer.text(ERROR_MESSAGES[isValidSize]);
            return false;
        }

        var promise = new Promise(function (resolve, reject) {
            var _URL = window.URL || window.webkitURL;
            var img = new Image();
            var objectUrl = _URL.createObjectURL(file);
            img.onload = function () {
                if (this.width < AVATAR_MIN_WIDTH) {
                    uploadBtnWrap.addClass('has-error');
                    errorContainerParent.addClass('has-error');
                    errorContainer.text(ERROR_MESSAGES['width']);
                    reject()
                    return
                }

                if (this.height < AVATAR_MIN_HEIGHT) {
                    uploadBtnWrap.addClass('has-error');
                    errorContainerParent.addClass('has-error');
                    errorContainer.text(ERROR_MESSAGES['height']);
                    reject();
                    return;
                }

                resolve();
            }
            img.src = objectUrl;
        })
        promise.then(function () {
            $(':submit').prop('disabled', false);
            errorContainer.text('')
        }).catch(function () {
            $(':submit').prop('disabled', true);
        })

        return true;
    }

    function toggleSocialVisibility(isChanged) {
        var isSocialVisible = $('[data-visibility="select"]').find(':selected').data('visible') === 'yes';
        $('[data-social-btn]').each(function () {
            if (isChanged) {
                $(this).prop('checked', isSocialVisible);
            }

            $(this)
                .prop('disabled', !isSocialVisible)
                .closest('label').css('cursor', isSocialVisible ? 'pointer' : 'not-allowed');
        })
    }
})(jQuery);
