$(document).ready(function(){
    Keyword.load();
    var selected_id = "";
});

var Keyword = (function(){

    var select = function(){
        $(".select_tr").click(function(e){
            e.preventDefault();
            selected_id = $(this).attr('id');
            $('#title-keyword').html('Edit Keyword');
            $(".select_tr").removeClass('bg-primary-i');
            $(this).addClass('bg-primary-i');
            var keyword_name = $(this).find("td:nth-child(1)").text();
            keyword_name = $.trim(keyword_name);
            var url = $(this).data('url');
            $('#input-keyword-name').val(keyword_name);
            $('#add-edit-keyword').html('Edit Keyword');
            $('#unselect-keyword').prop('disabled', false);

        });

        $('#unselect-keyword').click(function(e){
            e.preventDefault();
            $('#unselect-keyword').prop('disabled', true);
            $('#add-edit-keyword').html('Add Keyword');
            $('#title-keyword').html('Add Keyword');
            $('#input-keyword-name').val("");
            $(".select_tr").removeClass('bg-primary-i');
            $("#input-keyword-file").val("");
        });

        var inside_form = false;
        var inside_table = false;
        var inside_alert = false;

        $('#mask_click').click(function(){
            if(inside_form == false && inside_table == false && inside_alert == false){
                $('#unselect-keyword').trigger('click');
            }
            inside_form = false;
            inside_table = false;
            inside_alert = false;
        });
        $('#form_click').click(function(){
            inside_form = true;
        });
        $('#table_click').click(function(){
            inside_table = true;
        });
        $('#alert-div').click(function(){
            inside_alert = true;
        });
    };

    var add = function(){
        $("#KeywordHomeForm").submit(function(e){
            e.preventDefault();
            if($('#add-edit-keyword').html() == "Add Keyword"){
                var url = '/keywords/ajax_add_keyword/' + $('#input-keyword-name').val();
                var request = $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: url
                });

                request.done(function(data){
                    if(!data.message){
                        if(($('#current_page').html() == $('#total_pages').html()) && ($('#total_keywords').html() != 10)){
                            var keyword_id = data.Keyword.id;
                            // file deepcode ignore DOMXSS: <please specify a reason of ignoring this>
                            $('#table-keywords').append(
                                "<tr class='select_tr' " +
                                "id='" + keyword_id + "'" +
                                "data-id='" + keyword_id + "'" +
                                "> + " +
                                "<td class='ta-center'> " + $('#input-keyword-name').val() + "</td> " +
                                "<td class='ta-center'> " +
                                "<a href='javascript:;' " +
                                "class='delete-keyword-js' " +
                                "data-confirmmsg='Do you want to delete this keyword?' " +
                                "data-id='" + keyword_id + "'" +
                                "data-url='/keywords/ajax_delete_keyword/" + data.Keyword.id + "'" +
                                "title='Delete'>" +
                                "<span class='ion-trash-b c-fallo'></span>" +
                                "</a>" +
                                "</td>" +
                                "</tr>"
                            );

                            $('.select_tr').unbind();
                            $(".delete-keyword-js").unbind();

                            select();
                            deleteKeyword();

                            $("#unselect-keyword").trigger("click");
                            $('#total_paginator').html(parseInt($('#total_paginator').html()) + 1);

                            Alertas.show($('#alert-div'), "exito", "Keyword has been added successfully");
                        } else if(( parseInt($('#total_paginator').html()) / 10 ) == parseInt($('#total_pages').html())){
                            window.location.href = '/keywords/home/page:' + (parseInt($('#total_pages').html()) + 1);
                        } else {
                            window.location.href = '/keywords/home/page:' + $('#total_pages').html();
                        }
                    } else {
                        Alertas.show($('#alert-div'), "fallo", data.message);
                    }
                });
                request.fail(function(){
                    Alertas.show($('#alert-div'), "fallo", "An error has occured");
                });
            }
        });
    };

    var edit = function(){
        $("#KeywordHomeForm").submit(function(e){
            e.preventDefault();
            if($('#add-edit-keyword').html() == "Edit Keyword"){
                var url = "/keywords/ajax_edit_keyword";
                url += "/" + $('#input-keyword-name').val();
                url += "/" + selected_id;
                var request = PeticionAjax.post(url);

                request.done(function(data){
                    if(!data.message){
                        $('#' + selected_id + ' td:eq(0)').html($('#input-keyword-name').val());
                        $("#unselect-keyword").trigger("click");
                        Alertas.show($('#alert-div'), "exito", "Service has been edited successfully");
                    } else {
                        Alertas.show($('#alert-div'), "fallo", data.message);
                    }
                });

                request.fail(function(){
                    Alertas.show($('#alert-div'), "fallo", "An error has occured");
                });
            }
        });
    };

    var deleteKeyword = function(){
        $(".delete-keyword-js").click(function(e){
            e.preventDefault();
            var keyword_id = $(this).data('id');
            if(confirm($(this).data('confirmmsg'))){
                var request = PeticionAjax.post($(this).data('url'));

                request.done(function(){
                    if($('#total_keywords').html() == 1){
                        window.location.href = '/keywords/home/page:' + (parseInt($('#current_page').html()) - 1);
                    } else {
                        window.location.href = '/keywords/home/page:' + parseInt($('#current_page').html());
                    }
                });

                request.fail(function(){
                    Alertas.show($('#alert-div'), "fallo", "An error has occured");
                });
            }
        });
    };

    return {
        load: function(){
            add();
            deleteKeyword();
            select();
            edit();
        }
    }

})();