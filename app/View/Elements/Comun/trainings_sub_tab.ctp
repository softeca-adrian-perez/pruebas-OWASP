<?php $active = 'class="active"'; ?>
<ul class="aag-subtabs">
    <?php if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::ADMIN, ConstantsRoles::SUPER_ADMIN))) {  ?>
        <li <?php echo $selected == 'trainings_providers' ? $active : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Training.Trainings_providers'),
                array(
                    'controller' => 'trainings_providers',
                    'action' => 'home',
                )
            );
            ?>
        </li>
        <li <?php echo $selected == 'trainers' ? $active : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Training.Trainers'),
                array(
                    'controller' => 'trainings_trainers',
                    'action' => 'home',
                )
            );
            ?>
        </li>
    <?php } ?>
    <li <?php echo $selected == 'list_of_courses' ? $active : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.List_of_courses'),
            array(
                'controller' => 'trainings_courses',
                'action' => 'home',
            )
        );
        ?>
    </li>
    <li <?php echo $selected == 'planned_courses' ? $active : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.Planned_courses'),
            array(
                'controller' => 'trainings_planned_courses',
                'action' => 'home',
            )
        );
        ?>
    </li>
    <li <?php echo $selected == 'list_delegates' ? $active : ''; ?>>
        <?php
        echo $this->Html->link(
            __t('Training.List_delegates'),
            array(
                'controller' => 'trainings_list_delegates',
                'action' => 'home',
            )
        );
        ?>
    </li>
</ul>