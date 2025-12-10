$(document).ready(function () {
    Genarts.load();
});

var Genarts = (function () {
    var worksTypeGarage = function () {
        $('.type_family_garage-js').on('change', function(){
            let genartId = $(this).data('element_id');
            $('.genart_input_' + genartId + '-js').each(function(){
                if ($(this).css('display') == 'none') {
                    $(this).css('display', 'flex');
                    $(this).attr('disabled', false);
                } else {
                    $(this).css('display', 'none');
                    $(this).attr('disabled', true);
                }
            });
        });
    }

    return {
        load: function () {
            worksTypeGarage();
        },
    };
})();
