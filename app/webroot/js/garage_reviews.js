$(document).ready(function(){
    GarageReviews.load();
});

var GarageReviews = (function(){

    var showLocationIdInput = function(){
        var kiyohCheckbox = $('#kiyoh-checkbox-js');
        var kiyohInputContainer = $('.kiyoh_input_container-js');

        if(kiyohCheckbox.prop('checked')){
            kiyohInputContainer.show();
        }

        kiyohCheckbox.on('change', function () {
            if ($(this).prop('checked')) {
                kiyohInputContainer.show();
            } else {
                kiyohInputContainer.hide();
            }
        });
    }

    let setTodayDateInInput = function() {
        $("#review_date-js").datepicker('setDate', new Date());
    }


    return {
        load: function(){
            showLocationIdInput();
            setTodayDateInInput();
        }
    }

})();