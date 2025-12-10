$(document).ready(function(){
    distributors_permissions.load();
});

var distributors_permissions  = (function(){

    var distributorsPermissions = function(){

        $('.view_distributor_permission').on('click',function(){
            $('#distributor_id_click').val( $(this).data('distributor-id') );
        });

        $(".search-users-permissions-distributors-js").click(function(event){
            var element = $(this);
            event.preventDefault();
            setTimeout(function(){
                var url = element.data('url') + '?' + $('#search-permissions-js').serialize();
                var div = element.data('div_users_permissions');
                var data = {};
                data.distributor_id = $('#distributor_id_click').val();
                var permission_list_val = $('#permission_list').val();
                var request = PeticionAjax.post(url,data);
                request.done(function(data){
                    $(div).html(data);
                    Select2.load();
                    paginateResult();
                    distributorsPermissions();
                    $('#permission_list').val( permission_list_val ).trigger('change');
                });
            }, 100);
        });

    };

    var paginateResult = function(){
        $('#ModalPermissions .paginacion a').off('off').on('click',function(e){
            e.preventDefault();
            var div = $('.search-users-permissions-distributors-js').data('div_users_permissions');
            var url = $(this).attr('href');
            var data = {};
            data.distributor_id = $('#distributor_id_click').val();
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $(div).html(data);
                Select2.load();
                paginateResult();
                distributorsPermissions();
            });
        })
    }

    return {
        load: function(){
            distributorsPermissions();
        }
    }

})();