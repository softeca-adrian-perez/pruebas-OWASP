<?php
if ( $trading_groups != null ){
    foreach ( $trading_groups as $trading_group ){
        ?>
        <div class="medium-4 columns end">
            <?php
            $value = false;
            if ( isset($distributor_network) && !empty($distributor_network)){
                $trading_group_tmp = explode(",", $distributor_network[0]['TradingGroupDistributorNetwork']);
                if ( in_array($trading_group['TradingGroup']['id'], $trading_group_tmp) ){
                    $value = true;
                }
            }
            echo $this->Form->input(
                'TradingGroup.' . $trading_group['TradingGroup']['id'] . '.value',
                array(
                    'label' => $trading_group['TradingGroup']['name'],
                    'type' => 'checkbox',
                    'checked' => $value,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <?php
    }
} else {
    echo "<div class='aag-subtitle'>" .  __t('Network.Select_network') . "</div>";
}
?>