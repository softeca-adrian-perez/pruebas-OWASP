<div class="menu_garage aag-tabs">
    <ul>
        <li <?php echo $selected == 'visited' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Appointment.Visited'),
                array(
                    'controller' => 'appointments_objectives',
                    'action' => 'reporting',
                )
            );
            ?>
        </li>
        <li <?php echo $selected == 'not_visited' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Appointment.Not_visited'),
                array(
                    'controller' => 'distributors_objectives',
                    'action' => 'not_visited',
                )
            );
            ?>
        </li>
    </ul>
</div>