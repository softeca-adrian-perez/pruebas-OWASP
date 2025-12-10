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
                __t('Communication.Communications') .' '.__t('Communication.Communications_subsections'),
                array(
                    'controller' => 'communications',
                    'action' => 'maintenance_section_subsection'
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
                'action' => 'maintenance_section_subsection',
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'communications',
                'action' => 'edit_subsection',
                $section_subsection['SectionSubsection']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Communication.Communications_subsections'); ?>
    </div>
<div class="row p-1">
    <div class="columns medium-12">
        <fieldset class="columns fieldset-garage-list">
            <div class="row">
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.English_name') ?>: </strong>
                    <br>

                    <div
                        class="b-bottom-1 height_input"><?php echo h($section_subsection['SectionSubsection']['name_en']); ?></div>
                </div>
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.French_name') ?>: </strong>
                    <br>

                    <div
                        class="b-bottom-1 height_input"><?php echo h($section_subsection['SectionSubsection']['name_fr']); ?></div>
                </div>
                <div class="columns medium-4 p-1">
                    <strong><?php echo __t('Communication.German_name') ?>: </strong>
                    <br>

                    <div
                        class="b-bottom-1 height_input"><?php echo h($section_subsection['SectionSubsection']['name_de']); ?></div>
                </div>
                <div class="columns medium-4 p-1 end">
                    <strong><?php echo __t('Communication.Communication_section') ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                        <?php echo h($sections[$section_subsection['SectionSubsection']['communication_section_id']]) ?>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>