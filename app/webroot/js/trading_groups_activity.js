$(document).ready(function(){
    Activity.load();
});

var Activity  = (function(){
    var activity = function (){
        if( $('#1_activity').prop('checked')== false ) {
            $('#1_join').hide();
            $('#1_left').hide();
        }

        if( $('#2_activity').prop('checked')== false ) {
            $('#2_join').hide();
            $('#2_left').hide();
        }

        if( $('#3_activity').prop('checked')== false ) {
            $('#3_join').hide();
            $('#3_left').hide();
        }

        if( $('#4_activity').prop('checked')== false ) {
            $('#4_join').hide();
            $('#4_left').hide();
        }

        if( $('#5_activity').prop('checked')== false ) {
            $('#5_join').hide();
            $('#5_left').hide();
        }
    }

    return {
        load: function($context){
            activity();
        }
    }
})();

function Disable1(id){
    if($('#'+id).prop('checked') == false){
        $('#1_join').hide();
        $('#1_left').hide();
    }
    if($('#'+id).prop('checked') == true){
        $('#1_join').show();
        $('#1_left').show();
    }
}

function Disable2(id){
    if($('#'+id).prop('checked') == false){
        $('#2_join').hide();
        $('#2_left').hide();
    }
    if($('#'+id).prop('checked') == true){
        $('#2_join').show();
        $('#2_left').show();
    }
}

function Disable3(id){
    if($('#'+id).prop('checked') == false){
        $('#3_join').hide();
        $('#3_left').hide();
    }
    if($('#'+id).prop('checked') == true){
        $('#3_join').show();
        $('#3_left').show();
    }
}

function Disable4(id){
    if($('#'+id).prop('checked') == false){
        $('#4_join').hide();
        $('#4_left').hide();
    }
    if($('#'+id).prop('checked') == true){
        $('#4_join').show();
        $('#4_left').show();
    }
}

function Disable5(id){
    if($('#'+id).prop('checked') == false){
        $('#5_join').hide();
        $('#5_left').hide();
    }
    if($('#'+id).prop('checked') == true){
        $('#5_join').show();
        $('#5_left').show();
    }
}