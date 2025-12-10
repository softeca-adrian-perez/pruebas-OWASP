<div class="menu_garage aag-tabs">
    <ul>
        <li <?php if( $selected == 'families_configuration'){ echo 'class="active"'; }?>>
            <?php
            echo $this->Html->link(
                __t('Network.Families_configuration'),
                array(
                    'controller' => 'networks',
                    'action' => 'families_configuration',
                    $network_id
                )
            );
            ?>
        </li>
        <li <?php if( $selected == 'recommended_networks'){ echo 'class="active"'; }?>>
            <?php
            echo $this->Html->link(
                __t('Network.Recommended_networks'),
                array(
                    'controller' => 'networks',
                    'action' => 'recommended_networks',
                    $network_id
                )
            );
            ?>
        </li>
		<?php if(CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) { ?>
			<li <?php if( $selected == 'quoting'){ echo 'class="active"'; }?>>
				<?php
				echo $this->Html->link(
					__t('Network.Quoting'),
					array(
						'controller' => 'networks',
						'action' => 'quoting',
						$network_id
					)
				);
				?>
			</li>
            <li <?php if( $selected == 'general_settings'){ echo 'class="active"'; }?>>
                <?php
                echo $this->Html->link(
                    __t('General.Settings'),
                    array(
                        'controller' => 'networks',
                        'action' => 'general_settings',
                        $network_id
                    )
                );
                ?>
            </li>
        <?php } ?>
    </ul>
</div>
