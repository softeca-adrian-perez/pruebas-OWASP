<ul class="aag-subtabs">
    <li <?php echo $selected == 'credits_networks' ? 'class="active"' : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.Garages'),
            array(
                'controller' => 'garages',
                'action' => 'training_credits',
                $garage_id
            )
        );
        ?>
    </li>
    <li <?php echo $selected == 'credits_movements' ? 'class="active"' : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.Credits_movements'),
            array(
                'controller' => 'garages',
                'action' => 'training_credits_movements',
                $garage_id
            )
        );
        ?>
    </li>
</ul>