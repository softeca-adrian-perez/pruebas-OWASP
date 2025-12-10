<?php
echo $this->Form->create(
    'AppointmentObjective',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
echo $this->Form->hidden('AppointmentObjective.id');
$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('CRM.Crm'),
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home'
                    )
                ),
                __t('Crm.Management_area'),
                __t('AppointmentsObjectives.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('CRM.Crm'),
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home'
                    )
                ),
                __t('Crm.Management_area'),
                __t('AppointmentsObjectives.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $url_cancel); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('AppointmentObjective.Objective'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php echo $this->Form->input(
            'AppointmentObjective.name',
            array(
                'required' => true,
                'type' => 'text',
                'id' => 'name',
                'label' => __t('AppointmentObjective.Name'),
            )
        ); ?>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('AppointmentObjective.Type') ?>
    </div>
    <div class="cnt-form-inputs p-bottom-1">
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <div>
                <?php
                echo $this->Form->input(
                    'AppointmentObjective.tg_personal',
                    array(
                        'label' => __t('AppointmentObjective.Tg_personal'),
                        'type' => 'checkbox',
                        'checked' => isset($objective) && $objective['AppointmentObjective']['tg_personal'] ? true : false,
                    )
                );
                ?>
            </div>
            <div>
                <?php
                echo $this->Form->input(
                    'AppointmentObjective.tg_management',
                    array(
                        'label' => __t('AppointmentObjective.Tg_management'),
                        'type' => 'checkbox',
                        'checked' => isset($objective) && $objective['AppointmentObjective']['tg_management'] ? true : false,
                    )
                );
                ?>
            </div>
            <div>
                <?php
                echo $this->Form->input(
                    'AppointmentObjective.gpc_personal',
                    array(
                        'label' => __t('AppointmentObjective.Gpc_personal'),
                        'type' => 'checkbox',
                        'checked' => isset($objective) && $objective['AppointmentObjective']['gpc_personal'] ? true : false,
                    )
                );
                ?>
            </div>
            <div>
                <?php
                echo $this->Form->input(
                    'AppointmentObjective.gpc_management',
                    array(
                        'label' => __t('AppointmentObjective.Gpc_management'),
                        'type' => 'checkbox',
                        'checked' => isset($objective) && $objective['AppointmentObjective']['gpc_management'] ? true : false,
                    )
                );
                ?>
            </div>
            <?php
            echo $this->Form->hidden(
                'AppointmentObjective.aag_region_id',
                array(
                    'value' => $aagRegionId
                )
            );
            ?>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>