$(document).ready(function(){
    Route.load();
});

var Route = (function(){

    var deleteRoute = function(){
        $('.delete-route-js').on('click',function(){
            var element = $(this);
            var url=  $(this).data('url');
            var data = {};
            data.route_id = $(this).data('id');
            data.route_type = $(this).data('type');
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url, data);
                    request.done(function (data) {
                        $('#cnt-routes-lists').html(data);
                        deleteRoute();
                        emptySearch();
                    });
                }
            });
        });
    };

    var emptySearch = function(){
        $('.route_search').each(function(){
            $(this).val('').trigger('change');
        });
    };

    return {
        load: function(){
            deleteRoute();
        }
    }
})();