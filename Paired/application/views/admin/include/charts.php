
  
<!-- high charts js-->
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
  <?php if(is_admin()): ?>
    var incomeData = <?= $income_data; ?>;
    var incomeAxis = <?= $income_axis; ?>;
    var chartbg = '<?php if(site_mode() == 'dark'){echo "#2B3035";}else{echo "#fff";} ?>';
    var chart_title_color = '<?php if(site_mode() == 'dark'){echo "#ddd";}else{echo "#2B3035";} ?>';


    Highcharts.chart('adminIncomeChart', {
        chart: {
            backgroundColor: chartbg,
            type: 'areaspline'
        },
        credits: {
            enabled: false
        },
        title: {
            text: '',
            style: {
                color: chart_title_color // Set the title color to white
            }
        },
        xAxis: {
            labels: {
                style: {
                    color: chart_title_color // Set x-axis label color
                }
            },
            categories: incomeAxis
        },
        yAxis: {
            title: {
                text: ''
            },
            labels: { 
                style: {
                    color: chart_title_color // Set x-axis label color
                },
                format: '<?php if(settings()->curr_locate == 0){echo html_escape($currency);} ?>{value} <?php if(settings()->curr_locate == 1){echo html_escape($currency);} ?>'
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
                    format: '<?php if(settings()->curr_locate == 0){echo html_escape($currency);} ?>{point.y} <?php if(settings()->curr_locate == 1){echo html_escape($currency);} ?>'
                }
            }
        },

        tooltip: {
            headerFormat: '<span class="fs-14">{series.name}</span><br>',
            pointFormat: '<span>{point.name}</span> <b><?php echo html_escape($currency) ?>{point.y}</b><br/>'
        },

        series: [
            {
                name: '<?= trans('income') ?>',
                data: incomeData,
                color: '#2568ef'
            }
        ]
    });


    
  <?php endif; ?>



  <?php if(is_user()): ?>
    var incomeData = <?= $income_data; ?>;
    var incomeAxis = <?= $income_axis; ?>;
    var chartbg = '<?php if(site_mode() == 'dark'){echo "#2B3035";}else{echo "#fff";} ?>';
    var chart_title_color = '<?php if(site_mode() == 'dark'){echo "#ddd";}else{echo "#2B3035";} ?>';

    Highcharts.chart('userIncomeChart', {
        chart: {
            backgroundColor: chartbg,
            type: 'areaspline'
        },
        title: {
            text: '',
            style: {
                color: chart_title_color // Set the title color to white
            }
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 150,
            y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
        },
        xAxis: {
            labels: {
                style: {
                    color: chart_title_color // Set x-axis label color
                }
            },
            categories: incomeAxis
        },
        yAxis: {
            title: {
                text: ''
            },
            labels: {
                style: {
                    color: chart_title_color // Set x-axis label color
                },
                format: '<?php if($this->business->curr_locate == 0){echo html_escape($currency);} ?>{value} <?php if($this->business->curr_locate == 1){echo html_escape($currency);} ?>'
            },
        },
        tooltip: {
            headerFormat: '<span class="fs-14">{series.name}</span><br>',
            pointFormat: '<span>{point.name}</span> <b><?php if($this->business->curr_locate == 0){echo html_escape($currency);} ?>{point.y} <?php if($this->business->curr_locate == 1){echo html_escape($currency);} ?></b><br/>'
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.2,
                dataLabels: {
                    enabled: true,
                    format: '<?php if($this->business->curr_locate == 0){echo html_escape($currency);} ?>{point.y} <?php if($this->business->curr_locate == 1){echo html_escape($currency);} ?>'
                }
            }
        },
        series: [{
            name: '<?php echo trans('income') ?>',
            data: incomeData,
            color: 'rgb(35, 199, 112)'
        }]
    });


  <?php endif; ?>

</script>
