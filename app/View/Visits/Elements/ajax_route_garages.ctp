<?php if(!isset($added)){
    $toggle = 'd-none';
    $class = 'garage_added cursor-pointer';
    $added_str = '';
} else {
    $class = 'cursor-pointer odd garage_added';
    $toggle = '';
}?>

<?php foreach($garages as $garage){ ?>
    <tr
    id="garage-<?php echo $garage['Garage']['id'];?>-added"
    class="<?php echo $class; ?>"
    data-lat="<?php echo $garage['Garage']['latitude'];?>"
    data-lng="<?php echo $garage['Garage']['longitude'];?>"
    data-id="<?php echo $garage['Garage']['id'];?>">
        <td class="c-defecto" data-th="<?php echo __t('Garage.G_number');?>">
            <span class="bt-content">
                <?php echo $garage['Garage']['g_number_id'];?>
            </span>
        </td>
        <td class="c-defecto" data-th="<?php echo __t('Appointment.Customer');?>">
            <span class="bt-content">
                <?php echo $garage['Garage']['name'];?>
            </span>
        </td>
        <td class="c-defecto" data-th="<?php echo __t('Visit.City');?>">
            <span class="bt-content">
                <?php echo $garage['Garage']['town']; ?>
            </span>
        </td>
        <td class="c-defecto" data-th="<?php echo __t('CRM.Postcode');?>">
            <span class="bt-content">
                <?php echo $garage['Garage']['postcode'];?>
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
                <?php echo Fecha::toFormatoVistaFecha($garage['Garage']['last_visit']);?>
            </span>
        </td>
        <td class="ws-nowrap d-none td-time" data-th="<?php echo __t('Visit.Time');?>" style="display: table-cell;" width="145">
            <span class="bt-content">
                <input type="text" class="timepicker input_time start_time" value="<?php echo $garage['Garage']['start_time'];?>" style="width: 61px; margin: 0 !important; float: left;">
                <input type="text" class="timepicker input_time end_time visit_time" value="<?php echo $garage['Garage']['end_time'];?>" style="width: 61px; margin: 0 !important; float: right;">
            </span>
        </td>
        <td class="ta-center">
            <a class="remove-garage" data-id="garage-<?php echo $garage['Garage']['id'];?>-added">
                <span class="aag-icon-papelera c-fallo"></span>
            </a>
        </td>
    </tr>
<?php } ?>