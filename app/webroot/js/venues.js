$(document).ready(function () {
    Venues.load()
});

var Venues = (function () {

    var deleteVenue = function () {
        $(".delete-venue").on('click', function () {
            let url = $(this).data('delete-url');
            swal({
                title: $.i18n._('Venue.Delete_venue?'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url);
                    request.done(function (result) {
                        if (result == 2) {
							swal({
								title: $.i18n._("Venue.Cannot_delete"),
								type: "error",
							}).then(function (result) {
								location.reload();
							});
						}else if(result) {
							swal({
								title: $.i18n._("Constants.Message_well_deleted"),
								type: "success",
							}).then(function (result) {
								location.reload();
							});
						} else {
                            swal({
                                title: $.i18n._('Constants.Message_bad_deleted'),
                                type: "error"
                            });
                        }
                    });
                    request.fail(function () {
                        swal({
                            title: $.i18n._('Constants.Message_bad_deleted'),
                            type: "error"
                        });
                    });
                };
            });
        });
    };


    return {
        load: function () {
            deleteVenue();
        }
    }

})();
