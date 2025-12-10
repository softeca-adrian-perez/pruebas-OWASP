<?php /* @var $this View */ ?>
<div class="widget-alert">
    <div style="height: 288px; overflow: hidden;">
        <table class="tabla-listado-widget">
            <thead>
            <tr>
                <th><?php echo h(__t('Widget.AlertDate'));?></th>
                <th><?php echo h(__t('Widget.AlertText'));?></th>
                <th><?php echo h(__t('Widget.AlertType'));?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($data)
            {
                foreach($data as $alert)
                {
                    $url = $this->Html->url(
                        array(
                            'plugin' => null,
                            'controller' => 'alerts',
                            'action' => 'redirect_alert',
                            $alert['Alert']['id']
                        )
                    );
                    $fecha = mb_substr($alert['Alert']['creation_date'], 0, 10);
                    ?>
                    <tr>
                        <td><?php echo h($fecha);?></td>
                        <td>
                            <?php
                            echo $this->Html->link(
                                $alert['Alert']['text'],
                                array (
                                    'target' => '_blank',
                                )
                            );
                            
                            ?>
                        </td>
                        <td><?php echo h(__t($alert['AlertType']['name']));?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr class="none">
                    <td colspan="3"><?php echo h(__t('Widget.AlertNone'));?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="bottom-links">
        <?php
        echo $this->Html->link (
            __t('Widget.GoToAlerts').'<span class="ion-ios-arrow-thin-right"></span>',
            array(
                'plugin' => null,
                'controller' => 'alerts',
                'action' => 'home',
            ),
            array(
                'class' => 'btn-ver-mas',
                'escape' => false,
            )
        );
        ?>
    </div>
</div>
