$(document).ready(function () {
    DistributorContact.load();
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});

var DistributorContact = (function () {
    selected_item = null;

    var linkContactList = function () {
        $('.link-contact').on('click', function () {
            window.location = $(this).data('url');
        });
    };

    var deleteContactList = function () {
        $('.delete-contact').on('click', function () {
            if ($(this).data('delete') == true) {
                var url = $(this).data('url');
                var list = $('#' + $(this).data('list'));
                swal({
                    title: $(this).data('confirmmsg'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: primary_color,
                    confirmButtonText: $(this).data('yes'),
                    cancelButtonText: $(this).data('no')
                }).then(function (result) {
                    if (result.value) {
                        var request = PeticionAjax.post(url);
                        request.done(function (data) {
                            $('#dashboard-lists').html(data);
                            $('.link-contact').unbind('click');
                            $('.delete-contact').unbind('click');
                            linkContactList();
                            deleteContactList();
                            if ($('#delete_contact_list').val() == '1') {
                                swal({
                                    title: $.i18n._('Contact.Contact_list_used'),
                                    type: "error",
                                    confirmButtonColor: primary_color
                                });
                            }
                        });
                    }
                });
            }
        });
    };

    var searchTime = function () {
        var timer;

        $("#search_contact_list").keyup(function () {
            $("#search_contact").val('');
            clearTimeout(timer);
            timer = setTimeout(function () {
                searchContactList();
            }, 750);
        });

        $("#search_contact").keyup(function () {
            $("#search_contact_list").val('');
            clearTimeout(timer);
            timer = setTimeout(function () {
                searchContact();
            }, 750);
        });

        $('#search_form').keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                search();
            }, 750);
        });

        $('#position_form').on('change', function () {
            search();
        });
    };

    var searchContactList = function () {
        var search_input = $("#search_contact_list").val();
        if (search_input == "") { search_input = 'all' }
        var url = 'search_contact_list/' + search_input;
        var request = PeticionAjax.post(url);
        request.done(function (data) {
            $('#dashboard-lists').html(data);
            $('.link-contact').unbind('click');
            $('.delete-contact').unbind('click');
            linkContactList();
            deleteContactList();
            window.dispatchEvent(new Event('resize'));
        });
    };

    var searchContact = function () {
        var search_input = $("#search_contact").val();
        if (search_input == "") { search_input = 'all' }
        var url = 'search_contact/' + search_input;
        var request = PeticionAjax.post(url);
        request.done(function (data) {
            $('#dashboard-lists').html(data);
            $('.link-contact').unbind('click');
            $('.delete-contact').unbind('click');
            linkContactList();
            deleteContactList();
            window.dispatchEvent(new Event('resize'));
        });
    };

    var search = function () {
        var $search = $("#search_form");
        var $position = $("#position_form");
        var data = {};
        data.contact_name = $search.val();
        data.position_id = $position.val();
        data.contact_list_id = $search.data('id');
        var url = $search.data('url');
        var request = PeticionAjax.post(url, data);
        request.done(function (data) {
            $('#list_form').html(data);
            selectItem();
            sortTableList();
            selected_item = null;
            window.dispatchEvent(new Event('resize'));
        });
    };


    var sortTableList = function () {

        $(function () {
            $("#List1, #List2").sortable(
                {
                    appendTo: 'body',
                    tolerance: 'pointer',
                    connectWith: '#List1, #List2',
                    revert: 'invalid',
                    forceHelperSize: true,
                    helper: 'original',
                    scroll: true
                });
        });
    };

    var sendDataCreateList = function () {
        $('#create-data').on('click', function (e) {
            e.preventDefault();
            if ($('#contact-list-name').val() != '') {
                var url = $(this).data('url');
                var redirect = $(this).data('redirect');
                data = {};
                var $contact_id = [];
                $('#List1').children().each(function () {
                    $contact_id.push($(this).data('id'));
                });
                data.contacts = $contact_id;
                data.name = $('#contact-list-name').val();
                data.color = $('#contact-list-color').val();
                data.global = $('#contact-list-global').prop('checked');
                var request = PeticionAjax.post(url, data);
                request.done(function () {
                    swal({
                        title: $.i18n._('ContactList.Add_success'),
                        type: "success",
                        confirmButtonColor: primary_color
                    }).then(function () {
                        window.location.replace(redirect);
                    });
                });
            } else {
                swal({
                    title: $.i18n._('Contact.List_name_required'),
                    type: "error",
                    confirmButtonColor: primary_color
                });
                $('#contact-list-name').parent().addClass('error');
            }
        });
    };

    var sendDataSaveList = function () {
        $('#save-data').on('click', function (e) {
            e.preventDefault();
            if ($('#contact-list-name').val() != '') {
                var $url = $(this).data('url');
                var list_id = $('#List1').data('id');
                $url = $url + '/' + list_id;
                data = {};
                var $contact_id = [];

                $('#List1').children().each(function () {
                    $contact_id.push($(this).data('id'));
                });
                data.contacts = $contact_id;
                data.name = $('#contact-list-name').val();
                data.color = $('#contact-list-color').val();
                data.global = $('#contact-list-global').prop('checked');
                var request = PeticionAjax.post($url, data);
                request.done(function () {
                    Alertas.show($('#alert-div'), "exito", $.i18n._('ContactList.Save_success'));
                });
            } else {
                Alertas.show($('#alert-div'), "fallo", $.i18n._('Contact.List_name_required'));
                $('#contact-list-name').parent().addClass('error');
            }
        });
    };

    var selectItem = function () {
        var $list1 = $('#List1');
        var $list2 = $('#List2');

        $list1.children().on('click', function () {
            $list1.children().each(function () {
                $(this).removeClass('selected_item');
            });
            $list2.children().each(function () {
                $(this).removeClass('selected_item');
            });

            $(this).addClass('selected_item');
            selected_item = $(this);
        });

        $list2.children().on('click', function () {
            $list1.children().each(function () {
                $(this).removeClass('selected_item');
            });
            $list2.children().each(function () {
                $(this).removeClass('selected_item');
            });

            $(this).addClass('selected_item');
            selected_item = $(this);
        });
    };

    var moveItem = function () {
        $('#move-to-left').on('click', function () {
            if (selected_item != null) {
                if (selected_item.parent().attr('id') == 'List2') {
                    selected_item.detach();
                    $('#List1').prepend(selected_item);
                }
            }
        });
        $('#move-to-right').on('click', function () {
            if (selected_item != null) {
                if (selected_item.parent().attr('id') == 'List1') {
                    selected_item.detach();
                    $('#List2').prepend(selected_item);
                }
            }
        });
    };

    return {
        load: function () {
            linkContactList();
            deleteContactList();
            searchTime();
            sortTableList();
            sendDataSaveList();
            selectItem();
            moveItem();
            sendDataCreateList();
        }
    }
})();