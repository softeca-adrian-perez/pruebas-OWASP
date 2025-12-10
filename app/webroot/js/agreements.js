$(document).ready(function () {
    Agreement.load();
});

var Agreement = (function(){

    var sendData = function() {
        $("#btn-guardar").on('click',function(e){
            if($('#aag-region-select').length > 0){
                e.preventDefault();
                $('#aag-region-select').attr('disabled', false);
                $('#form').submit();
            }
        });
    }

    return {
        load: function(){
            sendData();
        }
    }
})();
