(function ($) {
    $(document).ready(function () {
        toggleSocialVisibility(false);
        $('[data-visibility]').on('change', function (event) {
            toggleSocialVisibility(true);
        });

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
    });
})(jQuery);
