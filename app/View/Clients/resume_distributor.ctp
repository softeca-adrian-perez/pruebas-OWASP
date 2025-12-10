<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            __t('CRM.Overview'),
            __t('General.Home'),
        ));
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report">
        <?php echo $this->element('../Clients/Elements/menu_distributor') ?>
    </div>

    <div class="row" data-equalizer>
        <div class="columns medium-4">
            <div class="background-color-primary d-inline-block w-100p" data-equalizer-watch>
                <div class="title-crm-right" >
                    <div class="title-header">
                        <h1 style="font-weight: 100; color: #9EA4B2;">
                            <?php echo __t('Sales.Latest_visit'); ?>
                        </h1>
                    </div>
                    <div class="img-header f-right">
                        <img src="/img/iconos/calendario_lastert.svg">
                    </div>
                </div>
                <div class="columns medium-12 fs-xx-large ta-center">
                    <?php echo $latest_visit;?>
                </div>
                <div class="columns medium-12 fs-small ta-center">
                    <?php echo __t('Appointment.Days_ago');?>
                </div>
                <div class="columns medium-12">
                    <div class="columns medium-6 p-1 ta-right">
                        <div class="fs-small">
                            <?php echo __t('Appointment.Next');?>
                        </div>
                        <div class="border_bottom_blue" style="min-height: 25px;">
                            <strong>
                                <?php echo (isset($distributor_appointment_next['Appointment'])) ? Fecha::toFormatoVistaFecha(h($distributor_appointment_next['Appointment']['date'])) : null;?>
                            </strong>
                        </div>
                    </div>
                    <div class="columns medium-6 p-1  ta-right">
                        <div class="fs-small">
                            <?php echo __t('Appointment.Previous');?>
                        </div>
                        <div class="border_bottom_blue" style="min-height: 25px;">
                            <?php echo ($distributor_appointment_previous) ? Fecha::toFormatoVistaFecha(h($distributor_appointment_previous['Appointment']['date'])) : null;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="columns medium-4">
            <div class="background-color-primary d-inline-block w-100p" data-equalizer-watch>
                <div class="title-crm-right" >
                    <div class="title-header">
                        <h1 style="font-weight: 100; color: #9EA4B2;">
                            <?php echo __t('Sales.Evolution'); ?>
                        </h1>
                    </div>
                    <div class="img-header f-right">
                        <img src="/img/iconos/circle_money.svg">
                    </div>
                </div>
                <?php if($sale_figures_details){ ?>
                    <?php
                    if(isset($sale_figures_details['LV_Month_Change'])){
                        $font = 'c-fallo';
                        if($sale_figures_details['LV_Month_Change'] > 0){
                            $font = 'c-exito';
                        }
                    }
                    else{
                        $font = 'c-default';
                    }
                    ?>
                    <div class="columns medium-12 <?php echo $font?> fs-xx-large ta-center" style="padding-bottom: 20px;">
                        <?php echo (isset($sale_figures_details['LV_Month_Change'])) ? round($sale_figures_details['LV_Month_Change']*100, 2).' %' : '--';?>
                    </div>
                    <div class="columns medium-12">
                        <div class="columns medium-6 p-1 ta-right">
                            <div class="fs-small">
                                <?php echo h($sale_figures_details['Column_Month']);?>
                            </div>
                            <?php
                            if(isset($sale_figures_details['LV_Month'])){
                                $font = 'c-fallo';
                                if($sale_figures_details['LV_Month'] > 0){
                                    $font = 'c-exito';
                                }
                            }
                            else{
                                $font = 'c-default';
                            }
                            ?>
                            <div class="border_bottom_blue" style="min-height: 25px;">
                                <strong class="<?php echo $font?>">
                                    <?php echo number_format(round($sale_figures_details['LV_Month'],2), 2, ',', '.').' &#128';?>
                                </strong>
                            </div>
                        </div>
                        <div class="columns medium-6 p-1  ta-right">
                            <div class="fs-small">
                                <?php echo h($sale_figures_details['Column_Month_PY']);?>
                            </div>
                            <div class="border_bottom_blue" style="min-height: 25px;">
                                <?php echo (isset($sale_figures_details['LV_Month_PY'])) ? number_format(round($sale_figures_details['LV_Month_PY'],2), 2, ',', '.').' &#128' : '--';?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="columns medium-4 end">
            <div class="background-color-primary d-inline-block w-100p" data-equalizer-watch>
                <div class="title-crm-right" >
                    <div class="title-header">
                        <h1 style="font-weight: 100; color: #9EA4B2;">
                            <?php echo __t('Sales.Pilled_up'); ?>
                        </h1>
                    </div>
                    <div class="img-header f-right">
                        <img src="/img/iconos/square_money.svg">
                    </div>
                </div>
                <?php if($sale_figures_details){ ?>
                    <?php
                        if(isset($sale_figures_details['LV_YTD_Change'])){
                            $font = 'c-fallo';
                            if($sale_figures_details['LV_YTD_Change'] > 0){
                                $font = 'c-exito';
                            }
                        }
                        else{
                            $font = 'c-default';
                        }
                    ?>
                    <div class="columns medium-12 <?php echo $font?> fs-xx-large ta-center" style="padding-bottom: 20px;">
                        <?php echo (isset($sale_figures_details['LV_YTD_Change'])) ? round($sale_figures_details['LV_YTD_Change']*100, 2).' %' : '--';?>
                    </div>
                    <div class="columns medium-12">
                        <div class="columns medium-6 p-1 ta-right">
                            <div class="fs-small">
                                <?php echo h($sale_figures_details['Column_YTD']);?>
                            </div>
                            <?php
                            if(isset($sale_figures_details['LV_YTD'])){
                                $font = 'c-fallo';
                                if($sale_figures_details['LV_YTD'] > 0){
                                    $font = 'c-exito';
                                }
                            }
                            else{
                                $font = 'c-default';
                            }
                            ?>
                            <div class="border_bottom_blue" style="min-height: 25px;">
                                <strong class="<?php echo $font?>">
                                    <?php echo number_format(round($sale_figures_details['LV_YTD'],2), 2, ',', '.').' &#128';?>
                                </strong>
                            </div>
                        </div>
                        <div class="columns medium-6 p-1 ta-right">
                            <div class="fs-small">
                                <?php echo h($sale_figures_details['Column_YTD_PY']);?>
                            </div>
                            <div class="border_bottom_blue" style="min-height: 25px;">
                                <?php echo (isset($sale_figures_details['LV_YTD_PY'])) ? number_format(round($sale_figures_details['LV_YTD_PY'],2), 2, ',', '.').' &#128' : '--';?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>