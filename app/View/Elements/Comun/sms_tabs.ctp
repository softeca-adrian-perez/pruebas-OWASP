<div class="menu_garage aag-tabs">
    <ul>
        <li <?php echo $selected == 'sms' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Sms.Sms_list'),
                array(
                    'controller' => 'sms',
                    'action' => 'list',
                )
            );
            ?>
        </li>
        <li <?php echo $selected == 'shortner_url_log' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Sms.Url_shortner_configuration'),
                array(
                    'controller' => 'shortner_url_log',
                    'action' => 'home',
                )
            );
            ?>
        </li>
    </ul>
</div>