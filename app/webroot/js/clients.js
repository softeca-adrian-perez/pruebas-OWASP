$(document).ready(function(){
    Clients.load();
});

var Clients = (function(){

    var searchTime = function(){
        var timer;

        //$('#name-garages').children('.select2-search__field').keyup(function(e){
        $('.select2-search__field').keyup(function(e){
            clearTimeout(timer);
            timer = setTimeout(function search(){
                searchContract();
            }, 750);
        });
    };

    var searchContract = function(){
        var name_garage = 'name=' + $('#name-garages').val();
        var trading_group_id = 'trading_group_id=' + $('#trading-group-id').val();
        var network_id = 'network_id=' + $('#network-id').val();
        let g_number_id = $('#g_number_id').val() == undefined ? '' : 'g_number_id=' + $('#g_number_id').val();
        let city_id = $('#city-id').val() == undefined ? '' : 'city_id=' + $('#city-id').val();
        var bdm_id = 'bdm_id=' + $('#bdm-id').val();
        var postcode = 'postcode=' + $('#postcode-id').val();
        var service_id = 'service_id=' + $('#service-id').val();
        let distributor_id = $('#distributor-id-js').val() == undefined ? '' :'distributor_id=' + $('#distributor-id-js').val();
        var town = 'town=' + $('#town-id').val();
        var ramps = 'ramps=' + $('#ramps-id').val();
        var MOT_bays = 'MOT_bays=' + $('#MOT-bays-id').val();
        var phone = 'phone=' + $('#phone-id').val();
        var vehicle_type_id = 'vehicle_type_id=' + $('#vehicle-type-id').val();
        var foundation_year = 'foundation_year=' + $('#foundation-year-id').val();
        var lead_source_id = 'lead_source=' + $('#lead_source_id').val();
        if($('#my_customers').prop('checked')){
            var my_customer = 'search_my_customers=1';
        }
        else{
            var my_customer = 'search_my_customers=0';
        }
        let erp_id = $('#erp_id-id').val() == undefined ? '' : 'erp_id=' + $('#erp_id-id').val()
        var ref_code = $('#ref-code-id').val() == undefined ? '' : 'ref_code=' + $('#ref-code-id').val();

        var url = name_garage + '&'  + trading_group_id + '&' + network_id + '&' + city_id + '&' + g_number_id + '&' + postcode + '&' + service_id + '&' + distributor_id + '&' + town + '&' + ramps + '&' + MOT_bays + '&' + phone + '&' + vehicle_type_id + '&' + foundation_year + '&' + lead_source_id + '&' + ref_code + '&' + my_customer+ '&' + erp_id;

        url += '&' + bdm_id;

        var request = PeticionAjax.post($('#name-garages').data('url') + '?' + url);
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

    return {
        load: function(){
            searchTime();
        }
    }

})();