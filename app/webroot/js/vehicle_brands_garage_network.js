$(document).ready(function(){
    VehicleBrand.load();
});

var VehicleBrand = (function(){

    var checkBoth = function () {
        $(".check-specialist-js").on('click', function (e) {
            $('#vehicle_black_list_' + $(this).data('id')).prop('checked', false);
        });
        $(".check-blacklist-js").click(function (e) {
            $('#vehicle_' + $(this).data('id')).prop('checked', false);
        });
    };

    return {
        load: function(){
            checkBoth();
        }
    }
})();