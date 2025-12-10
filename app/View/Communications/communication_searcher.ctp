<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'controller' => 'home',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Communication.Communications'),
                array(
                    'controller' => 'home',
                    'action' => 'home_page2'
                )
            ),
            __t('Communication.List'),
        ));
        ?>
    </div>
</div>
<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Communication.Communications'); ?>
        <?php echo $this->element('../Elements/Comun/article_searcher'); ?>
    </div>
    </br>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th class="ta-center"><?php echo __t('General.Image'); ?></th>
                    <th><?php echo __t('General.Title'); ?></th>
                    <th><?php echo $this->Paginator->sort('Communication.subtitle', __t('Communication.Subtitle')); ?></th>
                    <th><?php echo __t('Communication.Communication_section'); ?></th>
                    <th><?php echo __t('Communication.Communication_subsection'); ?></th>
                    <th><?php echo $this->Paginator->sort('Communication.start_date', __t('Communication.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Communication.end_date', __t('Communication.End_date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($communications as $communication) { ?>
                    <tr>
                        <td class="ta-center">
                            <img
                                src="<?php echo FileManager::get_url(FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication['Communication']['image']); ?>"
                                style="max-height: 70px">
                        </td>
                        <td>
                            <?php echo $this->Html->link(
                                $communication['Communication']['title'],
                                array(
                                    'controller' => 'communications',
                                    'action' => 'home_section',
                                    $communication['Communication']['communication_section_id'],
                                    $communication['Communication']['section_subsection_id'],
                                    $communication['Communication']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            ); ?>
                        </td>
                        <td>
                            <?php echo h($communication['Communication']['subtitle']); ?>
                        </td>
                        <td>
                            <?php echo h($communications_section[$communication['Communication']['communication_section_id']]); ?>
                        </td>
                        <td>
                            <?php echo h($communications_subsection[$communication['Communication']['section_subsection_id']]); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVista(h($communication['Communication']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVista(h($communication['Communication']['end_date'])); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br />
    <?php echo $this->element('Comun/paginacion'); ?>
</div>