<ul class="aag-subtabs">
    <li <?php echo $selected == 'credits_networks' ? 'class="active"' : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.Garages'),
            array(
                'controller' => 'trainings_credits_networks',
                'action' => 'home',
            )
        );
        ?>
    </li>
    <li <?php echo $selected == 'credits_movements' ? 'class="active"' : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.Credits_movements'),
            array(
                'controller' => 'trainings_credits_movements',
                'action' => 'home',
            )
        );
        ?>
    </li>
</ul>