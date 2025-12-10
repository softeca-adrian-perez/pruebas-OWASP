$(document).ready(function(){
    myTableArrayLog = [];
    Position.load();
    ConstantsPositionConfigType_GARAGE = 1;
    ConstantsPositionConfigType_DISTRIBUTOR = 2;
    ConstantsPositionConfigType_DEFAULT_TYPE = 3;
    ConstantsPositionConfigType_COMMUNICATION_SSO = 4;
    ConstantsPositionConfigType_SUPPLIER_CATEGORY = 5;
});

var Position = (function(){

    var selectType = function(){
        $('#cnt_networks, #cnt_regions, #cnt_trading_groups, #cnt_distributor_networks').addClass('d-none');
        $('#position_config_type').on('change',function(){
            if($(this).val() == ''){
                $('#cnt_networks, #cnt_regions, #cnt_trading_groups, #cnt_distributor_networks, #cnt_bdms, #ctn_new_permission_group-js, #cnt_aag_members, #cnt_customer_activities, #cnt_profiles, #cnt_suppliers_categories').addClass('d-none');
            } else if($(this).val() == ConstantsPositionConfigType_GARAGE ){
                $('#cnt_networks, #cnt_regions, #cnt_bdms').removeClass('d-none');
                $('#cnt_trading_groups, #cnt_distributor_networks, #cnt_customer_activities, #cnt_profiles, #cnt_suppliers_categories').addClass('d-none');
            } else if($(this).val() == ConstantsPositionConfigType_DISTRIBUTOR){
                $('#cnt_trading_groups, #cnt_bdms').removeClass('d-none');
                $('#cnt_networks, #cnt_regions, #cnt_distributor_networks, #cnt_customer_activities, #cnt_profiles, #cnt_suppliers_categories').addClass('d-none');
            } else if($(this).val() == ConstantsPositionConfigType_DEFAULT_TYPE){
                $('#cnt_networks, #cnt_regions, #cnt_trading_groups, #cnt_distributor_networks, #cnt_bdms, #cnt_customer_activities, #cnt_profiles, #cnt_aag_members, #cnt_suppliers_categories').addClass('d-none');
            } else if($(this).val() == ConstantsPositionConfigType_COMMUNICATION_SSO){
                $('#cnt_trading_groups, #cnt_distributor_networks, #cnt_networks, #cnt_aag_members, #cnt_customer_activities, #cnt_profiles').removeClass('d-none');
                $('#cnt_regions, #cnt_bdms, #cnt_categories').addClass('d-none');
            } else if($(this).val() == ConstantsPositionConfigType_SUPPLIER_CATEGORY){
                $('#cnt_suppliers_categories').removeClass('d-none');
                $('#cnt_regions, #cnt_bdms, #cnt_trading_groups, #cnt_distributor_networks, #cnt_networks, #cnt_aag_members, #cnt_customer_activities, #cnt_profiles').addClass('d-none');
            }

            var counter = 0;
            $('.filter_config').each(function(){
                if( !$(this).hasClass('d-none') ){
                    if( !(counter % 4) && counter ) {
                        $(this).addClass('clear');
                    }
                    counter++;
                } else {
                    $(this).removeClass('clear')
                }
            });
            
            if($(this).val() != ''){
                $('#ctn_new_permission_group-js').removeClass('d-none');
            }
            clearFields();
        });
    };

    var addPosition = function(){

        $('#button_add').on('click',function(e){
            e.preventDefault();
            if( $('#position_config_type').val() == ''){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_position_config_type'), 'error');
            }else if($('#permissions').val() == ''){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_permission_group'), 'error');
            }else if(
                $('#permissions').val() != '' && 
                (
                    $('#input_networks').val() != null ||
                    $('#input_regions').val() != null || 
                    $('#input_trading_groups').val() != null || 
                    $('#input_distributor_networks').val() != null || 
                    $('#input_aag_members').val() != null || 
                    $('#input_profiles').val() != null ||
                    $('#input_customer_activities').val() != null ||
                    $('#input_suppliers_categories').val() != null
                )
                ||
                $('#position_config_type').val() == ConstantsPositionConfigType_DEFAULT_TYPE && $('#permissions').val() != ''
            ){
                var networks_select = $("#input_networks").select2('data');
                var networks_value = '';
                var networks_tmp = [];
                networks_select.forEach(function(ele){
                    networks_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    networks_tmp.push(ele.id);
                });
                networks[positions_length] = networks_tmp;
                
                var regions_select = $("#input_regions").select2('data');
                var regions_value = '';
                var regions_tmp = [];
                regions_select.forEach(function(ele){
                    regions_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    regions_tmp.push(ele.id);
                });
                regions[positions_length] = regions_tmp;

                var trading_groups_select = $("#input_trading_groups").select2('data');
                var trading_groups_value = '';
                var trading_groups_tmp = [];
                trading_groups_select.forEach(function(ele){ 
                    trading_groups_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    trading_groups_tmp.push(ele.id);
                });
                trading_groups[positions_length] = trading_groups_tmp;

                var distributor_networks_select = $("#input_distributor_networks").select2('data');
                var distributor_networks_value = '';
                var distributor_networks_tmp = [];
                distributor_networks_select.forEach(function(ele){
                    distributor_networks_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    distributor_networks_tmp.push(ele.id);
                });
                distributor_networks[positions_length] = distributor_networks_tmp;
                
                var aag_members_select = $("#input_aag_members").select2('data');
                var aag_members_value = '';
                var aag_members_tmp = [];
                aag_members_select.forEach(function(ele){
                    aag_members_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    aag_members_tmp.push(ele.id);
                });
                aag_members[positions_length] = aag_members_tmp;
                
                var profiles_select = $("#input_profiles").select2('data');
                var profiles_value = '';
                var profiles_tmp = [];
                profiles_select.forEach(function(ele){
                    profiles_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    profiles_tmp.push(ele.id);
                });
                profiles[positions_length] = profiles_tmp;             

                var customer_activities_select = $("#input_customer_activities").select2('data');
                var customer_activities_value = '';
                var customer_activities_tmp = [];
                customer_activities_select.forEach(function(ele){
                    customer_activities_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    customer_activities_tmp.push(ele.id);
                });
                customer_activities[positions_length] = customer_activities_tmp;

                var suppliers_categories_select = $("#input_suppliers_categories").select2('data');
                var suppliers_categories_value = '';
                var suppliers_categories_tmp = [];
                suppliers_categories_select.forEach(function(ele){
                    suppliers_categories_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    suppliers_categories_tmp.push(ele.id);
                });
                suppliers_categories[positions_length] = suppliers_categories_tmp;

                var bdms_select = $("#input_bdms").select2('data');
                var bdms_value = '';
                var bdms_tmp = [];
                bdms_select.forEach(function(ele){ 
                    bdms_value += '<span data-id="' + ele.id + '">' + ele.text + '</span>' + '<br>';
                    bdms_tmp.push(ele.id);
                });
                bdms[positions_length] = bdms_tmp;


                group_permissions[positions_length] = $('#permissions').val();
                position_types[positions_length] = $('#position_config_type').val();
                
                $('#table_position').append(
                    "<tr data-row='" + positions_length +"'>" +
                        "<td class='ta-center position_type_select'>" + $("#position_config_type option:selected").text() + "</td>" + 
                        "<td class='ta-center network_select'>" + networks_value + "</td>" + 
                        "<td class='ta-center region_select'>" + regions_value + "</td>" + 
                        "<td class='ta-center trading_group_select'>" + trading_groups_value + "</td>" + 
                        "<td class='ta-center distributor_network_select'>" + distributor_networks_value + "</td>" + 
                        "<td class='ta-center aag_member_select'>" + aag_members_value + "</td>" + 
                        "<td class='ta-center profile_select'>" + profiles_value + "</td>" + 
                        "<td class='ta-center customer_activity_select'>" + customer_activities_value + "</td>" + 
                        "<td class='ta-center bdm_select'>" + bdms_value + "</td>" + 
                        "<td class='ta-center supplier_category_select'>" + suppliers_categories_value + "</td>" + 
                        "<td class='ta-center permission_select'><span data-id='" + $("#permissions").val() + "'>" + $("#permissions option:selected").text() + "</span></td>" + 
                        "<td class='ta-center'>" +
                            "<span class='ion-android-cancel cursor-pointer c-fallo delete-row'></span>" +
                        "</td>" +
                    "</tr>"
                );

                $('.table-tracking').basictable('destroy');
                $('.table-tracking').basictable();
                clearFields();
                deleteRow();
                positions_length++;
            }
        });
    };

    var clearFields = function(){
        $('.clear_field').each(function(){
            $(this).val('').trigger('change');
        });
    };

    var safeSave = function(){
        $('#btn-guardar').on('click',function(e){
            e.preventDefault();
            if($('.ion-asterisk').parent().prev().val() == ''){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_name'), 'error');

            } else if( $('#position_role').val() == '' ){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_role'), 'error');
            } else if( $('#position_config_type').val() == '' && ( !Object.keys(group_permissions).length || ( !Object.keys(networks).length || !Object.keys(regions).length ) )){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_config_type'), 'error');
            } else if( $('#position_config_type').val() == ConstantsPositionConfigType_GARAGE && ( !Object.keys(group_permissions).length || ( !Object.keys(networks).length || !Object.keys(regions).length ) )){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_garage'), 'error');

            } else if( $('#position_config_type').val() == ConstantsPositionConfigType_DISTRIBUTOR && ( !Object.keys(group_permissions).length || ( !Object.keys(networks).length || !Object.keys(regions).length ) )){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_distributor'), 'error');

            } else if( $('#position_config_type').val() == ConstantsPositionConfigType_DEFAULT_TYPE && ( !Object.keys(group_permissions).length || ( !Object.keys(networks).length || !Object.keys(regions).length ) )){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_default'), 'error');
            } else if( $('#position_config_type').val() == ConstantsPositionConfigType_COMMUNICATION_SSO && ( !Object.keys(group_permissions).length || ( 
                !Object.keys(networks).length || 
                !Object.keys(trading_groups).length ||
                !Object.keys(distributor_networks).length ||
                !Object.keys(aag_members).length ||
                !Object.keys(profiles).length || 
                !Object.keys(customer_activities).length
            ) )){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_communication'), 'error');
            } else if( $('#position_config_type').val() == ConstantsPositionConfigType_SUPPLIER_CATEGORY && ( !Object.keys(group_permissions).length || (!Object.keys(suppliers_categories).length) )){
                swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_category'), 'error');
            } else {
                var myForm = document.getElementById('position-form');
                
                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][networks]';
                hiddenInput_tmp.value = JSON.stringify(networks);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][regions]';
                hiddenInput_tmp.value = JSON.stringify(regions);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][trading_groups]';
                hiddenInput_tmp.value = JSON.stringify(trading_groups);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][distributor_networks]';
                hiddenInput_tmp.value = JSON.stringify(distributor_networks);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][aag_members]';
                hiddenInput_tmp.value = JSON.stringify(aag_members);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][profiles]';
                hiddenInput_tmp.value = JSON.stringify(profiles);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][customer_activities]';
                hiddenInput_tmp.value = JSON.stringify(customer_activities);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][suppliers_categories]';
                hiddenInput_tmp.value = JSON.stringify(suppliers_categories);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][bdms]';
                hiddenInput_tmp.value = JSON.stringify(bdms);
                myForm.appendChild(hiddenInput_tmp);

                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][group_permissions]';
                if(Object.keys(group_permissions).length == 0){
                    group_permissions[0] = $('#permissions_default').val();
                }
                hiddenInput_tmp.value = JSON.stringify(group_permissions);
                myForm.appendChild(hiddenInput_tmp);
                
                var hiddenInput_tmp = document.createElement('input');
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[Position][position_types]';
                hiddenInput_tmp.value = JSON.stringify(position_types);
                myForm.appendChild(hiddenInput_tmp);

                var myTableArray = [];
                var myTableHeaderArray = [];
                $("table #table_position_header").each(function() {
                    var arrayOfThisRow = [];
                    var tableData = $(this).find('th');
                    tableData.each(function() {
                        arrayOfThisRow.push($(this).text().trim()); 
                    });
                    myTableHeaderArray.push(arrayOfThisRow);
                });

                $("table #table_position tr").each(function() {
                    var tableData = $(this).find('td');
                    if (tableData.length > 0) {
                        rowData = '';
                        tableData.each(function(key) {
                            if($(this).text().trim() != ''){
                                rowData += myTableHeaderArray[0][key] + ': ' + $(this).text().trim() + ' / '
                            }
                        });
                        myTableArray.push(rowData);
                    }
                });

                if(!arraysEqual(myTableArrayLog, myTableArray)){
                    var hiddenInput_tmp = document.createElement('input');
                    hiddenInput_tmp.type = 'hidden';
                    hiddenInput_tmp.name = 'data[Position][logs]';
                    hiddenInput_tmp.value = JSON.stringify(myTableArray);
                    myForm.appendChild(hiddenInput_tmp);
                }
                
                $('#btn-guardar').off().trigger('click');
            }
        });
    };

    var deleteRow = function(){
        $('.delete-row').off('click').on('click',function(){
            var parent_tr = $(this).parents().eq(2);
            var network_select = parent_tr.find('td.network_select');
            var region_select = parent_tr.find('td.region_select');
            var permission_select = parent_tr.find('td.permission_select');
            var trading_group_select = parent_tr.find('td.trading_group_select');
            var distributor_network_select = parent_tr.find('td.distributor_network_select');
            var aag_members_select = parent_tr.find('td.aag_member_select');
            var profiles_select = parent_tr.find('td.profile_select');
            var customer_activities_select = parent_tr.find('td.customer_activity_select');
            var suppliers_categories_select = parent_tr.find('td.supplier_category_select');
            var bdm_select = parent_tr.find('td.bdm_select');

            if(network_select.length){
                network_select.find('span').each(function(){
                });
                delete networks[parent_tr.data('row')]; 
            }
            if(region_select.length){
                region_select.find('span').each(function(){
                });
                delete regions[parent_tr.data('row')]; 
            }
            if(permission_select.length){
                permission_select.find('span').each(function(){
                });
                delete group_permissions[parent_tr.data('row')]; 
            }
            if(trading_group_select.length){
                trading_group_select.find('span').each(function(){
                });
                delete trading_groups[parent_tr.data('row')]; 
            }
            if(distributor_network_select.length){
                distributor_network_select.find('span').each(function(){
                });
                delete distributor_networks[parent_tr.data('row')]; 
            }
            if(aag_members_select.length){
                aag_members_select.find('span').each(function(){
                });
                delete aag_members[parent_tr.data('row')]; 
            }
            if(profiles_select.length){
                profiles_select.find('span').each(function(){
                });
                delete profiles[parent_tr.data('row')]; 
            }
            if(customer_activities_select.length){
                customer_activities_select.find('span').each(function(){
                });
                delete customer_activities[parent_tr.data('row')]; 
            }
            if(suppliers_categories_select.length){
                suppliers_categories_select.find('span').each(function(){
                });
                delete suppliers_categories[parent_tr.data('row')]; 
            }
            if(bdm_select.length){
                bdm_select.find('span').each(function(){
                });
                delete bdms[parent_tr.data('row')]; 
            }

            parent_tr.remove();
            clearFields();
        });
    }

    var rolePositionType = function(){
        if($('#position_role').val() == ''){
            $('#role_position_type').addClass('d-none');
        } else {
            var url = $('#position_role').data('url');
            var data = {};
            data.role_id = $('#position_role').val();
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $('#position_type_cnt').html(data);
                $('#role_position_type').removeClass('d-none');
                $('#position_config_type').select2();
                selectType();
                positionGroupPermissions();
            });
        }
        $('#position_role').on('change',function(){
            if($(this).val() == ''){
                $('#role_position_type').addClass('d-none');
            } else {
                var url = $(this).data('url');
                var data = {};
                data.role_id = $(this).val();
                var request = PeticionAjax.post(url,data);
                request.done(function(data){
                    $('#position_type_cnt').html(data);
                    $('#role_position_type').removeClass('d-none');
                    $('#position_config_type').select2();
                    selectType();
                    positionGroupPermissions();
                });
            }
        });
    };

    var positionGroupPermissions = function(){
        if($('#position_config_type').val() != ''){
            var url = $(this).data('url');
            var data = {};
            data.role_id = $(this).val();
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $('#group_permission_cnt').html(data);
                $('#permissions').select2();
            });
        }
        $('#position_config_type').on('change',function(){
            if($(this).val() != ''){
                var url = $(this).data('url');
                var data = {};
                data.position_config_type_id = $(this).val();
                var request = PeticionAjax.post(url,data);
                request.done(function(data){
                    $('#group_permission_cnt').html(data);
                    $('#permissions').select2();
                });
            }
        });
    };

    var selectAll = function(){
        var input_networks = $('#input_networks').val();
        $('#input_networks').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_networks != null && $this.val() != null){
                    var intersection = input_networks.filter(function(){ return $this.val() });
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_networks = null;
                            $('#input_networks').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_networks').val(difference).trigger("change", true);
                    }
                }
            } 
            input_networks = $this.val();
        });

        var input_regions = $('#input_regions').val();
        $('#input_regions').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_regions != null && $this.val() != null){
                    var intersection = input_regions.filter(function(){ return $this.val() });
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_regions = null;
                            $('#input_regions').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_regions').val(difference).trigger("change", true);
                    }
                }
            } 
            input_regions = $this.val();
        });

        var input_trading_groups = $('#input_trading_groups').val();
        $('#input_trading_groups').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_trading_groups != null && $this.val() != null){
                    var intersection = input_trading_groups.filter(function(){return $this.val()});
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_trading_groups = null;
                            $('#input_trading_groups').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_trading_groups').val(difference).trigger("change", true);
                    }
                }
            } 
            input_trading_groups = $this.val();
        });

        var input_distributor_networks = $('#input_distributor_networks').val();
        $('#input_distributor_networks').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_distributor_networks != null && $this.val() != null){
                    var intersection = input_distributor_networks.filter(function(){return $this.val()});
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_distributor_networks = null;
                            $('#input_distributor_networks').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_distributor_networks').val(difference).trigger("change", true);
                    }
                }
            } 
            input_distributor_networks = $this.val();
        });

        var input_aag_members = $('#input_aag_members').val();
        $('#input_aag_members').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_aag_members != null && $this.val() != null){
                    var intersection = input_aag_members.filter(function(){return $this.val()});
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_aag_members = null;
                            $('#input_aag_members').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_aag_members').val(difference).trigger("change", true);
                    }
                }
            } 
            input_aag_members = $this.val();
        });

        var input_profiles = $('#input_profiles').val();
        $('#input_profiles').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_profiles != null && $this.val() != null){
                    var intersection = input_profiles.filter(function(){return $this.val()});
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_profiles = null;
                            $('#input_profiles').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_profiles').val(difference).trigger("change", true);
                    }
                }
            } 
            input_profiles = $this.val();
        });

        var input_customer_activities = $('#input_customer_activities').val();
        $('#input_customer_activities').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_customer_activities != null && $this.val() != null){
                    var intersection = input_customer_activities.filter(function(){return $this.val()});
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_customer_activities = null;
                            $('#input_customer_activities').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_customer_activities').val(difference).trigger("change", true);
                    }
                }
            } 
            input_customer_activities = $this.val();
        });

        var input_suppliers_categories = $('#input_suppliers_categories').val();
        $('#input_suppliers_categories').on('change',function(e, trigger){
            var $this = $(this);
            if(!trigger){
                if(input_suppliers_categories != null && $this.val() != null){
                    var intersection = input_suppliers_categories.filter(function(){return $this.val()});
                    if(intersection[0] != -2){
                        if($this.val().includes("-2")){
                            input_suppliers_categories = null;
                            $('#input_suppliers_categories').val(-2).trigger("change"); 
                        }
                    } else {
                        var difference = $this.val().pop();
                        $('#input_suppliers_categories').val(difference).trigger("change", true);
                    }
                }
            } 
            input_suppliers_categories = $this.val();
        });
    };
    
    var warningDelete = function(){
        var prev_val = $('#position_role').val();
        $('#position_role').on('change',function(e, change){
            if(change == undefined && prev_val != ''){
                swal({
                    title: $.i18n._('Alert.Warning'),
                    text: $.i18n._('Position.Warning_delete_text'),
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: $.i18n._('General.Yes'),
                  }).then(function(result){
                    if (result.value) {
                        $('.delete-row').each(function(){
                            $(this).trigger('click');
                        });
                        prev_val = $('#position_role').val()
                    } else {
                        $('#position_role').val(prev_val).trigger('change',[false]);
                    }
                });
            } else {
                prev_val = $('#position_role').val()
            }
        });
    }

    var logTableData = function(){
        var myTableHeaderArray = [];
        $("table #table_position_header").each(function() {
            var arrayOfThisRow = [];
            var tableData = $(this).find('th');
            tableData.each(function() {
                arrayOfThisRow.push($(this).text().trim()); 
            });
            myTableHeaderArray.push(arrayOfThisRow);
        });

        $("table #table_position tr").each(function() {
            var tableData = $(this).find('td');
            if (tableData.length > 0) {
                rowData = '';
                tableData.each(function(key) {
                    if($(this).text().trim() != ''){
                        rowData += myTableHeaderArray[0][key] + ': ' + $(this).text().trim() + '. '
                    }
                });
                myTableArrayLog.push(rowData);
            }
        });
    }

    function arraysEqual(a, b) {
        if (a === b) return true;
        if (a == null || b == null) return false;
        if (a.length != b.length) return false;
      
        for (var i = 0; i < a.length; ++i) {
          if (a[i] !== b[i]) return false;
        }
        return true;
      }
    
    return {
        load: function(){
            addPosition();
            safeSave();
            deleteRow();
            rolePositionType();
            positionGroupPermissions();
            selectAll();
            warningDelete();
            logTableData();
        },
    }

})();