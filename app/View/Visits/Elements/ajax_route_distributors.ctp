<?php foreach($distributors as $distributor){ ?>
    <tr
    id="distributor-<?php echo $distributor['Distributor']['id'];?>-added"
    class="cursor-pointer odd distributor_added"
    data-lat="<?php echo $distributor['Distributor']['latitude'];?>"
    data-lng="<?php echo $distributor['Distributor']['longitude'];?>"
    role="row"
    data-id="<?php echo $distributor['Distributor']['id'];?>">
        <td class="c-defecto" data-th="<?php echo __t('Distributor.Account_number');?>">
            <span class="bt-content">
                <?php echo $distributor['Distributor']['account_number'];?>
            </span>
        </td>
        <td class="c-defecto" data-th="<?php echo __t('Appointment.Customer');?>">
            <span class="bt-content">
                <?php echo $distributor['Distributor']['name'];?>
            </span>
        </td>
        <td class="c-defecto" data-th="<?php echo __t('CRM.Postcode');?>">
            <span class="bt-content">
                <?php echo $distributor['Distributor']['postcode'];?>
            </span>
        </td>
        <td class="c-defecto" data-th="<?php echo __t('Visit.City');?>">
            <span class="bt-content">
                <?php echo $distributor['Distributor']['town']; ?>
            </span>
        </td>
        <?php
        $color = null;
        if(is_null($garage['Garage']['last_visit'])){
            $color = 'c-defecto';
        } else if ($garage['Garage']['last_visit'] < date('Y-m-d', strtotime("-3 months"))) {
            $color = 'c-fallo';
        } else if ($garage['Garage']['last_visit'] > date('Y-m-d', strtotime("-2 weeks"))) {
            $color = 'c-exito';
        } else {
            $color = 'c-informacion';
        } ?>
        <td class="<?php echo $color; ?>" data-th="<?php echo __t('CRM.Last_visit');?>">
            <span class="bt-content">
                <?php echo Fecha::toFormatoVistaFecha($distributor['Distributor']['last_visit']);?>
            </span>
        </td>
        <td class="ws-nowrap d-none td-time" data-th="<?php echo __t('Visit.Time');?>" style="display: table-cell;" width="145">
            <span class="bt-content">
                <input type="text" class="timepicker input_time start_time" value="<?php echo $distributor['Distributor']['start_time'];?>" style="width: 61px; margin: 0 !important; float: left;">
                <input type="text" class="timepicker input_time end_time visit_time" value="<?php echo $distributor['Distributor']['end_time'];?>" style="width: 61px; margin: 0 !important; float: right;">
            </span>
        </td>
        <td class="ta-center">
            <a class="remove-distributor" data-id="distributor-<?php echo $distributor['Distributor']['id'];?>-added">
                <span class="aag-icon-papelera c-fallo"></span>
            </a>
        </td>
    </tr>
<?php } ?>