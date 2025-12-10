$(document).ready(function () {
    Email.load();
});

var Email = (function () {
    var resendEmail = function () {
        $("#resend-email").off('click').on('click', function (event) {
            event.preventDefault();
            var element = $("#resend-email");
            var urlHref = element.data('url-href');
            var url = element.data('url');

            swal({
                title: $.i18n._('Email.Resend_email?'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    PeticionAjax.mostrarCargando();
                    var request = PeticionAjax.post(urlHref);
                    request.done(function (data) {
                        window.location.href = url;
                        PeticionAjax.ocultarCargando();
                        resendEmail();
                    });
                }
            });
        });
    };

    return {
        load: function () {
            resendEmail();
        }
    }
})();