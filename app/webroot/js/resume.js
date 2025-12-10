$(document).ready(function () {
    Statistic.load();
});

var Statistic = (function () {

    var draw_graph = function () {
        var url = $('#clear-canvas').data('url');
        var request = $.ajax({
            dataType: "json",
            url: url
        });
        request.done(function (data) {
            var current_year = (new Date()).getFullYear();
            var previous_year = current_year - 1;
            var last_year = previous_year - 1;
            $('#clear-canvas').html('<canvas id="myChart"></canvas>');
            var ctx = document.getElementById("myChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months_js,
                    datasets: [
                        {
                            label: 'IAM ' + last_year,
                            data: data[4],
                            backgroundColor: ['rgba(254,117,160,0.3)'],
                            borderColor: ['rgba(254,117,160,1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgba(254,117,160,1)',
                            pointBackgroundColor: 'rgba(254,117,160,1)',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'OE' + last_year,
                            data: data[5],
                            backgroundColor: ['rgba(254,117,160,0.3)'],
                            borderColor: ['rgba(254,117,160,1)'],
                            borderDash: [10, 10],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgba(254,117,160,1)',
                            pointBackgroundColor: 'rgba(254,117,160,1)',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'IAM ' + previous_year,
                            data: data[2],
                            backgroundColor: ['rgba(0,82,255,0.3)'],
                            borderColor: ['rgba(0,82,255,1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgba(0,82,255,1)',
                            pointBackgroundColor: 'rgba(0,82,255,1)',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'OE' + previous_year,
                            data: data[3],
                            backgroundColor: ['rgba(0,82,255,0.3)'],
                            borderColor: ['rgba(0,82,255,1)'],
                            borderDash: [10, 10],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgba(0,82,255,1)',
                            pointBackgroundColor: 'rgba(0,82,255,1)',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'IAM ' + current_year,
                            data: data[0],
                            backgroundColor: ['rgba(59,150,24,0.3)'],
                            borderColor: ['rgba(59,150,24,1)'],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgba(59,150,24,1)',
                            pointBackgroundColor: 'rgba(59,150,24,1)',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'OE '  + current_year,
                            data: data[1],
                            backgroundColor: ['rgba(59,150,24,0.3)'],
                            borderColor: ['rgba(59,150,24,1)'],
                            borderDash: [10, 10],
                            borderWidth: 2,
                            pointHoverBackgroundColor: 'rgba(59,150,24,1)',
                            pointBackgroundColor: 'rgba(59,150,24,1)',
                            pointHoverRadius: 5
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

    return {
        load: function () {
            draw_graph();
        }
    }
})();