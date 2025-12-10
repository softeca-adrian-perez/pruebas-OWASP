$(document).ready(function () {
    Dashboard.load();
    $('#tab-garage').on('click', function () {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-garage').show();
        $('#form-distributor').hide();
        $('#form-distributor-msg').hide();
        $(document).foundation();
    });
    $('#tab-distributor').on('click', function () {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-distributor-msg').show();
        $('#form-distributor').show();
        $('#form-garage').hide();
        $(document).foundation();
    });
    $('#tab-assigned').on('click', function () {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-assigned').show();
        $('#form-created').hide();
        $('#form-assigned-group').hide();
        $('#form-assigned-customers').hide();
        $(document).foundation();
    });
    $('#tab-created').on('click', function () {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-created').show();
        $('#form-assigned').hide();
        $('#form-assigned-group').hide();
        $('#form-assigned-customers').hide();
        $(document).foundation();
    });
    $('#tab-assigned-group').on('click', function () {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-assigned-group').show();
        $('#form-assigned').hide();
        $('#form-assigned-customers').hide();
        $('#form-created').hide();
        $(document).foundation();
    });
    $('#tab-assigned-customers').on('click', function () {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-assigned-customers').show();
        $('#form-assigned').hide();
        $('#form-assigned-group').hide();
        $('#form-created').hide();
        $(document).foundation();
    });
});

var Dashboard = (function () {

    var loadTableAssigned = function() {
        $('#sort-due-today').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-today').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned').html(data);
                $('#sort-recently-added').find('span').removeClass('c-primary');
                $('#sort-due-this-week').find('span').removeClass('c-primary');
                $('#sort-due-today').find('span').addClass('c-primary');
                $('#see-all-assigned').attr('href', $('#sort-due-today').data('url-search'));
                loadTableAssigned();
                Tools.loadTr();
            });
        });

        $('#sort-due-this-week').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-this-week').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned').html(data);
                $('#sort-recently-added').find('span').removeClass('c-primary');
                $('#sort-due-this-week').find('span').addClass('c-primary');
                $('#sort-due-today').find('span').removeClass('c-primary');
                $('#see-all-assigned').attr('href', $('#sort-due-this-week').data('url-search'));
                loadTableAssigned();
                Tools.loadTr();
            });
        });

        $('#sort-recently-added').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-recently-added').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned').html(data);
                $('#sort-recently-added').find('span').addClass('c-primary');
                $('#sort-due-this-week').find('span').removeClass('c-primary');
                $('#sort-due-today').find('span').removeClass('c-primary');
                $('#see-all-assigned').attr('href', $('#sort-recently-added').data('url-search'));
                loadTableAssigned();
                Tools.loadTr();
            });
        });
    };    
    
    var loadTableCreated = function() {
        $('#sort-recently-added-created').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-recently-added-created').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-created').html(data);
                $('#sort-recently-added-created').find('span').addClass('c-primary');
                $('#sort-due-this-week-created').find('span').removeClass('c-primary');
                $('#sort-due-today-created').find('span').removeClass('c-primary');
                $('#see-all-created').attr('href', $('#sort-recently-added-created').data('url-search'));
                loadTableCreated();
                Tools.loadTr();
            });
        });
        
        $('#sort-due-today-created').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-today-created').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-created').html(data);
                $('#sort-recently-added-created').find('span').removeClass('c-primary');
                $('#sort-due-this-week-created').find('span').removeClass('c-primary');
                $('#sort-due-today-created').find('span').addClass('c-primary');
                $('#see-all-created').attr('href', $('#sort-due-today-created').data('url-search'));
                loadTableCreated();
                Tools.loadTr();
            });
        });

        $('#sort-due-this-week-created').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-this-week-created').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-created').html(data);
                $('#sort-recently-added-created').find('span').removeClass('c-primary');
                $('#sort-due-this-week-created').find('span').addClass('c-primary');
                $('#sort-due-today-created').find('span').removeClass('c-primary');
                $('#see-all-created').attr('href', $('#sort-due-this-week-created').data('url-search'));
                loadTableCreated();
                Tools.loadTr();
            });
        });
    };

    var loadTableAssignedGroup = function() {
        $('#sort-due-today-group').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-today-group').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned-group').html(data);
                $('#sort-recently-added-group').find('span').removeClass('c-primary');
                $('#sort-due-this-week-group').find('span').removeClass('c-primary');
                $('#sort-due-today-group').find('span').addClass('c-primary');
                $('#see-all-group').attr('href', $('#sort-due-today-group').data('url-search'));
                loadTableAssignedGroup();
                Tools.loadTr();
            });
        });

        $('#sort-due-this-week-group').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-this-week-group').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned-group').html(data);
                $('#sort-recently-added-group').find('span').removeClass('c-primary');
                $('#sort-due-this-week-group').find('span').addClass('c-primary');
                $('#sort-due-today-group').find('span').removeClass('c-primary');
                $('#see-all-group').attr('href', $('#sort-due-this-week-group').data('url-search'));
                loadTableAssignedGroup();
                Tools.loadTr();
            });
        });

        $('#sort-recently-added-group').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-recently-added-group').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned-group').html(data);
                $('#sort-recently-added-group').find('span').addClass('c-primary');
                $('#sort-due-this-week-group').find('span').removeClass('c-primary');
                $('#sort-due-today-group').find('span').removeClass('c-primary');
                $('#see-all-group').attr('href', $('#sort-recently-added-group').data('url-search'));
                loadTableAssignedGroup();
                Tools.loadTr();
            });
        });
    };

    var loadTableAssignedCustomer = function() {
        $('#sort-due-today-customer').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-today-customer').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned-customer').html(data);
                $('#sort-recently-added-customer').find('span').removeClass('c-primary');
                $('#sort-due-this-week-customer').find('span').removeClass('c-primary');
                $('#sort-due-today-customer').find('span').addClass('c-primary');
                $('#see-all-customer').attr('href', $('#sort-due-today-customer').data('url-search'));
                loadTableAssignedCustomer();
                Tools.loadTr();
            });
        });

        $('#sort-due-this-week-customer').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-due-this-week-customer').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned-customer').html(data);
                $('#sort-recently-added-customer').find('span').removeClass('c-primary');
                $('#sort-due-this-week-customer').find('span').addClass('c-primary');
                $('#sort-due-today-customer').find('span').removeClass('c-primary');
                $('#see-all-customer').attr('href', $('#sort-due-this-week-customer').data('url-search'));
                loadTableAssignedCustomer();
                Tools.loadTr();
            });
        });

        $('#sort-recently-added-customer').off('click').on('click',function(event){
            event.preventDefault();
            var url = $('#sort-recently-added-customer').data('url');
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#table-assigned-customer').html(data);
                $('#sort-recently-added-customer').find('span').addClass('c-primary');
                $('#sort-due-this-week-customer').find('span').removeClass('c-primary');
                $('#sort-due-today-customer').find('span').removeClass('c-primary');
                $('#see-all-customer').attr('href', $('#sort-recently-added-customer').data('url-search'));
                loadTableAssignedCustomer();
                Tools.loadTr();
            });
        });
    };

    var loadBehaviour = function () {
        var url_family_sales = $('#cnt_family_sales').data('url');
        var request_family_sales = PeticionAjax.post(url_family_sales);
        request_family_sales.done(function(data) {
            $('#cnt_family_sales').html(data);
            $('.change_rows').hide();
            ajaxFamily();
            loadFamilyMonth();
            loadFamilyYear();
        });

        var url_customer_sales = $('#cnt_customer_sales').data('url');
        var request_customer_sales = PeticionAjax.post(url_customer_sales);
        request_customer_sales.done(function(data) {
            $('#cnt_customer_sales').html(data);
            $('.change_rows').hide();
            ajaxCustomer();
            loadCustomerMonth();
            loadCustomerYear();
        });
    };

    var ajaxFamily = function () {
        $('#cnt_family_sales').find('.paginacion a').off('click').on('click', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var data = {};
            data.search = $('#search_family_sales').val();
            data.month = $('#family_month').val();
            data.year = $('#family_year').val();
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $('#cnt_family_sales').html(data);
                $('.change_rows').hide();
                ajaxFamily();
                loadFamilyMonth();
                loadFamilyYear();
            });
            return false;
        });
    };

    var ajaxCustomer = function () {
        $('#cnt_customer_sales').find('.paginacion a').off('click').on('click', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var data = {};
            data.search = $('#search_customer_sales').val();
            data.month = $('#customer_month').val();
            data.year = $('#customer_year').val();
            var request = PeticionAjax.post(url,data);
            request.done(function(data){
                $('#cnt_customer_sales').html(data);
                $('.change_rows').hide();
                ajaxCustomer();
                loadCustomerMonth();
                loadCustomerYear();
            });
            return false;
        });
    };

    var searchTimerFamily = function () {
        var timer;

        $("#search_family_sales").keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function() {
                var url_family_sales = $('#cnt_family_sales').data('url');
                var data = {};
                data.search = $('#search_family_sales').val();
                data.month = $('#family_month').val();
                data.year = $('#family_year').val();
                var request_family_sales = PeticionAjax.post(url_family_sales,data);
                request_family_sales.done(function(data) {
                    $('#cnt_family_sales').html(data);
                    $('.change_rows').hide();
                    ajaxFamily();
                    loadFamilyMonth();
                    loadFamilyYear();
                });

            }, 750);
        });
    };

    var searchTimerCustomer = function () {
        var timer;

        $("#search_customer_sales").keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function() {
                var url_customer_sales = $('#cnt_customer_sales').data('url');
                var data = {};
                data.search = $('#search_customer_sales').val();
                data.month = $('#customer_month').val();
                data.year = $('#customer_year').val();
                var request_customer_sales = PeticionAjax.post(url_customer_sales,data);
                request_customer_sales.done(function(data) {
                    $('#cnt_customer_sales').html(data);
                    $('.change_rows').hide();
                    ajaxCustomer();
                    loadCustomerMonth();
                    loadCustomerYear();
                });
            }, 750);
        });
    };

    var loadFamilyMonth = function() {
        $('#family_month').off('change').on('change',function(){
            var url_customer_sales = $('#cnt_family_sales').data('url');
            var data = {};
            data.search = $('#search_family_sales').val();
            data.month = $('#family_month').val();
            data.year = $('#family_year').val();
            var request = PeticionAjax.post(url_customer_sales,data);
            request.done(function(data){
                $('#cnt_family_sales').html(data);
                $('.change_rows').hide();
                ajaxFamily();
                loadFamilyMonth();
                loadFamilyYear();
            });
        });
    };

    var loadFamilyYear = function() {
        $('#family_year').off('change').on('change',function(){
            var url_customer_sales = $('#cnt_family_sales').data('url');
            var data = {};
            data.search = $('#search_family_sales').val();
            data.month = $('#family_month').val();
            data.year = $('#family_year').val();
            var request = PeticionAjax.post(url_customer_sales,data);
            request.done(function(data){
                $('#cnt_family_sales').html(data);
                $('.change_rows').hide();
                ajaxFamily();
                loadFamilyMonth();
                loadFamilyYear();
            });
        });
    };

    var loadCustomerMonth = function() {
        $('#customer_month').off('change').on('change',function(){
            var url_customer_sales = $('#cnt_customer_sales').data('url');
            var data = {};
            data.month = $('#customer_month').val();
            data.year = $('#customer_year').val();
            data.search = $('#search_customer_sales').val();
            var request = PeticionAjax.post(url_customer_sales,data);
            request.done(function(data){
                $('#cnt_customer_sales').html(data);
                $('.change_rows').hide();
                ajaxCustomer();
                loadCustomerMonth();
                loadCustomerYear();
            });
        });
    };

    var loadCustomerYear = function() {
        $('#customer_year').off('change').on('change',function(){
            var url_customer_sales = $('#cnt_customer_sales').data('url');
            var data = {};
            data.month = $('#customer_month').val();
            data.year = $('#customer_year').val();
            data.search = $('#search_customer_sales').val();
            var request = PeticionAjax.post(url_customer_sales,data);
            request.done(function(data){
                $('#cnt_customer_sales').html(data);
                $('.change_rows').hide();
                ajaxCustomer();
                loadCustomerMonth();
                loadCustomerYear();
            });
        });
    };

    var createModalTask = function(){
        $('.modal-task').off('click').on('click', function (e) {
            var url = $(this).data('url');
            var bdm_code = $(this).data('bdm-code');

            data = {};
            data.bdm_code = bdm_code;
            
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#modal_task').html(data);
                createModalTask();
                FormHelper.load();
                Select2.load();
                JQueryHelper.load();

            });
        });

        $('#save_submit_create_task').on('click',function(){

            var url = $(this).data('url');
            var data = {};
           
            if( $('#title').val() === '' ) {
                swal($.i18n._('Alert.Error'), $.i18n._('Task.Title_is_required'), "error");
            }
            else if( $('#body').val() === '' ){
                swal($.i18n._('Alert.Error'), $.i18n._('Task.Body_is_required'), "error");
            }
            else {
                var formData = new FormData($('#task-form')[0]);
                var request = $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
					processData: false
                });
            }

            request.done(function(data){
                if( data === false ){
                    swal($.i18n._('Alert.Error'), $.i18n._('General.Error'), "error");
                }
                else if( data === 'error_file' ){
                    swal($.i18n._('Alert.Error'), $.i18n._('Alert.Error_file'), "error");
                }
                else{
                    createModalTask();
                    $('a.close-modal').trigger('click');
                }
                
            })

        });

    };

    return {
        load: function () {
            loadTableAssigned();
            loadTableCreated();
            loadTableAssignedGroup();
            loadTableAssignedCustomer();
            Tools.loadTr();
            createModalTask();
            //loadBehaviour();
            //ajaxFamily();
            //ajaxCustomer();
            //searchTimerFamily();
            //searchTimerCustomer();
        }
    }
})();


