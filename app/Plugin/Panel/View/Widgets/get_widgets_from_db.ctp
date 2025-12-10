<ul>
    <?php foreach ($widgets as $widget) {?>
        <li data-widget_id="<?php echo $widget['PanelWidget']['id'];?>" data-row="<?php echo $widget['PanelWidgetUser']['row']?>" data-col="<?php echo $widget['PanelWidgetUser']['col']?>" data-sizex="<?php echo $widget['PanelWidgetUser']['size_x']?>" data-sizey="1" data-max-sizex="2" data-max-sizey="1">
            <div class="individual-container-widget">
                <span class="ion-ios-close delete"></span>
                <div class="title-widget">
                    <div class="text-tit"><?php echo __t($widget['PanelWidget']['display_name']);?></div>
                    <div class="drag-container"></div>
                </div>
                <div class="f-widget body-widget">
                    <div class="p-1">
                        <?php
                        echo $this->requestAction(
                            array(
                                'plugin' => 'panel',
                                'controller' => 'widgets',
                                'action' => 'get_data_widget',
                                $widget['PanelWidget']['logic_model'],
                                $widget['PanelWidget']['url_view'],
                                $widget['PanelWidgetUser']['size_x']
                            ),
                            array('return')
                        );
                        ?>
                    </div>
                </div>
            </div>
        </li>
    <?php } ?>
</ul>
