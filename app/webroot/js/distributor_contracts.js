$(document).ready(function(){
    DistributorContract.load();
});

var DistributorContract = (function(){

    var loadNetworks = function(){
        $('#trading_group_id').on('change',function(){
            var url = $(this).data('url');
            var data = {};
            data.trading_group_id = $(this).val();
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $('#distributor_network').html(data);
                Select2.load();
            });
        });
    };

    var deleteDistributorContract = function() {
        $('.delete-distributor-contract-js').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            var element = $(this);
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            });
        })
    };

    return {
        load: function(){
            loadNetworks();
            deleteDistributorContract();
        }
    }

})();