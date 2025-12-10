$(document).ready(function () {
    CommunicationMaintenance.load();
});

var CommunicationMaintenance = (function () {

    var deleteCommunicationSection = function(){

        $(".delete-communication_sections-js").off('click').on('click',function(e){
            e.preventDefault();
            var element = $(this);

            swal({
                title: element.data('confirmmsg'),
                // text: element.data('confirmmsg_text'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: element.data('yes'),
                cancelButtonText: element.data('no')
            }).then(function (result) {
                if (result.value) {

                    var url = element.data('url_delete');
                    var url_redirect = element.data('url_redirect');
                    var data = {}
                    data.section_id = element.data('section_id');

                    var request = PeticionAjax.post(url, data);
                    request.done(function(data) {
                        if(data != 'error'){
                            swal({
                                title: $.i18n._('Constants.Message_well_deleted'),
                                type: "success"
                            }).then(function (result) {
                                window.location.replace(url_redirect);
                            });
                        }else{
                            swal({
                                title: $.i18n._('Constants.Category_are_associated'),
                                type: "error"
                            });
                        }
                        deleteCommunicationSection();
                    });

                }
            });
        });
    }

    var deleteCommunicationSubsection = function(){

        $(".delete-communication_subsections-js").off('click').on('click',function(e){
            e.preventDefault();
            var element = $(this);

            swal({
                title: element.data('confirmmsg'),
                // text: element.data('confirmmsg_text'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: element.data('yes'),
                cancelButtonText: element.data('no')
            }).then(function (result) {
                if (result.value) {

                    var url = element.data('url_delete');
                    var url_redirect = element.data('url_redirect');
                    var data = {}
                    data.subsection_id = element.data('subsection_id');

                    var request = PeticionAjax.post(url, data);
                    request.done(function(data) {
                        if(data != 'error'){
                            swal({
                                title: $.i18n._('Constants.Message_well_deleted'),
                                type: "success"
                            }).then(function (result) {
                                window.location.replace(url_redirect);
                            });
                        }else{
                            swal({
                                title: $.i18n._('Constants.Subcategory_are_associated'),
                                type: "error"
                            });
                        }
                        deleteCommunicationSubsection();
                    });

                }
            });
        });
    }

    var deleteCommunication = function(){

        $(".delete-communication-js").off('click').on('click',function(e){
            e.preventDefault();
            var element = $(this);

            swal({
                title: element.data('confirmmsg'),
                // text: element.data('confirmmsg_text'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: element.data('yes'),
                cancelButtonText: element.data('no')
            }).then(function (result) {
                if (result.value) {

                    var url = element.data('url_delete');
                    var url_redirect = element.data('url_redirect');
                    var data = {}
                    data.communication_id = element.data('communication_id');

                    var request = PeticionAjax.post(url, data);
                    request.done(function(data) {
                        if(data != 'error'){
                            swal({
                                title: $.i18n._('Constants.Message_well_deleted'),
                                type: "success",
                            }).then(function (result) {
                                window.location.replace(url_redirect);
                            });
                        }else{
                            swal({
                                title: $.i18n._('Constants.Message_bad_deleted'),
                                type: "error",
                            });
                        }
                        deleteCommunication();
                    });

                }
            });
        });
    }

    return {
        load: function () {
            deleteCommunicationSection();
            deleteCommunicationSubsection();
            deleteCommunication();
        }
    }
})();