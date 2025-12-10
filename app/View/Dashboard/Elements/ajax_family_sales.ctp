<div class="o-auto graphs" style="margin-top: -32px;">
    <div class="medium-3 f-right m-bottom-1">
        <?php echo $this->Form->input(
            'family_year',
            array(
                'label' => false,
                'type' => 'select',
                'options' => array(
                    date('Y') => date('Y'),
                    date('Y') - 1 => date('Y') - 1,
                    date('Y') - 2 => date('Y') - 2,
                ),
                'class' => 'select2-multiple',
                'empty' => false,
                'default' => $selected_year,
                'id' => 'family_year'
            )
        );?>
    </div>
    <div class="medium-3 f-right m-bottom-1">
        <?php echo $this->Form->input(
            'family_month',
            array(
                'label' => false,
                'type' => 'select',
                'options' => $months,
                'class' => 'select2-multiple',
                'empty' => false,
                'default' => $selected_month,
                'id' => 'family_month'
            )
        );?>
    </div>
    <table class="table-tracking" id="families_sales">
        <thead>
        <tr>
            <th width="100" class="ta-center"><?php echo __t('CRM.Growth'); ?></th>
            <th><?php echo __t('CRM.Families'); ?></th>
            <th width="50"><?php if($selected_month == 0){ $months[$selected_month] = __t('CRM.See_all'); }echo h($months[$selected_month]) . ' ' . ($selected_year - 1)?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($garage_family_sales as $key => $garage_family_sale) {
            $IAM_status = 'success';
            $IAM_color = '#43ac6a';
            $IAM_icon = 'ion-arrow-graph-up-right';
            $OE_status = 'success';
            $OE_color = '#43ac6a';
            $OE_icon = 'ion-arrow-graph-up-right';
            if ($garage_family_sale['GarageSale']['IAM_month_previous_year'] == 0 && $garage_family_sale['GarageSale']['IAM_month_current_year'] == 0) {
                $IAM_percentage = null;
                $IAM_bar = 0;
                $IAM_status = '';
                $IAM_color = '';
                $IAM_icon = '';
            } else if ($garage_family_sale['GarageSale']['IAM_month_previous_year'] == 0) {
                if($garage_family_sale['GarageSale']['IAM_month_current_year'] < 0){
                    $IAM_percentage = null;
                    $IAM_bar = 0;
                    $IAM_status = 'alert';
                    $IAM_color = '#f04124';
                    $IAM_icon = 'ion-arrow-graph-down-right';
                } else {
                    $IAM_percentage = null;
                    $IAM_bar = 100;
                }
            } else if ($garage_family_sale['GarageSale']['IAM_month_current_year'] == 0) {
                $IAM_percentage = null;
                $IAM_bar = 0;
                $IAM_status = 'alert';
                $IAM_color = '#f04124';
                $IAM_icon = 'ion-arrow-graph-down-right';
            } else {
                $IAM_variation = $garage_family_sale['GarageSale']['IAM_month_current_year'] - $garage_family_sale['GarageSale']['IAM_month_previous_year'];
                if ($IAM_variation < 0) {
                    $IAM_status = 'alert';
                    $IAM_color = '#f04124';
                    $IAM_icon = 'ion-arrow-graph-down-right';
                    $IAM_variation = abs($IAM_variation);
                    $IAM_percentage = $IAM_variation / $garage_family_sale['GarageSale']['IAM_month_previous_year'] * 100;
                    $IAM_bar = (100 - $IAM_percentage);
                    $IAM_percentage = '-' . $IAM_percentage;
                } else {
                    $IAM_percentage = $IAM_variation / $garage_family_sale['GarageSale']['IAM_month_previous_year'] * 100;
                    $IAM_bar = 100;
                }
            }
            if ($garage_family_sale['GarageSale']['OE_month_previous_year'] == 0 && $garage_family_sale['GarageSale']['OE_month_current_year'] == 0) {
                $OE_percentage = null;
                $OE_bar = 0;
                $OE_status = '';
                $OE_color = '';
                $OE_icon = '';
            } else if ($garage_family_sale['GarageSale']['OE_month_previous_year'] == 0) {
                if($garage_family_sale['GarageSale']['OE_month_current_year'] < 0){
                    $OE_percentage = null;
                    $OE_bar = 0;
                    $OE_status = 'alert';
                    $OE_color = '#f04124';
                    $OE_icon = 'ion-arrow-graph-down-right';
                } else {
                    $OE_percentage = null;
                    $OE_bar = 100;
                }
            } else if ($garage_family_sale['GarageSale']['OE_month_current_year'] == 0) {
                $OE_percentage = null;
                $OE_bar = 0;
                $OE_status = 'alert';
                $OE_color = '#f04124';
                $OE_icon = 'ion-arrow-graph-down-right';
            } else {
                $OE_variation = $garage_family_sale['GarageSale']['OE_month_current_year'] - $garage_family_sale['GarageSale']['OE_month_previous_year'];
                if ($OE_variation < 0) {
                    $OE_status = 'alert';
                    $OE_color = '#f04124';
                    $OE_icon = 'ion-arrow-graph-down-right';
                    $OE_variation = abs($OE_variation);
                    $OE_percentage = $OE_variation / $garage_family_sale['GarageSale']['OE_month_previous_year'] * 100;
                    $OE_bar = (100 - $OE_percentage);
                    $OE_percentage = '-' . $OE_percentage;
                } else {
                    $OE_percentage = $OE_variation / $garage_family_sale['GarageSale']['OE_month_previous_year'] * 100;
                    $OE_bar = 100;
                }
            }
            ?>
            <tr class="va-top">
                <td class="td-con-porcentaje">
                    <br>
                    <div class="progress background-color-transparent bc-transparent ta-center"
                         style="color: <?php echo $IAM_color ?>">
                        <?php echo __t('Sales.IAM'); ?>
                        <?php if ($IAM_percentage != null) {
                            echo round($IAM_percentage, 2) . '%';
                        } ?>
                        <span class="<?php echo $IAM_icon ?>"></span>
                    </div>
                    <div class="progress background-color-transparent bc-transparent ta-center"
                         style="color: <?php echo $OE_color ?>">
                        <?php echo __t('Sales.OE'); ?>
                        <?php if ($OE_percentage != null) {
                            echo round($OE_percentage, 2) . '%';
                        } ?>
                        <span class="<?php echo $OE_icon ?>"></span>
                    </div>
                </td>
                <td class="td-con-porcentaje">
                    <div class="c-negro"><?php echo $garage_family_sale['SaleFamily']['name' . __s()] ?></div>
                    <div class="progress round <?php echo $IAM_status ?>">
                        <?php if($IAM_bar != 0) { ?>
                            <span class="meter c-blanco ta-center" style="width:  <?php echo $IAM_bar . '%' ?>">
                                        <?php echo round($garage_family_sale['GarageSale']['IAM_month_current_year'], 0) . ' €'; ?>
                                    </span>
                        <?php } else { ?>
                            <span class="d-inline-block ta-center" style="width: 100%">
                                        <?php echo round($garage_family_sale['GarageSale']['IAM_month_current_year'], 0) . ' €'; ?>
                                    </span>
                        <?php } ?>
                    </div>
                    <div class="progress round <?php echo $OE_status ?>">
                        <?php if($OE_bar != 0) { ?>
                            <span class="meter c-blanco ta-center" style="width:  <?php echo $OE_bar . '%' ?>">
                                        <?php echo round($garage_family_sale['GarageSale']['OE_month_current_year'], 0) . ' €'; ?>
                                    </span>
                        <?php } else { ?>
                            <span class="d-inline-block ta-center" style="width:  100%">
                                        <?php echo round($garage_family_sale['GarageSale']['OE_month_current_year'], 0) . ' €'; ?>
                                    </span>
                        <?php } ?>
                    </div>
                </td>
                <td class="td-con-porcentaje">
                    <br>

                    <div class="progress background-color-transparent bc-transparent ta-center c-negro fw-bold"><?php echo round($garage_family_sale['GarageSale']['IAM_month_previous_year'], 0) . ' €'; ?></div>
                    <div class="progress background-color-transparent bc-transparent ta-center c-negro fw-bold"><?php echo round($garage_family_sale['GarageSale']['OE_month_previous_year'], 0) . ' €'; ?></div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>