$(document).ready(function () {
    Tutorials.load();
});

var Tutorials = (function () {

    var sortTutorials = function(){
        $("#SortVideos").sortable({
            start: function( event, ui ) {
                $('#SortVideos').addClass('cnt-videos-dragged');
            },
            stop: function( event, ui ) {
                $('#SortVideos').removeClass('cnt-videos-dragged');
                var order = 1;
                var data = {};
                $('.item-video').each(function(){
                    var id_tmp = $(this).data('id');
                    data[id_tmp]= order;
                    order++;
                });
                var url = $('#SortVideos').data('url-sort');
                PeticionAjax.post(url,data);
            }
        });
    };

    var loadMoreTutorials = function(){
        var executed = false;
        $(window).on('scroll',function () {
            if(!executed){
                var distanceFromBottom = Math.floor($(document).height() - $(document).scrollTop() - $(window).height());

                var $id_videos = $('#SortVideos');

                if (distanceFromBottom < 200) {
                    $id_videos.data('page', Number($id_videos.data('page')) + 1 );
                    var url = $id_videos.data('url');
                    var data = {};
                    data.page = $id_videos.data('page');
                    var request = PeticionAjax.get(url, data);
                    request.done(function (data) {
                        $id_videos.append(data);
                        if(!$('.page-load-status').length){
                            executed = false;
                        }
                    });
                    executed = true;
                }
            }
        });
    };

    var deleteTutorial = function() {
        $('.delete-tutorial-js').click(function(e) {
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
        load: function () {
            sortTutorials();
            loadMoreTutorials();
            deleteTutorial();
        }
    }
})();