<?php if(isset($added)){
    $class = 'cursor-pointer even distributor_added';
    $id = 'distributor-' . $distributor['Distributor']['id'] . '-added';
    $d_none = '';
} else {
    $d_none = 'd-none';
    $class = 'distributor-result cursor-pointer';
    $id = 'distributor-' . $distributor['Distributor']['id'];
} ?>
<tr class="<?php echo $class; ?>" id="<?php echo $id?>"
    data-lat="<?php echo $distributor['Distributor']['latitude']?>"
    data-lng="<?php echo $distributor['Distributor']['longitude']?>"
    <?php if(isset($added)){ echo 'data-id="' . $distributor['Distributor']['id'] . '"'; } ?>
>
    <?php if(!isset($added)){ ?>
        <td class="check-td">
            <input type="checkbox" class="check-distributor"  data-id="<?php echo $distributor['Distributor']['id']?>">
        </td>
    <?php } ?>
    <td class="c-defecto">
        <?php echo h($distributor['Distributor']['account_number']); ?>
    </td>
    <td class="c-defecto">
        <?php echo h($distributor['Distributor']['name']); ?>
    </td>
    <td class="c-defecto">
        <?php echo h($distributor['Distributor']['postcode']); ?>
    </td>
    <td class="c-defecto">
        <?php echo h($distributor['Distributor']['town']); ?>
    </td>

    <?php
    $color = null;
    if(is_null($distributor['Distributor']['last_visit'])){
        $color = 'c-defecto';
    } else if ($distributor['Distributor']['last_visit'] < date('Y-m-d', strtotime("-6 months"))) {
        $color = 'c-fallo';
    } else if ($distributor['Distributor']['last_visit'] > date('Y-m-d', strtotime("-3 months"))) {
        $color = 'c-exito';
    } else {
        $color = 'c-informacion';
    }

    ?>
    <td class="<?php echo $color?> ta-center">
        <?php echo Fecha::toFormatoVistaFecha(h($distributor['Distributor']['last_visit']));?>
    </td>
    <td class="ws-nowrap <?php echo $d_none; ?> td-time" width="145">
        <input type="text" class="timepicker input_time start_time" value="08:00" style="width: 61px; margin: 0 !important; float: left;">
        <input type="text" class="timepicker input_time end_time visit_time" value="09:30" style="width: 61px; margin: 0 !important; float: right;">
    </td>
    <?php if(isset($added)){ ?>
        <td class="ta-center">
            <a class="remove-distributor" data-id="<?php echo 'distributor-' . $distributor['Distributor']['id'] . '-added'?>">
                <span class="aag-icon-papelera c-fallo"></span>
            </a>
        </td>
    <?php } ?>
</tr>