<ul class="aag-subtabs">
    <li class="<?php echo ($active == false) ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            __t('Logs.Logs_changes'),
            array(
                'controller' => 'garages',
                'action' => 'add_admin',
                $garage['Garage']['id']
            )
        );
        ?>
    </li>
    <li class="<?php echo ($active == ConstantsLogType::GARAGE) ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            __t('Garage.Comments'),
            array(
                'controller' => 'garages',
                'action' => 'add_comments',
                $garage['Garage']['id']
            )
        );
        ?>
    </li>
</ul>