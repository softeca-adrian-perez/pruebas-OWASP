<?php if(!isset($added)){
    $toggle = 'd-none';
    $class = 'garage-result cursor-pointer';
    $added_str = '';
} else {
    $class = 'cursor-pointer odd garage_added';
    $toggle = '';
    $added_str = '-added';
}?>

<?php
$userAagRegionId=CakeSession::read('Auth.User.aag_region_id');
$userRole=CakeSession::read('Auth.User.role_id');
?>

<tr
    class="<?php echo $class; ?>"
    id="garage-<?php echo $garage['Garage']['id'] . $added_str;?>"
    data-lat="<?php echo $garage['Garage']['latitude']?>"
    data-lng="<?php echo $garage['Garage']['longitude']?>"
    <?php if(isset($added)){ echo 'data-id="' . $garage['Garage']['id'] . '"'; } ?>
>
    <?php if(!isset($added)){ ?>
        <td class="check-td">
            <input type="checkbox" class="check-garage" data-id="<?php echo $garage['Garage']['id']?>">
        </td>
    <?php } ?>
    <?php if ((isset($userAagRegionId) && ($userAagRegionId != ConstantsAAGRegionId::BENELUX)) || ($userRole == ConstantsRoles::SUPER_ADMIN)) { ?>
        <td class="c-defecto">
        <?php echo h($garage['Garage']['g_number_id']); ?>
        </td>
    <?php } ?>
    <td class="c-defecto">
        <?php echo h($garage['Garage']['name']); ?>
    </td>
    <td class="c-defecto">
        <?php echo h($garage['Garage']['postcode']); ?>
    </td>
    <td class="c-defecto">
        <?php echo h($garage['Garage']['town']); ?>
    </td>

    <?php
    $color = null;
    if(is_null($garage['Garage']['last_visit'])){
        $color = 'c-defecto';
    } else if ($garage['Garage']['last_visit'] < date('Y-m-d', strtotime("-6 months"))) {
        $color = 'c-fallo';
    } else if ($garage['Garage']['last_visit'] > date('Y-m-d', strtotime("-3 months"))) {
        $color = 'c-exito';
    } else {
        $color = 'c-informacion';
    }

    ?>
    <td class="<?php echo $color?> ta-center">
        <?php echo Fecha::toFormatoVistaFecha(h($garage['Garage']['last_visit'])); ?>
    </td>
    <td class="ws-nowrap <?php echo $toggle; ?> td-time" width="145">
        <input type="text" class="timepicker input_time start_time" value="08:00" style="width: 61px; margin: 0 !important; float: left;">
        <input type="text" class="timepicker input_time end_time visit_time" value="09:30" style="width: 61px; margin: 0 !important; float: right;">
    </td>
    <?php if(isset($added)){ ?>
        <td class="ta-center">
            <a class="remove-garage" data-id="<?php echo 'garage-' . $garage['Garage']['id'] . '-added'?>">
                <span class="aag-icon-papelera c-fallo"></span>
            </a>
        </td>
    <?php } ?>
</tr>