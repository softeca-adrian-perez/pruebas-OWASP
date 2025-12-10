<div class="medium-12 columns">
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th class="ta-center"><?php echo __t('Communication.Date'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Image'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Title'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Subtitle'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Files'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Communication_subsection'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($communications as $communication){ ?>
                <tr>
                    <td width="1" class="c-primary ws-nowrap ta-center">
                        <?php echo Fecha::toFormatoVista($communication['Communication']['start_date']);?>
                    </td>
                    <td class="ta-center">
                        <img style="max-width:150px" src="<?php echo FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication['Communication']['image']) ?>">
                    </td>
                    <td class="c-negro ta-center">
                        <?php echo $this->Html->link(
                            $communication['Communication']['title'],
                            '#',
                            array(
                                'class' => 'c-primary cnt-img-carousel',
                                'data-id' => $communication['Communication']['id'],
                                'data-open' => 'modalCommunications',
                                'data-url' => '#'
                            )
                        ); ?>
                    </td>
                    <td class="c-negro ta-center">
                        <?php echo h($communication['Communication']['subtitle']);?>
                    </td>
                    <td class="c-negro ta-center">
                        <?php
                        if(isset($communication['Communication']['CommunicationFiles'])){ ?>
                            <span class="ion-paperclip c-informacion hover-c-primary"></span>
                        <?php } ?>
                    </td>
                    <td class="ta-center">
                        <?php echo h($sections_subsections[$communication['Communication']['section_subsection_id']]);?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <br>
        <?php echo $this->element('Comun/paginacion'); ?>
    </div>

</div>

<div style="display: none;" id="modalCommunications" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-url="
<?php echo Router::url(
    array(
        'controller' => 'communications',
        'action' => 'ajax_modal_communication'
    )
);?>">
    <div id="modalCommunications_view">
        <?php echo $this->element('../Communications/Elements/modal_communication');?>
    </div>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>