$(document).ready(function () {
    Maintenance.load();
});

var Maintenance = (function () {
    var init = function () {
        $('.maintenance_folder_closed-js').off('click').on('click', function () {
            var section = $(this).data('section');
            $('.boton-seccion').each(function(){
                if(!$(this).hasClass('btn-item'))
                {
                    if($(this).data('section') != section)
                    {
                        $(this).hide();
                        $(this).removeClass('open');
                    }
                    else
                    {
                        $(this).addClass('open');
                    }
                }
            });
            $(this).parent().show();
            $('.show-folder').each(function () {
                if ($(this).data('section') == section) {
                    $(this).show();
                }
            });
            $(this).removeClass('maintenance_folder_closed-js');
            $(this).addClass('maintenance_folder_opened-js');
            init();
        });
        $('.maintenance_folder_opened-js').off('click').on('click', function () {
            $('.boton-seccion').each(function(){
                if(!$(this).hasClass('btn-item')){
                    $(this).show();
                    $(this).removeClass('open');
                }
            });
            var section = $(this).data('section');
            $('.show-folder').each(function () {
                if ($(this).data('section') == section) {
                    $(this).hide();
                }
            });

            $(this).removeClass('maintenance_folder_opened-js');
            $(this).addClass('maintenance_folder_closed-js');
            init();
        });

        $('svg').off('click').on('click',function(){
            $(this).next().trigger('click');
        });
    };

    return {
        load: function () {
            init();
        }
    }

})();