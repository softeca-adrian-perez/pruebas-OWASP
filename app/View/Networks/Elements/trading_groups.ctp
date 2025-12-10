<div class="cnt-form-inputs-max-width p-vertical-1">
    <?php
    if($trading_groups != null)
    {
        foreach($trading_groups as $trading_group)
        {
            ?>
            <div>
                <?php
                $value = false;
                if(isset($network) && !empty($network))
                {
                    $trading_group_tmp = explode(",", $network[0]['TradingGroupNetwork']);
                    if(in_array($trading_group['TradingGroup']['id'], $trading_group_tmp)) { $value = true; }
                }
                echo $this->Form->input(
                    'TradingGroup.' . $trading_group['TradingGroup']['id'] . '.value',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => $trading_group['TradingGroup']['name'],
                        'type' => 'checkbox',
                        'checked' => $value
                    )
                );
                ?>
            </div>
            <?php
        }
    }
    else
    {
        echo "<div><h2>" .  __t('Network.Select_network') . "</h2></div>";
    }
    ?>
</div>