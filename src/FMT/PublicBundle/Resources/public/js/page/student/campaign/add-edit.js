(function ($) {
    $(document).ready(function () {
        $('.datepicker').DateWidget({
            minDate: moment().startOf('date')
        });

        $('.thanks-dropdown').on('show.bs.dropdown', function () {
            $(this).find('.thanks-text-area textarea').height(50)
        })
    });
}) (jQuery);
