$(document).ready(function(){
    Branches.load();
});

var Branches = (function(){

    var searchTime = function(){
        var timer;

        $("#name-distributors").keyup(function(e){
            clearTimeout(timer);
            timer = setTimeout(function search(){
                searchBranches();
            }, 750);
        });
    };

    var searchBranches = function(){

        var name_distributor = 'name=' + $('#name-distributors').val();
        var account_number = 'account_number=' + $('#account-number').val();
        var vat_number = 'VAT_number=' + $('#vat-number').val();
        // var trading_group_id = 'trading_group_id=' + $('#trading-groups').val();

        // var subsidiary = "";

        // if($('#subsidiary').length != 0){
        //     if($('#subsidiary').prop("checked")){
        //         subsidiary = 'subsidiary=0&subsidiary=1';
        //     } else {
        //         subsidiary = 'subsidiary=0';
        //     }
        // } else {
        //     subsidiary = 'subsidiary=' + $('#type').val();
        // }


        if(name_distributor == ""){
            name_distributor = 'all';
        }

        var url = name_distributor + '&' + account_number +  '&' + vat_number;

        var request = PeticionAjax.post($('#name-distributors').data('url') + '?' + url);
        request.done(function(data){
            $('#ajax_search_home_branches').html(data);
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