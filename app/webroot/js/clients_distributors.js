$(document).ready(function(){
    Clients.load();
});

var Clients = (function(){

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

        if($('#my_customers').prop('checked')){
            var my_customer = 'search_my_customers=1';
        }
        else{
            var my_customer = 'search_my_customers=0';
        }

        var head_office = "";
        if($('#head_office').length != 0){
            head_office = $('#head_office').prop("checked") ? 'head_office=0&head_office=1' : 'head_office=0';
        } else {
            head_office = 'head_office=' + $('#profile').val();
        }

        var url = name_distributor + '&' + account_number +  '&' + vat_number + '&' + trading_group_id + '&' + town + '&' + head_office + '&' + my_customer;
        if( $('#bdm-id').val() != undefined){
            var bdm_id = 'bdm_id=' + $('#bdm-id').val();
            url += '&' + bdm_id;
        }

        var request = PeticionAjax.post($('#name-distributors').data('url') + '?' + url);
        request.done(function(data){
            $('#ajax_search_distributors').html(data);
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

    return {
        load: function(){
            searchTime();
        }
    }

})();