$(document).ready(function () {
    DistributorsDistributorsNetworks.load();
});

var DistributorsDistributorsNetworks = (function () {

    var leavingReasons = function () {
        $(document).on("change", "#select-status", function () {
            let status_id = ($('#select-status option:selected').val());
            if (status_id == 7 || status_id == 8) {
                $('#reason-leaving').attr("style", "display: inline;");
            } else {
                $('#reason-leaving').attr("style", "display: none;");
            }
        });

        $("#btn-guardar").on('click', function (event) {
            event.preventDefault();
            if (($("#select-status").val() == 7 || $("#select-status").val() == 8) && !$("#select-reason-leaving").val()) {
                swal({
                    title: $.i18n._('Config.Empty_reason'),
                    type: "error"
                }).then(function (result) {
                });
            } else {
                $("#form-distributors-distributors-networks").submit();
            }
        });
    }

    var loadTradingGroupSelectNetwork = function () {
        $('.select-network-js').change(function (event) {
            event.preventDefault();
            var url = $(this).data('url');
            var div = $(this).data('div_trading_groups');
            var trading_group_field_name = $(this).data('trading_group_field_name');

            var network_id = $(this).val();

            if (network_id != null && network_id.length !== 0) {
                data = {};
                data.network_id = network_id;
                data.trading_group_field_name = trading_group_field_name;

                PeticionAjax.mostrarCargando();
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    PeticionAjax.ocultarCargando();
                    $(div).html(data);
                    Select2.load();
                });
            } else {
                return false;
            }
        });
    };

    return {
        load: function () {
            leavingReasons();
            loadTradingGroupSelectNetwork();
        }
    }

})();