(function ($) {

    var timeout = {};
    var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/;

    $(document).ready(function () {
        var formName = $('form[data-form-name]').data('form-name');
        //form
        $(document).on('submit', '[data-' + formName + '-form="form"]', function (e, url, removeQueryString) {
            var form = $(this),
                url = url || form.attr('action') || window.location.href.split('?')[0],
                data = form.serialize(),
                queryString = !removeQueryString ? url + '?' + data : url;

            history.pushState({}, '', queryString);

            if (form.data('reload-page')) {
                window.location.href = removeQueryString ? url : url + '?' + data;

                return false;
            }

            $.fmt.ajax.send({
                url: url,
                method: form.attr('method'),
                data: data
            }).then(function (response) {
                $('[data-ajax-container="container"]').html(response.form);
            }).catch(function (error) {
                console.log(error);
            });

            return false;
        });

        //all dropdown fields and radio buttons
        $(document).on('change', '[data-' + formName + '-dropdown-filter="dropdown"], [data-sort-order-choice]', function () {
            $(this).is('[data-original-page]') ?
                formSubmit($(this).data('original-page')) :
                formSubmit();
        });

        //search field
        $(document).on('keyup', '[data-' + formName + '-search-filter]', function () {
            var inputValueLength = $(this).val().length,
                _self = $(this);

            if (inputValueLength === 0 || date_regex.test(_self.val())) {
                formSubmit();
                _self.prop('disabled', true);
            }

            if (_self.data('search-by') === 'date' || _self.val().includes('/')) {
                return;
            }

            if(inputValueLength > 2) {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    formSubmit();
                    _self.prop('disabled', true);
                }, 1000);
            }
        });

        //pagination links
        $(document).on('click', '[data-pagintation-link="link"]', function () {
            var url = $(this).attr('href').split('?').shift();
            formSubmit(url);
            return false;
        });
        // column links
        $(document).on('click', '[data-sort-by]', function () {
            var sortByColumn = $(this).data('sort-by');
            var $dropdownFilter = $('[name="base_filter[sortBy]"]');

            if (sortByColumn === $dropdownFilter.val()) {
                toggleOrderDirection();
            } else {
                $dropdownFilter.val(sortByColumn)
            }

            formSubmit();
            return false;
        });

        function toggleOrderDirection() {
            $('[data-sort-order-choice]').each(function () {
                $(this).attr('checked', !$(this).is('checked'));
            });
        }

        // clear filter button
        $(document).on('click', '[data-' + formName + '-filter-clear]', function () {
            $('[data-' + formName + '-dropdown-filter="dropdown"]').prop('selected', function () {
                $(this).prop('selectedIndex',0);
            });
            $('[data-sort-order-choice]').prop('checked', false);
            $('[data-sort-order-choice]:first').prop('checked', true);
            $('[data-' + formName + '-search-filter]').val('');
            formSubmit($(this).data(formName + '-filter-clear'), true);
            return false;
        });

        function formSubmit(url, removeQueryString) {
            $('[data-' + formName + '-form="form"]').trigger('submit', [url, removeQueryString]);
        }
    });
})(jQuery);
