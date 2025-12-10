$(document).ready(function(){
    DistributorContact.load();
    selected_row_bdm = "";
    selected_row_staff = "";
    selected_row_general_branch_manager = "";
});

var DistributorContact = (function(){

    var addBDM = function(){
        $('.status-active-bdm').attr('title', $.i18n._('General.Delete'));
        $('.status-inactive-bdm').attr('title', $.i18n._('General.Add'));
        $('.status-active-bdm').click(function(e){
            selected_row_bdm = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_bdm.attr('data-url')
            });
            request.done(function(data){
                if(!data.message){
                    selected_row_bdm.attr('data-url', '/distributors_contacts_bdm/ajax_add_distributor_contact_bdm/' +
                        selected_row_bdm.data('distributor-id') + "/" +
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
        $('.status-inactive-bdm').click(function(e){
            selected_row_bdm = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_bdm.attr('data-url')
            });
            request.done(function(data){
                if(!data.message){
                    selected_row_bdm.attr('data-url', '/distributors_contacts_bdm/ajax_remove_distributor_contact_bdm/' +
                        selected_row_bdm.data('distributor-id') + "/" +
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

    var addStaff = function(){
        $('.status-active-staff').click(function(e){
            selected_row_staff = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_staff.attr('data-url')
            });
            request.done(function(data){
                if(!data.message){
                    selected_row_staff.attr('data-url', '/distributors_contacts_staff/ajax_add_distributor_contact_staff/' +
                        selected_row_staff.data('distributor-id') + "/" +
                        selected_row_staff.data('contact-id'));
                    selected_row_staff.removeClass('status-active-staff');
                    selected_row_staff.removeClass('c-exito');
                    selected_row_staff.addClass('c-defecto');
                    selected_row_staff.addClass('status-inactive-staff');
                    $('.status-active-staff').unbind();
                    $('.status-inactive-staff').unbind();

                    addStaff();
                }
            });
        });
        $('.status-inactive-staff').click(function(e){
            selected_row_staff = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_staff.attr('data-url')
            });
            request.done(function(data){
                if(!data.message){
                    selected_row_staff.attr('data-url', '/distributors_contacts_staff/ajax_remove_distributor_contact_staff/' +
                        selected_row_staff.data('distributor-id') + "/" +
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

    var addGeneralBranchManager = function(){
        $('.status-active-general-branch-manager').click(function(e){
            selected_row_general_branch_manager = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_general_branch_manager.attr('data-url')
            });
            request.done(function(data){
                if(!data.message){
                    selected_row_general_branch_manager.attr('data-url', '/distributors_contacts_general_branch_manager/ajax_add_distributor_contact_general_branch_manager/' +
                        selected_row_general_branch_manager.data('distributor-id') + "/" +
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
        $('.status-inactive-general-branch-manager').click(function(e){
            selected_row_general_branch_manager = $(e.currentTarget);
            var request = $.ajax({
                type: "POST",
                dataType: "json",
                url: selected_row_general_branch_manager.attr('data-url')
            });
            request.done(function(data){
                if(!data.message){
                    selected_row_general_branch_manager.attr('data-url', '/distributors_contacts_general_branch_manager/ajax_remove_distributor_contact_general_branch_manager/' +
                        selected_row_general_branch_manager.data('distributor-id') + "/" +
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

    return {
        load: function(){
            addBDM();
            addStaff();
            addGeneralBranchManager();
        }
    }

})();