<?php if(!empty($data)){ ?>
    <div id="container_network_evolution<?php if(isset($network_id)){echo '_' . $network_id;}?>">
        <div class="network_evolution_legend">
            <div class="network_evolution_legend_text"><?php echo __t('Widget.Last_12_months');?></div>
            <div class="network_evolution_legend2_legend4">
                <div class="network_evolution_legend_legend2 c-red"><?php echo __t('Widget.Unsubscriptions');?></div>
            </div>
            <div class="network_evolution_legend2_legend3">
                <div class="network_evolution_legend_legend1 c-blue"><?php echo __t('Widget.Subscriptions')?></div>
            </div>
        </div>
        <div class="network_evolution_graph" id="network_evolution_graph<?php if(isset($network_id)){echo '_' . $network_id;}?>"></div>
    </div>
    <script>
        var width_initial;
        if( <?php echo ConstantesSizeWidget::SMALL ?> == <?php echo $w_initial?> ) {
            width_initial  = ($('.container-widgets').width()/2)-40;
        } else {
            width_initial = $('.container-widgets').width()-40;
        }
        var chart = $('#network_evolution_graph<?php if(isset($network_id)){echo '_' . $network_id;}?>').highcharts({
            chart: {
                width: width_initial
            },
            <?php echo $data['graph_options']?>
            xAxis: {
                categories: <?php echo $data['graphic_category']?>
            },
            series: <?php echo $data['graphic_data']?>
        }, function(chart) {

            $('#container_network_evolution<?php if(isset($network_id)){echo '_' . $network_id;}?>').resize(function(){
                var w = $('#container_network_evolution<?php if(isset($network_id)){echo '_' . $network_id;}?>').width();
                chart.setSize(w, chart.chartHeight, false);
            });

        });
    </script>
<?php }?>

