<div class="menu_garage aag-tabs">
    <ul>
        <li <?php echo $selected == 'conferences' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Conference.Conferences'),
                array(
                    'controller' => 'conferences',
                    'action' => 'home',
                )
            );
            ?>
        </li>
        <li <?php echo $selected == 'delegate' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Delegate.Delegates'),
                array(
                    'controller' => 'conferences_delegates',
                    'action' => 'home',
                )
            );
            ?>
        </li>
    </ul>
</div>