<?php $classActive = 'class="active"'; ?>
<ul class="aag-subtabs">
    <?php
    if(isset($garage_network_id))
    {
        ?>
        <li <?php if ($selected == 'basic_job_settings') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('GarageNetwork.Basic_job_settings'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'basic_job_settings',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <li <?php if ($selected == 'genarts') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('GarageNetwork.Advanced_job_settings'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'genarts',
                    $garage_network_id,
                )
            );
            ?>
        </li>
		<?php if (isset($networkUseFluids) && $networkUseFluids) { ?>
			<li <?php if ($selected == 'fluids') { echo $classActive; }?>>
				<?php
				echo $this->Html->link(
					__t('GarageNetwork.Fluids'),
					array(
						'controller' => 'garages_networks',
						'action' => 'fluids',
						$garage_network_id
					)
				);
				?>
			</li>
        <?php
		}
    }
    ?>
</ul>
