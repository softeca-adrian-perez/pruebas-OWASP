$(document).ready(function () {
    Conferences.load()
});

var Conferences = (function () {

    var deleteConference = function () {
        $(".delete-conference").on('click', function () {
            let url = $(this).data('delete-url');
            swal({
                title: $.i18n._('Conference.Delete_conference?'),
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
								title: $.i18n._("Conference.Message_assoc_delete"),
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

    var toggleStatus = function () {
        $(".ico-toggle").on('click', function () {
            let url = $(this).data('url');
            let item = $(this);
            let alertMessage = null;
            if ($(this).hasClass('c-fallo')) {
                alertMessage = $.i18n._('Conference.Status_toggle_activate?');
            } else {
                alertMessage = $.i18n._('Conference.Status_toggle_deactivcate?');
            }
            swal({
                title: alertMessage,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
            var request = PeticionAjax.post(url);
            request.done(function (result) {
                if (result) {
                    toggleStatusIcon(item);
                } else {
                    swal({
                        title: $.i18n._('Constants.Error_alert_general'),
                        type: "error"
                    });
                }
            });
            request.fail(function () {
                swal({
                    title: $.i18n._('Constants.Error_alert_general'),
                    type: "error"
                });
            });
                }
            });
        });
    };

    var toggleStatusIcon = function (item_id) {
        if ($(item_id).hasClass('c-fallo')) {
            $(item_id).removeClass('c-fallo ion-toggle');
            $(item_id).addClass('c-exito ion-toggle-filled');
        } else {
            $(item_id).removeClass('c-exito ion-toggle-filled');
            $(item_id).addClass('c-fallo ion-toggle');
        }
    }

    return {
        load: function () {
            deleteConference();
            toggleStatus();
        }
    }

})();
