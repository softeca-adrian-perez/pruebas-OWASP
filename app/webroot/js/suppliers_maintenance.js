$(document).ready(function () {
    SupplierMaintenance.load();
});

var SupplierMaintenance = (function () {

    var deleteSupplierCategory = function(){

        $(".delete-supplier_category-js").off('click').on('click',function(e){
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
                    var url_redirect = element.data('url_redirect');
                    var data = {}
                    data.category_id = element.data('category_id');
        
                    var request = PeticionAjax.post(url, data);
                    request.done(function(data) {
                        if(data != 'error'){
                            window.location.replace(url_redirect);
                        }else{
                            swal({
                                title: $.i18n._('Constants.Message_bad_deleted'),
                                type: "error",
                                text: element.data('relationship_dependency'),
                                confirmButtonColor: primary_color,
                            });
                        }
                    });

                }
            });
        });
    }

    var editSupplierCategory = function(){
        $(".supplier_category_edit-js").off('click').on('click',function(e){
            e.preventDefault();
            var element = $(this);
            var active_options = element.data('active_options');
            var myForm = document.getElementById('form_suppliers-js');

            var file_id = element.data('file_id');

            var default_category_id = element.data('category_id');
            var default_name = element.data('name');
            var default_active = element.data('active');

            var input_cactegories = element.data('suppliers_categories');
            var active_options = element.data('active_options');
                swal.queue([
                    {
                        title: $.i18n._('Suppliers.Please_select_category'),
                        input: 'select',
                        inputOptions: input_cactegories,
                        inputValue: default_category_id,
                        inputValidator: function(value){
                            return !value && $.i18n._('Constants.Error_alert_name_empty')
                        },
                        showCancelButton: true,
                        progressSteps: ['1', '2', '3']
                    },
                    {
                        title: $.i18n._('Suppliers.File_name'),
                        input: 'text',
                        showCancelButton: true,
                        inputValue: default_name,
                        inputValidator: function(value){
                            return !value && $.i18n._('Constants.Error_alert_name_empty')
                        },
                        progressSteps: ['1', '2', '3']
                    },
                    {
                        title: $.i18n._('Suppliers.Is_active'),
                        input: 'select',
                        inputOptions: active_options,
                        showCancelButton: true,
                        inputValue: default_active,
                        inputValidator: function(value){
                            return !value && $.i18n._('Constants.Error_alert_name_empty')
                        },
                        progressSteps: ['1', '2', '3']
                    },
            ]).then(function (result) {
                if(result.value != undefined){
                    var category_id = result.value[0]
                    var file_name = result.value[1]
                    var is_active = result.value[2]

                    if(is_active == 1){
                        $('#' + 'supplier_active-js-' + file_id).removeClass('ion-close c-fallo')
                        $('#' + 'supplier_active-js-' + file_id).addClass('ion-checkmark c-exito')
                        $('#' + 'supplier_active-js-' + file_id).attr('title', $.i18n._('General.Active'))
                    }else{
                        $('#' + 'supplier_active-js-' + file_id).addClass('ion-close c-fallo')
                        $('#' + 'supplier_active-js-' + file_id).removeClass('ion-checkmark c-exito')
                        $('#' + 'supplier_active-js-' + file_id).attr('title', $.i18n._('General.Inactive'))
                    }

                    $('#' + 'supplier_file_name-js-' + file_id).html(file_name);
                    
                    var category_id_old = $('#file_id-' + file_id + '-js').data('category_id');

                    $('#cnt_category_name-' + category_id).removeClass('d-none')
                    jQuery("#file_id-" + file_id + "-js").appendTo('#cnt_category_id-' + category_id +'-js')
                    

                    $('#file_id-' + file_id + '-js').data('category_id', category_id)


                    var num_lines = $('#cnt_category_name-' + category_id).data('num_items');
                    $('#cnt_category_name-' + category_id).data('num_items', num_lines + 1);


                    var num_lines = $('#cnt_category_name-' + category_id_old).data('num_items');
                    $('#cnt_category_name-' + category_id_old).data('num_items', num_lines - 1);


                    if( $('#cnt_category_name-' + category_id_old).data('num_items') == 0 ){
                        $('#cnt_category_name-' + category_id_old).addClass('d-none');
                    }


                    element.data('category_id', category_id)
                    element.data('name', file_name)
                    element.data('active', is_active)

                    var hiddenInput_tmp = document.createElement('input');
                    hiddenInput_tmp.type = 'hidden';
                    hiddenInput_tmp.name = 'data[SupplierFileBefore][' + file_id + '][supplier_category_id]';
                    hiddenInput_tmp.value = category_id;
                    myForm.appendChild(hiddenInput_tmp);

                    var hiddenInput_tmp = document.createElement('input');
                    hiddenInput_tmp.type = 'hidden';
                    hiddenInput_tmp.name = 'data[SupplierFileBefore][' + file_id + '][name]';
                    hiddenInput_tmp.value = file_name;
                    myForm.appendChild(hiddenInput_tmp);

                    var hiddenInput_tmp = document.createElement('input');
                    hiddenInput_tmp.type = 'hidden';
                    hiddenInput_tmp.name = 'data[SupplierFileBefore][' + file_id + '][active]';
                    hiddenInput_tmp.value = is_active;
                    myForm.appendChild(hiddenInput_tmp);

                }
            })
        });

        $(".supplier_file_delete-js").off('click').on('click', function (e) {
            e.preventDefault();
            data = {};
            data.id = $(this).data('id');
            var url = $(this).data('url');
            swal({
                title: $(this).data('confirmmsg'),
                type: $(this).data('type'),
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function(result){
                if(result.value) {
                    var request = PeticionAjax.post(url, data);
                    request.done(function (data) {
                        $('#file-list-js').html(data);
                        editSupplierCategory();
                    });
                }
            })
        });

        $(".supplier_category_select-js").on('change',function(e){
            e.preventDefault();
            var element = $(this);
            var file_id = element.data('file_id');
            $('#supplier_category_hidden-js' + file_id).val(element.val());
        });

    }

    var loadSupplierFile = function () {
        $('.dragdrop_suppliers-js').each(function(){
            var element = $(this);
            element.niceFileInput();
            var fileWrapperParent = element.parents('.fileWrapper:not(.fileWrapperList)');
            if(element.hasClass('dragdrop_suppliers-multiple-js')){
                element.next().hide();
                fileWrapperParent.addClass('fileWrapperMultiple');
            }

            var files_tmp = {};
            var cont = 1;
            var myForm = document.getElementById('form_suppliers-js');

            element.change(function(){

                element = $(this);

                var input_cactegories = element.data('suppliers_categories');
                var active_options = element.data('active_options');
                swal.queue([
                    {
                        title: $.i18n._('Suppliers.Please_select_category'),
                        input: 'select',
                        inputOptions: input_cactegories,
                        showCancelButton: true,
                        inputValidator: function(value){
                            return !value && $.i18n._('Constants.Error_alert_name_empty')
                        },
                        progressSteps: ['1', '2', '3']
                    },
                    {
                        title: $.i18n._('Suppliers.File_name'),
                        input: 'text',
                        showCancelButton: true,
                        inputValidator: function(value){
                            return !value && $.i18n._('Constants.Error_alert_name_empty')
                        },
                        progressSteps: ['1', '2', '3']
                    },
                    {
                        title: $.i18n._('Suppliers.Is_active'),
                        input: 'select',
                        inputOptions: active_options,
                        showCancelButton: true,
                        inputValidator: function(value){
                            return !value && $.i18n._('Constants.Error_alert_name_empty')
                        },
                        progressSteps: ['1', '2', '3']
                    },
                ]).then(function (result) {
                    if(result.value != undefined){
                        var category_id = result.value[0]
                        var file_name = result.value[1]
                        var is_active = result.value[2]

                        if(is_active == 1){
                            var is_active_tex = $.i18n._('General.Yes');
                        }else{
                            var is_active_tex = $.i18n._('General.No');
                        }

                        var url_get_category_name_by_id = element.data('get_category_name_by_id');
                        var data = {}
                        var category_name = '';
                        data.category_id = category_id

                        var request = PeticionAjax.post(url_get_category_name_by_id, data);
                        request.done(function (data) {

                            category_name = data;
                            
                            var data_dict = {}
                            data_dict.file_name = element.val().replace("C:\\fakepath\\","")
                            data_dict.cont = cont
                            data_dict.category_id = category_id
                            data_dict.category_name = category_name
                            data_dict.file_name = file_name
                            data_dict.is_active = is_active

                            files_tmp[cont] = data_dict;
                            
                            var hiddenInput_tmp = document.createElement('input');
                            hiddenInput_tmp.type = 'hidden';
                            hiddenInput_tmp.name = 'data[SupplierFile][suppliers_categories]';
                            hiddenInput_tmp.value = JSON.stringify(files_tmp);
                            myForm.appendChild(hiddenInput_tmp);

                            fileWrapperParent = element.parents('.fileWrapper:not(.fileWrapperList)');

                            var fileWrapperDragDropDeleteFile = '<span style="padding-left:12px">' + 
                            $.i18n._('Suppliers.Category') + ': ' + category_name + '<br>' + 
                            $.i18n._('General.Name') + ': ' + file_name + '<br>' + 
                            $.i18n._('General.Active') + ': ' + is_active_tex +
                            '<span class="dragdrop-delete-file-js" data-id=' + cont +'><span class="icon-delete"></span></span>' +
                            '</span>';

                            if(element.hasClass('dragdrop_suppliers-multiple-js')){
                                var fileWrapperClone = fileWrapperParent.clone(true, true);
                                var fileWrapperParentInputText = fileWrapperParent.find('.fileInputText');
                                fileWrapperParentInputText.show();
                                fileWrapperParentInputText.after(fileWrapperDragDropDeleteFile);
                                fileWrapperParent.addClass('fileWrapperList');
                                element.parent().before(fileWrapperClone);
                                fileWrapperClone.children('input[type="file"]').val('');
                                fileWrapperClone.children('.fileInputText').val('');
                            }else{
                                if(!fileWrapperParent.find('.dragdrop-delete-file-js').html()){
                                    fileWrapperParent.find('.fileInputText').after(fileWrapperDragDropDeleteFile);
                                    element.next().show();
                                }
                            }

                            cont ++;

                            cargarEliminarDragAndDrop(myForm, files_tmp, hiddenInput_tmp);
                        });
                        
                    }else{
                        $('#suppliers-files').val(null);
                    }
                })

            });
        });

        var cargarEliminarDragAndDrop = function(myForm, files_tmp, hiddenInput_tmp){
            $('.dragdrop-delete-file-js').click(function(){
                $fileWrapperParent = $(this).parents('.fileWrapper');
                if($fileWrapperParent.hasClass('fileWrapperList')){
                    $fileWrapperParent.remove();
                }else{
                    $fileWrapperParent.find('input[type="file"]').val('');
                    $fileWrapperParent.find('.fileInputText').val('');
                    $(this).remove();
                }

                var pos_hidden = $(this).data('id');
                delete files_tmp[pos_hidden];
                hiddenInput_tmp.type = 'hidden';
                hiddenInput_tmp.name = 'data[SupplierFile][suppliers_categories]';
                hiddenInput_tmp.value = JSON.stringify(files_tmp);
                myForm.appendChild(hiddenInput_tmp);
            });
        };
    }

    var sendData = function() {
        $("#btn-guardar").on('click',function(e){
            if($('#aag-region-select').length > 0){
                e.preventDefault();
                $('#aag-region-select').attr('disabled', false);
                $('#form_suppliers-js').submit();
            }
        });
    }

    return {
        load: function () {
            deleteSupplierCategory();
            editSupplierCategory();
            loadSupplierFile();
            sendData();
        }
    }
})();