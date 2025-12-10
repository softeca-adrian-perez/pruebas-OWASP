<div class="medium-12 columns p-right-0">

    <?php if(!empty($garage)){ ?>
        <h1><?php echo __t('Garage.Garage') . ': ' . $garage['Garage']['name']; ?></h1>
    <?php }elseif(!empty($distributor)){ ?>
        <h1><?php echo __t('Distributor.Distributor') . ': ' . $distributor['Distributor']['name']; ?></h1>
    <?php }?>
    <h5><?php echo __t('Appointment.Next_visits'); ?></h5><br/>

    <div class="medium-12 columns p-0 m-top-1 m-bottom-1">
        <div class="o-auto">
            <table class="table-tracking">

                <thead>
					<tr>
						<th class="ta-center"><?php echo __t('Appointment.Date'); ?></th>
						<th class="ta-center"><?php echo __t('Appointment.Start_time'); ?></th>
						<th class="ta-center"><?php echo __t('Appointment.End_time'); ?></th>
						<th class="ta-center"><?php echo __t('Appointment.Assign_to'); ?></th>
					</tr>
                </thead>

                <tbody>
					<?php if (isset($customer_visits)) { foreach ($customer_visits as $visit) { ?>
						<tr>
							<td class="c-defecto ta-center">
								<?php echo $this->Html->link(
									$visit['Appointment']['date'],
									array(
										'controller' => 'appointments',
										'action' => 'edit',
										$visit['Appointment']['id']
									),
									array(
										'class' => 'c-primary'
									)
								); ?>
							</td>
							<td class="c-defecto fw-bold ta-center">
								<?php echo h($visit['Appointment']['start_time']); ?>
							</td>
							<td class="c-defecto fw-bold ta-center">
								<?php echo h($visit['Appointment']['end_time']); ?>
							</td>
							<td class="c-defecto ta-center">
								<?php echo h($visit['User']['name'] . ' ' . h($visit['User']['surname'] )); ?>
							</td>
						</tr>
					<?php } }?>
                </tbody>

            </table>
        </div>
    </div>

    <div class="medium-3 columns p-bottom-1 fpzi p-top-1 p-left-0">
        <?php echo $this->Form->input(
            'Appointment.date_modal',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js',
                'id' => 'appointment-date-modal',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Appointment.Date'),
            )
        ); ?>
    </div>

    <div class="medium-3 columns m-bottom-1 p-top-1 cnt-datp">
        <?php echo $this->Form->input(
            'Appointment.start_modal',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'timepicker',
                'id' => 'start-time-modal',
                'label' => __t('Appointment.Start_time'),
            )
        ); ?>
    </div>

    <?php
    echo $this->Form->hidden(
        'Appointment.appointment_status_id_modal',
        array(
            'id' => 'appointment-status-modal',
            'value' => ConstantsStatusAppointments::PLANNED
        )
    );
    ?>

    <div class="medium-3 columns p-top-1 p-left-0">
        <label>  <?php echo __t('Appointment.Assign_to') ?></label>
        <?php
        echo $this->Form->input(
            'Appointment.user_assigned_id_modal',
            array(
                'label' => false,
                'class' => 'select2-multiple',
                'id' => 'assigned-to-appointments-modal',
                'type' => 'select',
                'multiple' => false,
                'default' => isset($this->request->data['Appointment']['user_assigned_id']) ? $this->request->data['Appointment']['user_assigned_id'] : 1,
                'empty' => false,
                'options' => $users
            )
        );
        ?>
    </div>

    <div class="medium-3 columns  p-top-1 m-top-1 ta-center">
        <?php
        echo $this->Html->link(
            __t('Appointment.Save_visit'),
            array(),
            array(
                'escape' => false,
                'id' => 'save-follow-up-visit',
                'title' => __t('Appointment.Save_visit'),
                'class' => 'button-general uno',
                'data-url' => Router::url(
                    array(
                        'controller' => 'appointments',
                        'action' => 'ajax_save_follow_up_visit',
                    )
                )
            )
        );
        ?>
    </div>

</div>
