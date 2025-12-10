<?php $active = 'class="active"'; ?>
<div class="aag-tabs">
    <ul>
        <?php
        // Admin role can see training providers
        if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::ADMIN, ConstantsRoles::SUPER_ADMIN))) {
        ?>
            <li <?php echo $selected == 'training' ? $active : ''; ?>>
                <?php echo $this->Html->link(__t('Training.Training'), array('controller' => 'trainings_providers', 'action' => 'home')); ?>
            </li>
        <?php
        }
        // Network access without Admin role can access training list
        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE_NETWORK_MANAGER) {
        ?>
            <li <?php echo $selected == 'training' ? $active : ''; ?>>
                <?php echo $this->Html->link(__t('Training.Training'), array('controller' => 'trainings_courses', 'action' => 'home')); ?>
            </li>
        <?php
        }
        ?>
        <li <?php echo $selected == 'credits' ? $active : ''; ?>>
            <?php echo $this->Html->link(__t('Training.Credits'), array('controller' => 'trainings_credits_networks', 'action' => 'home')); ?>
        </li>
    </ul>
</div>