$(document).ready(function () {
    Statistics.load();
});

var Statistics = (function () {

    var loadBehaviour = function () {
        $(".fecha-js.from-js").on('change',function(){
            $(".fecha-js.max-stats-js").each(function(){
                $(this).datepicker("option", "maxDate", moment($(".fecha-js.from-js").val(), "DD-MM-YYYY").add(1,'year').add(-1,'month').endOf('month').format('DD-MM-YYYY'));
            });
        });

        if($('#network_id').val() != ""){
            $('#trading_group_id').prop('disabled',true);
        } else if($('#trading_group_id').val() != ""){
            $('#network_id').prop('disabled',true);
        }

        $('#network_id').on('change', function(){
            if($(this).val() == ""){
                $('#trading_group_id').prop('disabled',false);
            } else {
                $('#trading_group_id').prop('disabled',true);
            }
        });
        $('#trading_group_id').on('change', function(){
            if($(this).val() == ""){
                $('#network_id').prop('disabled',false);
            } else {
                $('#network_id').prop('disabled',true);
            }
        });

        var maximum = null;
        var maximum_element = null;

        $('.month_connections').each(function() {
            var value = parseInt($(this).data('connection'));
            maximum_element = (value > maximum) ? $(this) : maximum_element;
            maximum = (value > maximum) ? value : maximum;
        });
        if(maximum_element != null){
            maximum_element.addClass('c-exito');
        }

        if(maximum_element != null){
            $('.month_connections').each(function() {
                if(parseInt(maximum_element.data('connection')) == parseInt($(this).data('connection'))){
                    $(this).addClass('c-exito');
                }
            });
        }

    };

    var loadDataTables = function(){
        if ($.fn.dataTableExt !== undefined) {
            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
            'locale-compare-asc': function ( a, b ) {
                return a.localeCompare(b, 'cs', { sensitivity: 'case' })
            },
            'locale-compare-desc': function ( a, b ) {
                return b.localeCompare(a, 'cs', { sensitivity: 'case' })
            }
            })

            jQuery.fn.dataTable.ext.type.search['locale-compare'] = function (data) {
                return NeutralizeAccent(data);
            }
        }


        $('#cnt_connections table').DataTable({
            oLanguage: {
                sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            columnDefs : [
                { targets: [0,1,3,4], type: 'locale-compare' },
            ],
            order: [[ 0, "asc" ]]
        });
        setTimeout(function(){
            $('#cnt_connections table tbody').removeClass('d-none');
            filterCorrectly( $('#cnt_connections') );
        }, 400);

        $('#cnt_sections table').DataTable({
            oLanguage: {
                sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            columnDefs : [
                { targets: 0, type: 'locale-compare' },
            ],
            order: [[ 0, "asc" ]]
        });
        setTimeout(function(){
            $('#cnt_sections table tbody').removeClass('d-none');
            filterCorrectly( $('#cnt_sections') );
        }, 400);

        $('#cnt_articles table').DataTable({
            oLanguage: {
                sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            order: [[ 0, "asc" ]]
        });
        setTimeout(function(){
            $('#cnt_articles table tbody').removeClass('d-none');
            filterCorrectly( $('#cnt_articles') );
        }, 400);

        $('#cnt_bdms table').DataTable({
            aoColumnDefs: {
                sType: 'numeric', aTargets: [ 1,2 ]
            },
            oLanguage: {
                sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
            },
            columnDefs : [
                {
                    targets: [0], type: 'locale-compare',
                },

            ],
            autoWidth: false,
            deferRender: true,
            order: [[ 0, "asc" ]],
            columns: [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ]
        });
        setTimeout(function(){
            $('#cnt_bdms table tbody').removeClass('d-none');
            filterCorrectly( $('#cnt_bdms') );
        }, 400);
    }

    var loadGarageTable = function(){
        $('#load-garages-js').on('click',function(){
            let columnsConfig = [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                { "visible": false },
                { "orderData" : 8, "targets": 9},
                { "visible": false },
                { "orderData" : 10, "targets": 11},
            ];

            if (!$('.has_g_number-js').data('has-g-number')) {
                columnsConfig.shift()
            }
            var url = $(this).data('url');
            PeticionAjax.mostrarCargando();
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#cnt_garages').html(data);
                $('#cnt_garages table').DataTable({
                    oLanguage: {
                        sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
                    },
                    columnDefs : [
                        {
                            targets: [0], type: 'locale-compare',
                        },

                    ],
                    processing: true,
                    autoWidth: false,
                    deferRender: true,
                    order: [[ 7, "desc" ]],
                    columns: columnsConfig,
                    paging: true,
                    scrollY: "600px",
                    stateSave: true,
                    scroller: {
                        loadingIndicator: true,
                        displayBuffer: 10
                    }
                });
                setTimeout(function(){
                    $('#cnt_garages table tbody').removeClass('d-none');
                    filterCorrectly( $('#cnt_garages') );
                }, 400);
            });
            $(this).remove();
        });

    }

    var loadDistributorTable = function(){
        $('#load-distributors-js').on('click',function(){
            var url = $(this).data('url');
            PeticionAjax.mostrarCargando();
            var request = PeticionAjax.post(url);
            request.done(function(data){
                $('#cnt_distributors').html(data);
                $('#cnt_distributors table').DataTable({
                    // aoColumnDefs: {
                    //     sType: 'numeric', aTargets: [ 1,2 ]
                    // },
                    oLanguage: {
                        sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
                    },
                    columnDefs : [
                        {
                            targets: [0], type: 'locale-compare',
                        },

                    ],
                    autoWidth: false,
                    deferRender: true,
                    order: [[ 6, "desc" ]],
                    columns: [
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        { "visible": false },
                        { "orderData" : 7, "targets": 8},
                        { "visible": false },
                        { "orderData" : 9, "targets": 10},
                    ]
                });
                setTimeout(function(){
                    $('#cnt_distributors table tbody').removeClass('d-none');
                    filterCorrectly( $('#cnt_distributors') );
                }, 400);
            });
            $(this).remove();
        });
    }

    var loadChartSections = function(){
        data = {
            datasets: [
                {
                    data: users_statistics_sections,
                    backgroundColor: _colors()
                }
            ],
            labels: users_statistics_sections_names
        };

        var ctx = document.getElementById("chartSections").getContext('2d');
        chartSections = new Chart(ctx,{
            type: 'pie',
            data: data,
            options: {
                maintainAspectRatio: false,
                legend: { position: 'top' }
            }
        });
    }

    var loadChartArticles = function(){
        data = {
            datasets: [
                {
                    data: users_statistics_articles,
                    backgroundColor: _colors()
                }
            ],
            labels: users_statistics_articles_names
        };

        var ctx = document.getElementById("chartArticles").getContext('2d');
        chartArticles = new Chart(ctx,{
            type: 'pie',
            data: data,
            options: {
                maintainAspectRatio: false,
                legend: { position: 'right' }
            }
        });
    }

    var loadChartDevices = function(){
        data = {
            datasets: [
                {
                    data: device_connections,
                    backgroundColor: _colors()
                }
            ],
            labels: [
                $.i18n._('Statistics.Desktop'),
                $.i18n._('Statistics.Mobile'),
                $.i18n._('Statistics.Tablet')
            ]
        };


        var ctx = document.getElementById("chartDevices").getContext('2d');
        chartDevices = new Chart(ctx,{
            type: 'pie',
            data: data,
            options: {
                maintainAspectRatio: false,
                legend: { position: 'top' }
            }
        });

    }

    var _colors = function(){
        var colors = ['#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080', '#ffffff', '#000000']

        return colors;
    }

    function filterCorrectly(element){
        var filterVal = '';
        $(element).find('input').unbind();
        $(element).find('input').on('keyup change', function(){
        var val = NeutralizeAccent(this.value);
        if(val !== filterVal){
                filterVal = val;
                $(element).find('table').DataTable().search(val).draw();
            }
        });
    }

    function NeutralizeAccent(data){
      return !data
          ? ''
            : typeof data === 'string'
            ? data
            .replace(/\n/g, ' ')
            .replace(/[éÉěĚèêëÈÊË]/g, 'e')
            .replace(/[šŠ]/g, 's')
            .replace(/[čČçÇ]/g, 'c')
            .replace(/[řŘ]/g, 'r')
            .replace(/[žŽ]/g, 'z')
            .replace(/[ýÝ]/g, 'y')
            .replace(/[áÁâàÂÀ]/g, 'a')
            .replace(/[íÍîïÎÏ]/g, 'i')
            .replace(/[ťŤ]/g, 't')
            .replace(/[ďĎ]/g, 'd')
            .replace(/[ňŇ]/g, 'n')
            .replace(/[óÓ]/g, 'o')
            .replace(/[úÚůŮ]/g, 'u')
            : data
    }

    return {
        load: function () {
            loadBehaviour();
            loadDataTables();
            loadChartDevices();
            loadChartSections();
            // loadChartArticles();
            loadGarageTable();
            loadDistributorTable();
        }
    }
})();