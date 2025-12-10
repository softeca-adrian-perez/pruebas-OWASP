$(document).ready(function () {
    Toggle.load();
});

var Toggle = (function () {
    var changeToggle = function () {
        $('.toggle-js').each(function() {
            let id = $(this).data('toggle-id-js')
            changeStateArrowIcon($(getArrowElementString(id)), $(getContentElementString(id)))
        })
        $(document).ready(function () {
            $('.toggle-js').click(function () {
                let id = $(this).data('toggle-id-js')
                let contentElementString = getContentElementString(id)
                $(contentElementString).slideToggle("fast", function () {
                    changeStateArrowIcon($(getArrowElementString(id)), $(contentElementString));
                });
            });
        });
    }

    var getContentElementString = function (id) {
        return '.toggle-content-js[ data-toggle-id-js="' + id + '"]'
    }

    var getArrowElementString = function (id) {
        return '.toggle-js[ data-toggle-id-js="' + id + '"]'
    }

    var changeStateArrowIcon = function (element, listContainer) {
        var arrowIcon = element.find('.arrow-icon-js');
        if (listContainer.is(":visible")) {
            arrowIcon.removeClass('open');
        } else {
            arrowIcon.addClass('open');
        }
    };

    return {
        load: function () {
            changeToggle();
        },
    };
})();