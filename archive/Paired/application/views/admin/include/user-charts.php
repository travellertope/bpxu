
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script>
   
    Highcharts.chart('countryPie', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.0f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: '<?= trans('appointments') ?>',
            colorByPoint: true,
            data: [
            <?php
              foreach ($countries as $country) {
                echo '{
                  name: "'.$country->country_name.' ('. $country->total.')",
                  y: '.$country->total.'
                },';
              }
            ?>
          ]
        }]
    });
  

    var sessionData = <?= $session_data; ?>;
    var sessionAxis = <?= $session_axis; ?>;

    Highcharts.chart('sessionChart', {
        chart: {
            type: 'areaspline'
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: sessionAxis
        },
        yAxis: {
            title: {
                text: ''
            },
            labels: {
                format: '{value}'
            },
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y} <?= trans('booking') ?>'
                }
            }
        },

        tooltip: {
            headerFormat: '<span class="fs-14">{series.name}</span><br>',
            pointFormat: '<span>{point.name}</span> <b>{point.y}</b><br/>'
        },

        series: [
            {
                name: '<?= trans('sessions') ?>',
                data: sessionData,
                color: 'rgb(35, 199, 112, .5)'
            }
        ]
    });
</script>

<script>
    var netData = <?= $net_data; ?>;
    var netAxis = <?= $net_axis; ?>;

    Highcharts.chart('netIncomeChart', {
        chart: {
            type: 'column'
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: netAxis
        },
        yAxis: {
            title: {
                text: ''
            },
            labels: {
                format: '<?php if(settings()->curr_locate == 0){echo html_escape($currency);} ?>{value}<?php if(settings()->curr_locate == 1){echo html_escape($currency);} ?>'
            }
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                pointPadding: 0.4,
                groupPadding: 0,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '<?php if(settings()->curr_locate == 0){echo html_escape($currency);} ?>{point.y}<?php if(settings()->curr_locate == 1){echo html_escape($currency);} ?>'
                }
            }
        },

        tooltip: {
            headerFormat: '<span class="fs-14">{series.name}</span><br>',
            pointFormat: '<span>{point.name}</span> <b><?php if(settings()->curr_locate == 0){echo html_escape($currency);} ?>{point.y}<?php if(settings()->curr_locate == 1){echo html_escape($currency);} ?></b><br/>'
        },

        series: [
            {
                name: '<?= trans('net-income') ?>',
                data: netData,
                color: '#007bff'
            }
        ]
    });


    Highcharts.chart('menteesPie', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.0f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: '<?= trans('appointments') ?>',
            colorByPoint: true,
            data: [
            <?php
              foreach ($mentees as $mentee) {
                echo '{
                  name: "'.$mentee->mentee_name.' ('. $mentee->total.')",
                  y: '.$mentee->total.'
                },';
              }
            ?>
          ]
        }]
    });
</script>
