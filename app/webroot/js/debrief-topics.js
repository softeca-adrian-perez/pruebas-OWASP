$(document).ready(function(){
    Debrief.load();

});

var Debrief = (function(){

    var deleteTopic = function() {
        $('.delete-topic-js').click(function(e) {
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
            deleteTopic();
        }
    }

})();