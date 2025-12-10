$(document).ready(function(){
    Select2.load();
});

var Select2 = function() {

    var loadBehaviourSelect2 = function () {
        $(".select2-multiple").each(function(){
            if(!$(this).data('select2-multiple-applied')) {
                $options = $(this).find("option[value='']");
                if($options.length){
                    if(typeof( $(this).attr('multiple')) != 'undefined' ) {
                        $(this).select2({
                            allowClear: true
                        });
                    } else {
                        $(this).select2({
                            allowClear: true,
                            placeholder: '',
                        });
                    }
                } else {
                    $(this).select2();
                }
            }
        });
        $(".select2-multiple").data('select2-multiple-applied','1');
    };

    return {
        load: function(){
            loadBehaviourSelect2();
        }
    }
}();