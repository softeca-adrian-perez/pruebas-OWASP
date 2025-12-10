<div class="aag-tabs">
    <ul>
        <li class="<?php echo ($active == false) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                __t('Contact.Contacts'),
                array(
                    'controller' => 'contacts',
                    'action' => 'home'
                ),
                array('escape' => false)
            );
            ?>
        </li>
        <li class="<?php echo ($active == ConstantsLogType::CONTACT) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                __t('Contact.Logs_contacts'),
                array(
                    'controller' => 'contacts',
                    'action' => 'logs_contact'
                ),
                array('escape' => false)
            );
            ?>
        </li>
        <li class="<?php echo ($active == ConstantsLogType::GROUP_PERMISSION) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                __t('Contact.Permission_group_log'),
                array(
                    'controller' => 'contacts',
                    'action' => 'log_group_permissions'
                ),
                array('escape' => false)
            );
            ?>
        </li>
        <li class="<?php echo ($active == ConstantsLogType::PERMISSION) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                __t('Contact.Position_log'),
                array(
                    'controller' => 'contacts',
                    'action' => 'log_positions'
                ),
                array('escape' => false)
            );
            ?>
        </li>
</div>