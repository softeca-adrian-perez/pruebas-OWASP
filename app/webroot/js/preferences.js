$(document).ready(function(){
    Preferences.load();
});

var Preferences = (function(){

    var saveOnChange = function(){
        $('.user_preference').on('change',function(){
            var url = $('#url_save_ajax').data('url');
            var data = {};
            $('.user_preference').each(function(){
                data[$(this).data('preference')] = $(this).val()
            });

            var request = PeticionAjax.post(url,data);
            request.done(function(){
                swal($.i18n._('Constants.Message_well_saved'), '','success');
            });
        });
    }

    return {
        load: function(){
            saveOnChange();
        }
    }
})();