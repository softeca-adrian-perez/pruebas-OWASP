$(document).ready(function(){
    trading_groups_permissions.load();
});

var trading_groups_permissions  = (function(){

    var tradingGroupsPermissions = function(){

        $('.view_trading_groups_permission').on('click',function(){
            $('#trading_group_id_click').val( $(this).data('trading-group-id') );
        });

        $(".search-users-permissions-trading_groups-js").click(function(event){
            var element = $(this);
            event.preventDefault();
                setTimeout(function(){
                var url = element.data('url') + '?' + $('#search-permissions-js').serialize();
                var div = element.data('div_users_permissions');
                var data = {};
                data.trading_group_id = $('#trading_group_id_click').val();
                var request = PeticionAjax.post(url,data);
                var permission_list_val = $('#permission_list').val();
                request.done(function(data){
                    $(div).html(data);
                    Select2.load();
                    paginateResult();
                    tradingGroupsPermissions();
                    $('#permission_list').val( permission_list_val ).trigger('change');
                });
            }, 100);
        });

        var paginateResult = function(){
            $('#ModalPermissions .paginacion a').off('off').on('click',function(e){
                e.preventDefault();
                var div = $('.search-users-permissions-trading_groups-js').data('div_users_permissions');
                var url = $(this).attr('href');
                var data = {};
                data.trading_group_id = $('#trading_group_id_click').val();
                var request = PeticionAjax.post(url,data);
                request.done(function(data){
                    $(div).html(data);
                    Select2.load();
                    paginateResult();
                    tradingGroupsPermissions();
                });
            })
        }

    };

    return {
        load: function(){
            tradingGroupsPermissions();
        }
    }

})();