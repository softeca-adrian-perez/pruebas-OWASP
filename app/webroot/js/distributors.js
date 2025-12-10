$(document).ready(function(){
    Distributors.load();
});

var Distributors = (function(){

    var searchTime = function(){
        var timer;

        $("#name-distributors").keyup(function(e){
            clearTimeout(timer);
            timer = setTimeout(function search(){
                searchDistributor();
            }, 750);
        });
    };

    var searchDistributor = function(){
        var name_distributor = 'name=' + $('#name-distributors').val();
        var account_number = 'account_number=' + $('#account-number').val();
        // var mamid = 'MAMID=' + $('#mamid').val();
        var vat_number = 'VAT_number=' + $('#vat-number').val();
        // var reg_number = 'reg_number=' + $('#reg-number').val();
        var trading_group_id = 'trading_group_id=' + $('#trading-groups').val();
        // var association_id = 'association_type_id=' + $('#associations').val();
        var town = 'town=' + $('#town').val();

        var head_office = "";
        if($('#head_office').length != 0){
            head_office = $('#head_office').prop("checked") ? 'head_office=0&head_office=1' : 'head_office=0';
        } else {
            head_office = 'head_office=' + $('#profile').val();
        }

        var url = name_distributor + '&' + account_number +  '&' + vat_number + '&' + trading_group_id + '&' + town + '&' + head_office;
        if( $('#bdm-id').val() != undefined){
            var bdm_id = 'bdm_id=' + $('#bdm-id').val();
            url += '&' + bdm_id;
        }

        var request = PeticionAjax.post($('#name-distributors').data('url') + '?' + url);
        request.done(function(data){
            $('#ajax_search_home').html(data);
            if($('#total_paginator').html() != undefined ){
                $('#counter-total').html($('#total_paginator').html());
            } else {
                $('#counter-total').html($('table tbody tr').length);
            }

            $('tr.link-js td').click(function(){
                if(!$(this).hasClass('no-link-js')){
                    window.location = $(this).parent('tr').data('url');
                }
            });
        });
    };

    var viewMore = function(){
        $('#btn_load_mode').on('click',function(){
            var $this = $(this);
            var url = $(this).data('url');
            var data = {};
            data.page = $(this).data('page');
            data.distributor_id = $('#distributor_id').val();
            data.back = $(this).data('back');
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $('#table_garages_associated').append(data);
                $this.data('page', $this.data('page') + 1);
                if(Number($('#total_garages_distributors').text()) == $('#table_garages_associated').find('tr').length){
                    $this.hide();
                }
                Tools.loadTr();
            });
        });
    };

    var checkEmailLanguage = function(){
        $('#form-distributors').submit(function(e){
            if ($('#distributor_email').val() != '' && $('#distributor_language_select').val() == ''){
                e.preventDefault();
                swal({
                    title: $.i18n._('Garage.Mandatory_to_select_language'),
                    type: "error"
                }).then(function (result) {
                });
            }
            if ($('#distributor_email').val() == '' && $('#distributor_language_select').val() != ''){
                e.preventDefault();
                swal({
                    title: $.i18n._('Garage.Empty_email'),
                    type: "error"
                }).then(function (result) {
                });
            }
        });

        var label_language = $("label[for='distributor_language_select']").text();
        var label_email = $("label[for='distributor_email']").text();

        if($('#distributor_email').val() != '' && !$("label[for='distributor_language_select']").text().includes('*')){
            $("label[for='distributor_language_select']").append('<span style="color:red"> *</span>');
        } else if($('#distributor_email').val() == ''){
            $("label[for='distributor_language_select']").html(label_language);
        }

        $('#distributor_email').on('input change', function(){
            if($('#distributor_email').val() != '' && !$("label[for='distributor_language_select']").text().includes('*')){
                $("label[for='distributor_language_select']").append('<span style="color:red"> *</span>');
            } else if($('#distributor_email').val() == ''){
                $("label[for='distributor_language_select']").html(label_language);
            }
        });

        if($('#distributor_language_select').val() != '' && !$("label[for='distributor_email']").text().includes('*')){
            $("label[for='distributor_email']").append('<span style="color:red"> *</span>');
        } else if($('#distributor_language_select').val() == ''){
            $("label[for='distributor_email']").html(label_email);
        }

        $('#distributor_language_select').on('change', function(){
            if($('#distributor_language_select').val() != '' && !$("label[for='distributor_email']").text().includes('*')){
                $("label[for='distributor_email']").append('<span style="color:red"> *</span>');
            } else if($('#distributor_language_select').val() == ''){
                $("label[for='distributor_email']").html(label_email);
            }
        });
    }

    var search_distributors_garages_associated = function(){
        $('#network_id').on('change', function(){
            var network_id = 'network_id=' + $('#network_id').val();
            var network_status = 'status=' + $('#status_id').val();
            var url = network_id + '&' + network_status;
            var request = PeticionAjax.post($('#network_id').data('url') + '?' + url);
            request.done(function(data){
                $('#ajax_search_distributors_garages_associated').html(data);
                if($('#total_paginator').html() != undefined ){
                    $('#counter-total').html($('#total_paginator').html());
                } else {
                    $('#counter-total').html($('table tbody tr').length);
                }

                $('tr.link-js td').click(function(){
                    if(!$(this).hasClass('no-link-js')){
                        window.location = $(this).parent('tr').data('url');
                    }
                });
            });
        });

        $('#status_id').on('change', function(){
            var network_id = 'network_id=' + $('#network_id').val();
            var network_status = 'status=' + $('#status_id').val();
            var url = network_id + '&' + network_status;
            var request = PeticionAjax.post($('#network_id').data('url') + '?' + url);
            request.done(function(data){
                $('#ajax_search_distributors_garages_associated').html(data);
                if($('#total_paginator').html() != undefined ){
                    $('#counter-total').html($('#total_paginator').html());
                } else {
                    $('#counter-total').html($('table tbody tr').length);
                }

                $('tr.link-js td').click(function(){
                    if(!$(this).hasClass('no-link-js')){
                        window.location = $(this).parent('tr').data('url');
                    }
                });
            });
        });
    };

    return {
        load: function(){
            searchTime();
            viewMore();
            checkEmailLanguage();
            search_distributors_garages_associated();
        }
    }

})();