<div class="medium-12 columns">
    <?php
    echo $this->Form->hidden(
        'page_id',
        array(
            'id' => 'comm_pagination_id',
            'value' => $this->request->params['paging']['Communication']['page']
        )
    );
    ?>
    <div class="o-auto">
        <table class="table-tracking" id="communication_searcher_list_table">
            <thead>
            <tr>
                <th class="ta-center"><?php echo __t('Communication.Date'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Image'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Title'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Subtitle'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Body'); ?></th>
                <th class="ta-center"><?php echo __t('Communication.Files'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($communications as $communication){ ?>
                <tr data-communication-title="<?php echo $communication['Communication']['title']?>"data-id="<?php echo $communication['Communication']['id'];?>">
                    <td width="1" class="c-primary ws-nowrap ta-center">
                        <?php echo Fecha::toFormatoVista($communication['Communication']['start_date']);?>
                    </td>
                    <td class="ta-center">
                        <img style="max-width:150px" src="<?php echo FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication['Communication']['image']) ?>">
                    </td>
                    <td class="c-negro ta-center">
                        <?php echo $this->Html->link(
                            $communication['Communication']['title'],
                            array(
                                'controller' => 'communications',
                                'action' => 'section',
                                $communication['Communication']['communication_section_id'],
                                $communication['Communication']['section_subsection_id'],
                                $communication['Communication']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td class="c-negro ta-center">
                        <?php echo h($communication['Communication']['subtitle']);?>
                    </td>
                    <td class="ta-center">
                        <?php echo substr($communication['Communication']['body'], 0, 120);
                        if(substr(h($communication['Communication']['body']), 0, 120) != $communication['Communication']['body']){
                            echo ' ...';
                        }
                        ?>
                    </td>
                    <td class="c-negro ta-center">
                        <?php
                        if(isset($communication['Communication']['CommunicationFiles'])){ ?>
                            <span class="ion-paperclip c-informacion hover-c-primary"></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <br>
        <?php echo $this->element('Comun/paginacion'); ?>
    </div>
</div>
