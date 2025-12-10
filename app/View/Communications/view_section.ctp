<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Communication.Communications') .' '.__t('Communication.Communications_sections'),
                array(
                    'controller' => 'communications',
                    'action' => 'maintenance_communications_sections'
                )
            ),
            __t('Communication.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'communications',
                'action' => 'maintenance_communications_sections',
            ),
            array(
                'class' => 'button btn-cancelar btn-back',
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'communications',
                'action' => 'edit_section',
                $communication_section['CommunicationSection']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'edit',
            )
        );
        ?>
    </div>
</div>
<div class="row p-1">
    <div class="columns medium-12">
        <fieldset class="columns fieldset-garage-list">
            <div class="row">
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.English_name') ?>: </strong>
                    <br>
                    <div class="b-bottom-1 height_input"><?php echo h($communication_section['CommunicationSection']['name_en']); ?></div>
                </div>
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.French_name') ?>: </strong>
                    <br>
                    <div class="b-bottom-1 height_input"><?php echo h($communication_section['CommunicationSection']['name_fr']); ?></div>
                </div>
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.German_name') ?>: </strong>
                    <br>
                    <div class="b-bottom-1 height_input"><?php echo h($communication_section['CommunicationSection']['name_de']); ?></div>
                </div>
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.Size') ?>: </strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php if($communication_section['CommunicationSection']['single']){
                            echo __t('Communication.One_position');
                        } else {
                            echo __t('Communication.Two_positions');
                        }?>
                    </div>
                </div>
                <div class="columns medium-4 p-1 end">
                    <strong><?php echo __t('Communication.Position') ?>: </strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php if($communication_section['CommunicationSection']['position'] == ConstantsCommunicationSections::TOP){
                            echo __t('Communication.Position_top');
                        } else if($communication_section['CommunicationSection']['position'] == ConstantsCommunicationSections::LEFT) {
                            echo __t('Communication.Position_left');
                        } else if($communication_section['CommunicationSection']['position'] == ConstantsCommunicationSections::RIGHT){
                            echo __t('Communication.Position_right');
                        }?>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>