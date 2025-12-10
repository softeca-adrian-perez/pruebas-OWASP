$(document).ready(function () {
    TrainingsTrainers.load()
});

var TrainingsTrainers = (function () {

    var deleteTrainer = function () {
        $(".delete-trainer").on('click', function () {
            let url = $(this).data('delete-url');
            swal({
                title: $.i18n._('Training.Training_trainer_delete?'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url);
                    request.done(function (result) {
                        if(result) {
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
            deleteTrainer();
        }
    }

})();
