$(document).ready(function () {
    Statistic.load();
});

var Statistic = (function () {

    var loadBehaviour = function () {
        $('#families_month').DataTable({
            oLanguage: {
                sUrl: "../../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            iDisplayLength: 5,
            order: [[1, "asc"]],
            columnDefs: [{orderable: false, targets: [0, 2]}],
            fnInitComplete: function() {
                $('.dataTables_length').hide();
                $('.dataTables_filter').find('label').css({"float": "left"});
                $(document).foundation('reflow');
            }
        });
        $('#families_annual').DataTable({
            oLanguage: {
                sUrl: "../../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            iDisplayLength: 5,
            order: [[1, "asc"]],
            columnDefs: [{orderable: false, targets: [0, 2]}],
            fnInitComplete: function() {
                $('.dataTables_length').hide();
                $('.dataTables_filter').css({"float": "left"});
                $(document).foundation('reflow');
            }
        });

    };

    var draw_graph = function () {
        var url = $('#clear-canvas').data('url');
        var request = $.ajax({
            dataType: "json",
            url: url,
            data: {current_year: $('#month_stats').val()}
        });
        request.done(function (data) {
            $('#clear-canvas').html('<canvas id="myChart"></canvas>');
            $('#myChart').addClass('unselectable');
            var ctx = document.getElementById("myChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months_js,
                    datasets: [
                        {
                            label: 'IAM previous year',
                            data: data[2],
                            backgroundColor: ['rgba(254, 117, 160, 0.3)'],
                            borderColor: ['rgba(254, 117, 160, 1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgb(254, 117, 160)',
                            pointBackgroundColor: 'rgb(254, 117, 160)'
                        },
                        {
                            label: 'IAM',
                            data: data[0],
                            backgroundColor: ['rgba(255, 105, 0, 0.3)'],
                            borderColor: ['rgba(255, 105, 0,1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgb(255, 105, 0)',
                            pointBackgroundColor: 'rgb(255, 105, 0)'
                        },
                        {
                            label: 'OE previous year',
                            data: data[3],
                            backgroundColor: ['rgba(0, 209, 255, 0.3)'],
                            borderColor: ['rgba(0,209,255,1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgb(0, 209, 255)',
                            pointBackgroundColor: 'rgb(0, 209, 255)'
                        },
                        {
                            label: 'OE',
                            data: data[1],
                            backgroundColor: ['rgba(0, 82, 255, 0.3)'],
                            borderColor: ['rgba(0,82,255,1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgb(0, 82, 255)',
                            pointBackgroundColor: 'rgb(0, 82, 255)'
                        }
                    ]
                },
                options: {
                    tooltips: {
                        mode: 'nearest',
                        intersect: false
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            });
        });
    };

    var familySales = function () {
        $('#family_sales').off('change').on('change', function () {
            var $family_sales = $('#family_sales');
            var family_val = $family_sales.val();
            var $family_sales_year = $('#family_sales_year');
            var family_val_year = $family_sales_year.val();
            var url = $('#cnt_families_sales').data('url');
            var data = {};
            data.current_month = family_val;
            data.current_year = family_val_year;
            data.garage_id = $('#garage_id').val();
            var request = PeticionAjax.post(url,data);
            request.done(function (data) {
                $('#cnt_families_sales').html(data);
                $('#families_month').DataTable({
                    oLanguage: {
                        sUrl: "../../js/lib/dataTables/locale/" + language_code + ".json"
                    },
                    autoWidth: false,
                    deferRender: true,
                    iDisplayLength: 5,
                    order: [[1, "asc"]],
                    columnDefs: [{orderable: false, targets: [0, 2]}],
                    fnInitComplete: function() {
                        $('.dataTables_length').hide();
                        $('.dataTables_filter').find('label').css({"float": "left"});
                        $('#family_sales').select2();
                        $('#family_sales').val(family_val).trigger('change');
                        $('#family_sales_year').select2();
                        $('#family_sales_year').val(family_val_year).trigger('change');
                        familySales();
                        $(document).foundation('reflow');
                        $('#month-header').html($('#family_sales option:selected').text());
                        $('#year-header').html(Number($('#family_sales_year').val()) - 1);
                    }
                });
            });
        });
        $('#family_sales_year').off('change').on('change', function () {
            var $family_sales = $('#family_sales');
            var family_val = $family_sales.val();
            var $family_sales_year = $('#family_sales_year');
            var family_val_year = $family_sales_year.val();
            var url = $('#cnt_families_sales').data('url');
            var data = {};
            data.current_month = family_val;
            data.current_year = family_val_year;
            data.garage_id = $('#garage_id').val();
            var request = PeticionAjax.post(url,data);
            request.done(function (data) {
                $('#cnt_families_sales').html(data);
                $('#families_month').DataTable({
                    oLanguage: {
                        sUrl: "../../js/lib/dataTables/locale/" + language_code + ".json"
                    },
                    autoWidth: false,
                    deferRender: true,
                    iDisplayLength: 5,
                    order: [[1, "asc"]],
                    columnDefs: [{orderable: false, targets: [0, 2]}],
                    fnInitComplete: function() {
                        $('.dataTables_length').hide();
                        $('.dataTables_filter').find('label').css({"float": "left"});
                        $('#family_sales').select2();
                        $('#family_sales').val(family_val).trigger('change');
                        $('#family_sales_year').select2();
                        $('#family_sales_year').val(family_val_year).trigger('change');
                        familySales();
                        $(document).foundation('reflow');
                        $('#month-header').html($('#family_sales option:selected').text());
                        $('#year-header').html(Number($('#family_sales_year').val()) - 1);
                    }
                });
            });
        });
    };

    var annualFamilySales = function () {
        $('#annual_family_sales').off('change').on('change', function () {
            var $annual_family_sales = $('#annual_family_sales');
            var annual_family_val = $annual_family_sales.val();
            var url = $('#cnt_annual_family_sales').data('url');
            var data = {};
            data.current_year = annual_family_val;
            data.garage_id = $('#garage_id').val();
            var request = PeticionAjax.post(url,data);
            request.done(function (data) {
                $('#cnt_annual_family_sales').html(data);
                $('#families_annual').DataTable({
                    oLanguage: {
                        sUrl: "../../js/lib/dataTables/locale/" + language_code + ".json"
                    },
                    autoWidth: false,
                    deferRender: true,
                    iDisplayLength: 5,
                    order: [[1, "asc"]],
                    columnDefs: [{orderable: false, targets: [0, 2]}],
                    fnInitComplete: function() {
                        $('.dataTables_length').hide();
                        $('.dataTables_filter').find('label').css({"float": "left"});
                        $('#annual_family_sales').select2();
                        $('#annual_family_sales').val(annual_family_val).trigger('change');
                        annualFamilySales();
                        $(document).foundation('reflow');
                        $('#year-head').html(Number($('#annual_family_sales').val() - 1));
                    }
                });
            });
        });
    };

    var monthStats = function () {
        $('#month_stats').off('change').on('change', function () {
            var $month_stats = $('#month_stats');
            var month_stats = $month_stats.val();
            var url = $('#cnt_month_stats').data('url');
            var data = {};
            data.current_year = month_stats;
            data.garage_id = $('#garage_id').val();
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#cnt_month_stats').html(data);
                $('#month_stats').select2();
                $('#month_stats').val(month_stats).trigger('change');
                monthStats();
                draw_graph();
            });
        });
    };

    return {
        load: function () {
            loadBehaviour();
            draw_graph();
            familySales();
            annualFamilySales();
            monthStats();

        }
    }
})();