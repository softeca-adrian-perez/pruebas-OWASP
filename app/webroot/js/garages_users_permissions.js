$(document).ready(function(){
    garages_permissions.load();
});

var garages_permissions  = (function(){

    var garagesPermissions = function(){

        $('.view_garage_permission').on('click',function(){
            $('#garage_id_click').val( $(this).data('garage-id') );
			$('.list_permissions-js').empty();
        });

        $(".search-users-permissions-garages-js").click(function(event){
            var element = $(this);
            event.preventDefault();
            setTimeout(function(){
                var url = element.data('url') + '?' + $('#search-permissions-js').serialize();
                var div = element.data('div_users_permissions');
                var data = {};
                data.garage_id = $('#garage_id_click').val();
                var request = PeticionAjax.post(url, data);
                var permission_list_val = $('#permission_list').val();
                request.done(function(data){
                    $(div).html(data);
                    Select2.load();
                    paginateResult();
                    garagesPermissions();
                    $('#permission_list').val( permission_list_val ).trigger('change');
                });
            }, 100);
        });

        var paginateResult = function(){
            $('#ModalPermissions .paginacion a').off('off').on('click',function(e){
                e.preventDefault();
                var div = $('.search-users-permissions-garages-js').data('div_users_permissions');
                var url = $(this).attr('href');
                var data = {};
                data.garage_id = $('#garage_id_click').val();
                var request = PeticionAjax.post(url,data);
                request.done(function(data){
                    $(div).html(data);
                    Select2.load();
                    paginateResult();
                    garagesPermissions();
                });
            })
        }
    };

    return {
        load: function(){
            garagesPermissions();
        }
    }

})();
