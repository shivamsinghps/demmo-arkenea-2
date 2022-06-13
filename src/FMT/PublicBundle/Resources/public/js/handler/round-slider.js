(function ($) {
    $(document).ready(function () {
        $('.round-slider').each(function () {
            var $element = $(this),
                color = $(this).data('color'),
                gradient = null;

            if ($element.hasClass('round-slider-half')) {
                if ($element.hasClass('slider-horizontal-gradient')) {
                    gradient = "url(#horizontal-gradient)";
                } else if ($element.hasClass('slider-vertical-gradient')) {
                    gradient = "url(#vertical-gradient)";
                }
            }

            $element.roundSlider({
                svgMode: true,
                circleShape: getPropertyValue('circle-shape'),
                min: getPropertyValue('min'),
                max: getPropertyValue('max'),
                radius: getPropertyValue('radius'),
                startAngle: getPropertyValue('start-angle'),
                width: getPropertyValue('border-width'),
                value: $element.data('value'),
                readOnly: true,
                keyboardAction: false,
                mouseScrollAction: false,
                tooltipColor: color,
                rangeColor: gradient ? 'none' : color,
                borderColor: '#fff',
                pathColor: gradient ? gradient : '#eee',
                sliderType: 'min-range',
                handleShape: 'square',
                tooltipFormat: function (e) {
                    switch (true) {
                        case $element.hasClass('round-slider-price'):
                            return '$' + e.value;
                        case $element.hasClass('round-slider-half'):
                            return e.value + '%';
                        default:
                            return e.value;
                    }
                }
            });

            $element.find('.rs-handle').css('background-color', color);

            function getPropertyValue(prop) {
                var attr = $element.attr('data-' + prop);

                if (attr === false || typeof attr === typeof undefined) {
                    return null;
                }

                return $element.data(prop);
            }
        });
    });
})(jQuery);
