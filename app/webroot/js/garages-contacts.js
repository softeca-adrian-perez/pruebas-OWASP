$(document).ready(function () {
    GarageContact.load();
    selected_row_bdm = "";
    selected_row_staff = "";
    selected_row_general_branch_manager = "";
});

var GarageContact = (function () {

    var sortContacts = function(){
        $("#sort-contacts").sortable({
            stop: function( event, ui ) {
                var order = 1;
                var data = {};
                $('#garages-bdm').empty();
                $('.item-distributor').each(function(){
                    var id_tmp = $(this).data('id');
                    var id_dist_tmp = $(this).data('contact_id');
                    data[id_tmp]= order;
                    var newOption = new Option(order, id_dist_tmp+' '+id_tmp+' '+order, true, true);
                    $('#garages-bdm').append(newOption).trigger('change');
                    order++;
                });
                updateContent();
            }
        });
    };

    var reOrder = function () {
        var counter = 1;
        $("#sort-contacts").find('.order-row').each(function () {
            $(this).html(counter);
            counter++;
        });
    };

    var addBDMContact = function () {
        $('#btn_add_bdm').on('click', function (event) {
            event.preventDefault();
            if ($('#distributor_name').val() != null) {
                var data_ = {};
                data_.contact_id = $('#distributor_name').val();
                if ($(".item-distributor[data-contact_id='" + data_.contact_id + "']").length > 0) {
                    return;
                }

                var order_row = null;
                if (isNaN(Number($('.order-row').last().find('span').html()) + 1)) {
                    order_row = 1;
                } else {
                    order_row = Number($('.order-row').last().find('span').html()) + 1;
                }

                var url_ = $('#btn_add_bdm').data('url_info');

                var request = PeticionAjax.postJSON(url_, data_);
                request.done(function (data) {
                    var $table_row_1 = '';
                    $table_row_1 += "<tr>";
                    $table_row_1 += "<td class='order-row'>" + order_row + "</td>";
                    $table_row_1 += "<td class='item-distributor' " + "data-contact_id='" + data_.contact_id + "'>" + data.Contact.first_name + "</td>";
                    $table_row_1 += "<td>" + data.Contact.last_name + "</td>";
                    $table_row_1 += "<td>" + data.Contact.position_id + "</td>";
                    if (data.Contact.phone !== null) {
                        $table_row_1 += "<td>" + data.Contact.phone + "</td>";
                    } else {
                        $table_row_1 += "<td>" + '' + "</td>";
                    }
                    if (data.Contact.mobile_phone !== null) {
                        $table_row_1 += "<td>" + data.Contact.mobile_phone + "</td>";
                    } else {
                        $table_row_1 += "<td>" + '' + "</td>";
                    }
                    if (data.Contact.email !== null) {
                        $table_row_1 += "<td>" + data.Contact.email + "</td>";
                    } else {
                        $table_row_1 += "<td>" + '' + "</td>";
                    }
                    $table_row_2 =
                        "<td class='ta-center btn-hide'>" +
                        "<span class='delete-employee ion-android-cancel cursor-pointer c-fallo delete-contact-bdm'></span>" +
                        "</td>" +
                        "</tr>"
                    $('#sort-contacts').append($table_row_1 + $table_row_2);
                    var newOption = new Option(data.Contact.first_name, data.Contact.id+' '+order_row, true, true);
                    $('#garages-bdm').append(newOption).trigger('change');
                    updateContent();
                });
            }
        });
    };

    var deleteContactBDM = function () {
        $('.delete-contact-bdm').off('click').on('click', function () {
            var parent_tr = $(this).closest('tr');
            swal({
                title: $.i18n._('GarageBDM.Confirm_delete'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var data = {};
                    data.garage_contact_id = parent_tr.find('.item-distributor').data('id');
                    data.contact_id = parent_tr.find('.item-distributor').data('contact_id');
                    data.garage_id = $('#garage_id').val();
                    if (data.garage_contact_id === undefined) {
                        // no inserted in db yet, just remove from the "to be inserted" select
                        $('#garages-bdm > option').each(function () {
                            if (this.value.startsWith(data.contact_id + " ")) {
                                $(this).remove();
                                $('#garages-bdm').trigger('change');
                            }
                        });
                    } else {
                        // already inserted in bd, add to the "to be deleted" select
                        var newOption = new Option(data.garage_id, data.garage_contact_id, true, true);
                        $('#garages-contact-delete').append(newOption).trigger('change');
                    }
                    parent_tr.remove();
                    updateContent();
                }
            });
        });
    };

    var updateContent = function () {
        reOrder();
        $("#sort-contacts").sortable("destroy");
        $("#sort-contacts li").removeClass('ui-state-default');
        $("#sort-contacts li span").remove();
        sortContacts();
        deleteContactBDM();
    };

    var addBDM = function () {
        $('.status-active-bdm').attr('title', $.i18n._('General.Delete'));
        $('.status-inactive-bdm').attr('title', $.i18n._('General.Add'));
        $('.status-active-bdm').click(function (e) {
            selected_row_bdm = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_bdm.attr('data-url')
            });
            request.done(function (data) {
                if (!data.message) {
                    selected_row_bdm.attr('data-url', '/garages_contacts_bdm/ajax_add_garage_contact_bdm/' +
                        selected_row_bdm.data('garage-id') + "/" +
                        selected_row_bdm.data('contact-id'));
                    selected_row_bdm.removeClass('status-active-bdm');
                    selected_row_bdm.removeClass('c-exito');
                    selected_row_bdm.addClass('c-defecto');
                    selected_row_bdm.addClass('status-inactive-bdm');
                    $('.status-active-bdm').unbind();
                    $('.status-inactive-bdm').unbind();

                    addBDM();
                }
            });
        });
        $('.status-inactive-bdm').click(function (e) {
            selected_row_bdm = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_bdm.attr('data-url')
            });
            request.done(function (data) {
                if (!data.message) {
                    selected_row_bdm.attr('data-url', '/garages_contacts_bdm/ajax_remove_garage_contact_bdm/' +
                        selected_row_bdm.data('garage-id') + "/" +
                        selected_row_bdm.data('contact-id'));
                    selected_row_bdm.removeClass('status-inactive-bdm');
                    selected_row_bdm.addClass('c-exito');
                    selected_row_bdm.removeClass('c-defecto');
                    selected_row_bdm.addClass('status-active-bdm');
                    $('.status-active-bdm').unbind();
                    $('.status-inactive-bdm').unbind();

                    addBDM();
                }
            });
        });
    };

    var addStaff = function () {
        $('.status-active-staff').click(function (e) {
            selected_row_staff = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_staff.attr('data-url')
            });
            request.done(function (data) {
                if (!data.message && !data.error) {
                    selected_row_staff.attr('data-url', '/garages_contacts_staff/ajax_add_garage_contact_staff/' +
                        selected_row_staff.data('garage-id') + "/" +
                        selected_row_staff.data('contact-id'));
                    selected_row_staff.removeClass('status-active-staff');
                    selected_row_staff.removeClass('c-exito');
                    selected_row_staff.addClass('c-defecto');
                    selected_row_staff.addClass('status-inactive-staff');
                    $('.status-active-staff').unbind();
                    $('.status-inactive-staff').unbind();
                    addStaff();
                } else {
                    Tools.actualizarFlashMessage();
                }
            });
        });
        $('.status-inactive-staff').click(function (e) {
            selected_row_staff = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_staff.attr('data-url')
            });
            request.done(function (data) {
                if (!data.message) {
                    selected_row_staff.attr('data-url', '/garages_contacts_staff/ajax_remove_garage_contact_staff/' +
                        selected_row_staff.data('garage-id') + "/" +
                        selected_row_staff.data('contact-id'));
                    selected_row_staff.removeClass('status-inactive-staff');
                    selected_row_staff.addClass('c-exito');
                    selected_row_staff.removeClass('c-defecto');
                    selected_row_staff.addClass('status-active-staff');
                    $('.status-active-staff').unbind();
                    $('.status-inactive-staff').unbind();

                    addStaff();
                }
            });
        });
    };

    var priorityCheckbox = function () {
        $('.priority_contact_checkbox-js').parent().click(function () {
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: $(this).find('input[type="checkbox"]').data('url')
            });
            request.done(function (result) {
                if (result) {
                    swal({
                        title: $.i18n._("Constants.Message_well_updated"),
                        type: "success",
                    }).then(function (result) {
                        location.reload();
                    });
                } else {
                    swal({
                        title: $.i18n._("Constants.Message_bad_updated"),
                        type: "error",
                    });
                }
            });
            request.fail(function () {
                swal({
                    title: $.i18n._("Constants.Message_bad_updated"),
                    type: "error",
                });
            });
        });
    };

    var addGeneralBranchManager = function () {
        $('.status-active-general-branch-manager').click(function (e) {
            selected_row_general_branch_manager = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_general_branch_manager.attr('data-url')
            });
            request.done(function (data) {
                if (!data.message) {
                    selected_row_general_branch_manager.attr('data-url', '/garages_contacts_general_branch_manager/ajax_add_garage_contact_general_branch_manager/' +
                        selected_row_general_branch_manager.data('garage-id') + "/" +
                        selected_row_general_branch_manager.data('contact-id'));
                    selected_row_general_branch_manager.removeClass('status-active-general-branch-manager');
                    selected_row_general_branch_manager.removeClass('c-exito');
                    selected_row_general_branch_manager.addClass('c-defecto');
                    selected_row_general_branch_manager.addClass('status-inactive-general-branch-manager');
                    $('.status-active-general-branch-manager').unbind();
                    $('.status-inactive-general-branch-manager').unbind();

                    addGeneralBranchManager();
                }
            });
        });
        $('.status-inactive-general-branch-manager').click(function (e) {
            selected_row_general_branch_manager = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_general_branch_manager.attr('data-url')
            });
            request.done(function (data) {
                if (!data.message) {
                    selected_row_general_branch_manager.attr('data-url', '/garages_contacts_general_branch_manager/ajax_remove_garage_contact_general_branch_manager/' +
                        selected_row_general_branch_manager.data('garage-id') + "/" +
                        selected_row_general_branch_manager.data('contact-id'));
                    selected_row_general_branch_manager.removeClass('status-inactive-general-branch-manager');
                    selected_row_general_branch_manager.addClass('c-exito');
                    selected_row_general_branch_manager.removeClass('c-defecto');
                    selected_row_general_branch_manager.addClass('status-active-general-branch-manager');
                    $('.status-active-general-branch-manager').unbind();
                    $('.status-inactive-general-branch-manager').unbind();
                    addGeneralBranchManager();
                }
            });
        });
    };

    var checkEmailLanguage = function () {
        $('#form').submit(function (e) {
            if ($('#email_garage').val() != '' && $('#garage_language_select').val() == '') {
                e.preventDefault();
                swal({
                    title: $.i18n._('Garage.Mandatory_to_select_language'),
                    type: "error"
                }).then(function (result) {
                });
            }
            if ($('#email_garage').val() == '' && $('#garage_language_select').val() != '') {
                e.preventDefault();
                swal({
                    title: $.i18n._('Garage.Empty_email'),
                    type: "error"
                }).then(function (result) {
                });
            }
        });

        var label_language = $("label[for='garage_language_select']").text();
        var label_email = $("label[for='email_garage']").text();

        if ($('#email_garage').val() != '' && !$("label[for='garage_language_select']").text().includes('*')) {
            $("label[for='garage_language_select']").append('<span style="color:red"> *</span>');
        } else if ($('#email_garage').val() == '') {
            $("label[for='garage_language_select']").html(label_language);
        }

        $('#email_garage').on('input change', function () {
            if ($('#email_garage').val() != '' && !$("label[for='garage_language_select']").text().includes('*')) {
                $("label[for='garage_language_select']").append('<span style="color:red"> *</span>');
            } else if ($('#email_garage').val() == '') {
                $("label[for='garage_language_select']").html(label_language);
            }
        });

        if ($('#garage_language_select').val() != '' && !$("label[for='email_garage']").text().includes('*')) {
            $("label[for='email_garage']").append('<span style="color:red"> *</span>');
        } else if ($('#garage_language_select').val() == '') {
            $("label[for='email_garage']").html(label_email);
        }

        $('#garage_language_select').on('change', function () {
            if ($('#garage_language_select').val() != '' && !$("label[for='email_garage']").text().includes('*')) {
                $("label[for='email_garage']").append('<span style="color:red"> *</span>');
            } else if ($('#garage_language_select').val() == '') {
                $("label[for='email_garage']").html(label_email);
            }
        });

    }

    return {
        load: function () {
            reOrder();
            sortContacts();
            addBDM();
            addStaff();
            addGeneralBranchManager();
            deleteContactBDM();
            addBDMContact();
            priorityCheckbox();
            checkEmailLanguage();
        }
    }

})();