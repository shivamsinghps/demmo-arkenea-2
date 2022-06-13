(function ($) {
    $(document).ready(function () {
        var thanksData = $('#thanks-box').data('thanks-for-donations');
        var searchTimeoutId = null;
        var lastSearchedValue = $('#base_filter_search').val();
        var searching = false;

        if (thanksData.length) {
            var activePosition = Math.floor(Math.random() * thanksData.length);
            passThanksData(thanksData[activePosition]);
        }

        $(document).on('click', '.thanks-btn', function () {
            $(this).hasClass('thanks-btn-prev') ? decrementPosition() : incrementPosition();
            passThanksData(thanksData[activePosition]);
        });

        $('form[name="base_filter"]').on('submit', function (event) {
            event.preventDefault()

            if (searchTimeoutId !== null) {
                clearTimeout(searchTimeoutId);
                searchTimeoutId = null;
            }

            search($('#base_filter_search').val(), true)
        })

        $('#base_filter_search').on('input', function (event) {
            var inputValue = $(event.target).val();

            if (searchTimeoutId !== null) {
                clearTimeout(searchTimeoutId);
                searchTimeoutId = null;
            }

            searchTimeoutId = setTimeout(function () {
                search(inputValue);
            }, 1000)
        })

        $('#base_filter_search').on('blur', function (event) {
            search($(event.target).val())
        })

        function search(value, force = false) {
            if (searching || (!force && value === lastSearchedValue)) {
                return;
            }

            $('#base_filter_search').attr('readonly', true);
            $('.container .preloader').css('visibility', 'visible');

            lastSearchedValue = value;
            searching = true;

            $.ajax({
                url: window.location.origin + window.location.pathname,
                method: 'get',
                data: {
                    'base_filter': {
                        'search': value,
                        '_token': $('#base_filter__token').val(),
                    },
                },
            }).done(function (result) {
                var parser = new DOMParser();
                var dom = parser.parseFromString(result, 'text/html')

                $('.donors-students').html($(dom).find('.donors-students').html())
                $('#base_filter__token').val($(dom).find('#base_filter__token').val())
            }).always(function () {
                searching = false;
                window.history.pushState(null, '', this.url)
                $('#base_filter_search').attr('readonly', false);
                $('.container .preloader').css('visibility', 'hidden');
            })
        }

        function incrementPosition() {
            if (activePosition >= thanksData.length - 1) {
                return activePosition = 0;
            }

            ++activePosition;
        }

        function decrementPosition() {
            if (activePosition <= 0) {
                return activePosition = thanksData.length - 1;
            }

            --activePosition;
        }

        function passThanksData(data) {
            $('#thanks-box p:eq(0)').text(data.studentData.toUpperCase());
            $('#thanks-box p:eq(1)').text(data.schoolData);
            $('#thanks-box p:eq(2)').text(function () {
                return $(this)
                    .data('predefined-text')
                    .replace('%amount%', (data.fundedAmount / 100).toFixed(2))
                    .toUpperCase();
            });
            $('#thanks-box p:eq(3)').text('"' + data.thanksMessage + '"');
        }
    });
})(jQuery);
