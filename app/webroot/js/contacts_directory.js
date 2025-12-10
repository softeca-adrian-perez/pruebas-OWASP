$(document).ready(function(){
    ContactsDirectory.load();
});

var ContactsDirectory = function() {

    var loadContactsDirectory = function () {
         $('.position').on('click',function(){
            var url = $(this).data('url');
            var div = $(this).data('div');
            data = {};
            search = {};
            data.position_id = $(this).attr('data-id');
            
            search.first_name = $('#first_name-js').val();
            search.last_name = $('#last_name-js').val();
            search.position = $('#associations').val();
            search.email = $('#email-js').val();

            data.searcher = search;

            $('.position').removeClass('select-link-contact-directory');
            $('#'+data.position_id).addClass('select-link-contact-directory');
            var request = PeticionAjax.post( url, data );
            request.done(function (data) {
                $(div).html(data);
            });

         });
    };

    return {
        load: function(){
            loadContactsDirectory();
        }
    }
}();
