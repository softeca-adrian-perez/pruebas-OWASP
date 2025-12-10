$(document).ready(function(){
    Alert.load();
});

var Alert = (function(){

    var checkAlert = function(){
        $(".check-alert-js").off('click').on('click',function(event){
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;

            swal({
                title: $(this).data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function(result){
                if(result.value){
                    swalCheckAlert(element, urlHref);
                }
            });
        });
    };

    var uncheckAlert = function(){
        $(".uncheck-alert-js").off('click').on('click',function(event){
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;
            swal({
                title: $(this).data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function(result){
                if(result.value) {
                    swalUncheckAlert(element, urlHref);

                }
            })
        });
    };

    var clickLinkAlert = function(){
        $(".link-js").off('click').on('click',function(event){
            event.preventDefault();

            var element = $(this);
            var url = element.data('url-link-js');
            var data = {};
            data.alert_id = element.data('alert-id');
            var request = PeticionAjax.post(url,data);
        });
    };

    var readAllMessages = function () {
        $(".read-all-messages").off('click').on('click', function (e) {
            e.preventDefault();

            swal({
                title: $.i18n._('Alert.Mark_all_as_read_msg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var check_alerts = $('.check-alert-js');
                    check_alerts.each(function () {
                        var element = $(this);
                        var urlHref = this.href;
                        swalCheckAlert(element, urlHref);
                    });
                }
            });
        });

        $(".unread-all-messages").off('click').on('click', function (e) {
            e.preventDefault();

            swal({
                title: $.i18n._('Alert.Mark_all_as_unread_msg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var check_alerts = $('.uncheck-alert-js');
                    check_alerts.each(function () {
                        var element = $(this);
                        var urlHref = this.href;
                        swalUncheckAlert(element, urlHref);
                    });
                }
            });
        });
    };

    var swalCheckAlert = function(element, urlHref){
        var request = PeticionAjax.get(urlHref);
        request.done(function() {
            element.find('span').removeClass('icon-tick_off');
            element.find('span').removeClass('c-fallo');
            element.find('span').addClass('icon-tick_on');
            element.find('span').addClass('c-exito');
            element.data('confirmmsg', $('#confirm-uncheck').data('msg'));
            element.removeClass('check-alert-js');
            element.addClass('uncheck-alert-js');
            $(".uncheck-alert-js").unbind();
            $('#count-alerts').html(Number($('#count-alerts').html()) - 1);
            element.attr("href", $('#confirm-uncheck').data('url') + '/' + element.data('id'));
            uncheckAlert();
        });
    };

    var swalUncheckAlert = function(element, urlHref){
        var request = PeticionAjax.get(urlHref);
        request.done(function () {
            element.find('span').removeClass('icon-tick_on');
            element.find('span').removeClass('c-exito');
            element.find('span').addClass('icon-tick_off');
            element.find('span').addClass('c-fallo');
            element.data('confirmmsg', $('#confirm-check').data('msg'));
            element.removeClass('uncheck-alert-js');
            element.addClass('check-alert-js');
            $(".check-alert-js").unbind();
            $('#count-alerts').html(Number($('#count-alerts').html()) + 1);
            element.attr("href", $('#confirm-check').data('url') + '/' + element.data('id'));
            checkAlert();
        });
    };

    var checkAlertBeforeLeave = function(){
        $('.link-alert-js').on('click',function(e){
            e.preventDefault();
            var url_alert = this.href;
            var tr_parent = $(this).closest('tr');
            if( tr_parent.find('.check-alert-js').length){
                var urlHref = tr_parent.find('.check-alert-js').attr('href');
                var request = PeticionAjax.get(urlHref);
                    request.done(function() {
                        window.location.href = url_alert;
                    });
            } else {
                window.location.href = url_alert;
            }
        });
    }

    return {
        load: function(){
            checkAlert();
            uncheckAlert();
            readAllMessages();
            clickLinkAlert();
            checkAlertBeforeLeave();
        }
    }
})();