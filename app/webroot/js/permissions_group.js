$(document).ready(function(){
    PermissionGroup.load();
});

var PermissionGroup = (function(){

    var viewPermissionGroup = function(){
        $( ".view-group-permission-js" ).click(function(e) {
            e.preventDefault();

            var url_permission = $( ".view-group-permission-js" ).data('permission_url')
            var data = {};
            data.user_id = $(this).data('user_id');
            data.group_permission_id = $(this).data('group_permission_id');

            var request = PeticionAjax.post(url_permission, data);
            request.done(function(data) {
                $('#permission-group').html(data);
                $('#permission-group').show();
            });
        });
    };

    var deletePermissionGroup = function(){
        $( ".delete_group_permission-js" ).click(function(e) {
            e.preventDefault();
            var element = $(this);

            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: element.data('yes'),
                cancelButtonText: element.data('no')
            }).then(function (result) {
                if (result.value) {
                    var url = element.data('url_delete');
                    url += '/' + element.data('group_permission_id');

                    window.location = url;
                }
            });
        });
    };

    var changeTypePermissionGroup = function(){
        var prev_value = $("#group_permission_type-js").val();
        $("#group_permission_type-js").on('change',function(e, action){
            if(action != true){
                e.preventDefault();

                var element = $(this);
    
                swal({
                    title: element.data('confirmmsg'),
                    text: element.data('confirmmsg_text'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: primary_color,
                    confirmButtonText: element.data('yes'),
                    cancelButtonText: element.data('no')
                }).then(function (result) {
                    if (result.value) {
    
                        var url = element.data('url')
                        var data = {};
                        data.type = element.val();
            
                        var request = PeticionAjax.post(url, data);
                        request.done(function(data) {
                            $('#permissions_table-js').html(data);
                            prev_value = element.val();
                        });
    
                    } else {
                        element.val(prev_value).trigger('change',[true]);
                    }
                });
            }
        });
    };

    var newModalPermissionGroupFromPosition = function(){
        $("#button_new_group_permission-js").off('click').on('click',function(e){
            e.preventDefault();

            var type = $('#position_config_type').val()

            var url = $(this).data('url')
            var data = {};
            data.type = type

            var request = PeticionAjax.post(url, data);
            request.done(function(data) {
                $('#modal_new_permission_group-js').html(data);
                $('#primary_div').removeAttr( 'style' );
                $('#group_permission_type-js').val(type)
                $('#btn_save_from_position-js').removeClass('d-none')
                $('#cnt-form_actions-js').addClass('d-none')
                $('#group_permission_type-js').prop('disabled', true)
                $('#group_permission_type_hidden-js').val(type)
                savePermissionGroupFromPosition();
            });

        });
    };

    var savePermissionGroupFromPosition = function(){
        $("#button_new_group_permission_save-js").off('click').on('click',function(e){
            e.preventDefault();
            var element = $(this);
            var element_id = element.data('element_id');

            var data = $('#GroupPermissionForm').serialize();
            var url = $('#GroupPermissionForm').attr('action');
            var url_last_insert_id = element.data('url_get_last_group_permission');

            if( $('#' + element_id + '-js').val() != '' ){
                PeticionAjax.post(url, data).done(function(data){
                    var url_get_group_permissions = $('#button_new_group_permission-js').data('url_get_group_permissions');
                    var type = $('#position_config_type').val()
                    var data = {}
                    data.position_config_type_id = type
    
                    var request = PeticionAjax.post(url_get_group_permissions, data);
                    request.done(function(data) {
                        $('#group_permission_cnt').html(data);
                        Select2.load();

                        var request = PeticionAjax.post(url_last_insert_id, data);
                        request.done(function(data) {
                            $('#permissions').val(data).trigger('change');
                        });
    
                        $('a.close-modal').trigger('click');
                    });
                })
                .fail(function( jqXHR, textStatus ) {
                    swal({
                        title: $.i18n._('General.Error'),
                        text: textStatus,
                        type: 'error'
                    });
                });
            }else{
                swal({
                    title: $.i18n._('Constants.Error_alert_name_empty'),
                    type: "error",
                    confirmButtonColor: primary_color,
                });
            }
            
        });

    };

    var savePermission = function(){
        if(!$('#position-page').length){
            $("#btn-guardar").off('click').on('click',function(e){
                e.preventDefault();
                if($('.ion-asterisk').parent().prev().val() == ''){
                    swal($.i18n._('Constants.Error_alert_general'), $.i18n._('Position.Mandatory_name'), 'error');
    
                }
                else{
                    $('#btn-guardar').off().trigger('click');
                }
            });
        }
    }; 

    var clickCheckbox = function(){
        $('#permissions_table-js').on('click', 'tr', function(e){
            if (e.target.type != 'checkbox'){
                $(this).find('input:checkbox').trigger('click');
            }
        });
    }; 

    return {
        load: function(){
            viewPermissionGroup();
            deletePermissionGroup();
            changeTypePermissionGroup();
            newModalPermissionGroupFromPosition();
            savePermission();
            clickCheckbox();
        }
    }
})();