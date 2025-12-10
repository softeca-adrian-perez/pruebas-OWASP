$(document).ready(function(){
    Vehicle.load();
});

var Vehicle = (function(){

    var select = function(){
        $('.vehicle_specialist_service').click(function(){
            var vehicle_specialist = $(this).find('.garage_vehicle').attr('id');
            var vehicle = vehicle_specialist.replace('vehicle_specialist_','vehicle_');
            $('#' + vehicle).prop( "checked", true );
        });

        $('.vehicle_service').click(function(){
            var vehicle = $(this).find('.garage_vehicle').attr('id');
            var vehicle_specialist = vehicle.replace('vehicle_','vehicle_specialist_');
            $('#' + vehicle_specialist).prop( "checked", false );
        });
    };

    var markUnmark = function () {
        $("#mark_all_vehicles").click(function (e) {
            e.preventDefault();
            $('.vehicle_service').find('.garage_vehicle').prop('checked', true);
        });
        $("#unmark_all_vehicles").click(function (e) {
            e.preventDefault();
            $('.garage_vehicle').prop('checked', false);
        });
    };

    return {
        load: function(){
            select();
            markUnmark();
        }
    }
})();