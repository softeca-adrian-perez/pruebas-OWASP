<div class="o-auto cnt-table-search p-bottom-1">
	<table class="table-tracking table-responsive z-index-priority" id="table-reassignments">
		<thead>
			<tr>
				<th>
					<?php echo __t('User.Origin_user_name'); ?>
				</th>
				<th>
					<?php echo __t('User.Destination_user_name'); ?>
				</th>
				<th class="ta-center">
					<?php echo __t('Event.Start_date'); ?>
				</th>
				<th class="ta-center">
					<?php echo __t('Event.End_date'); ?>
				</th>
				<th class="ta-center">
					<?php echo __t('General.Status'); ?>
				</th>
				<th class="ta-center">
					<?php echo __t('User.Permanent'); ?>
				</th>
				<th class="ta-center">
					<?php echo __t('General.Actions'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $user_reassignments as $user_reassignment ){ ?>
			<tr>
				<td>
					<?php echo h($users_list[$user_reassignment['UserReassignment']['user_id_origin']]); ?>
				</td>
				<td>
					<?php echo h($users_list[$user_reassignment['UserReassignment']['user_id_destination']]); ?>
				</td>
				<td class="ta-center"
					<?php if( $user_reassignment['UserReassignment']['permanent'] ){
						echo 'data-order="z"';
					} else {
						echo 'data-order="' . $user_reassignment['UserReassignment']['date_from'] .'"';
					}?>
				>
					<?php echo Fecha::toFormatoVista($user_reassignment['UserReassignment']['date_from']); ?>
				</td>
				<td class="ta-center">
					<?php echo Fecha::toFormatoVista($user_reassignment['UserReassignment']['date_to']); ?>
				</td>
				<td class="ta-center">
					<?php
					if($user_reassignment['UserReassignment']['permanent']){
						echo __t('General.Completed');
					} else if( $user_reassignment['UserReassignment']['active'] ){
                        echo __t('General.Active');
                    } else {
                        echo __t('General.Pending');
                    }?>
				</td>
				<td class="ta-center">
					<?php echo Booleano::toString($user_reassignment['UserReassignment']['permanent']); ?>
				</td>
				<td class="ta-center">
					<?php
					if( $user_reassignment['UserReassignment']['permanent'] ){
					}else if( $user_reassignment['UserReassignment']['active'] ){
                        echo $this->Html->link(
                            '<span class="icon-delete"></span>',
                            array(
                                'controller' => 'users',
								'action' => 'revert_and_delete_reassignment',
								$user_reassignment['UserReassignment']['user_id_origin'],
								$user_reassignment['UserReassignment']['user_id_destination'],
								$user_reassignment['UserReassignment']['date_from']
                            ),
                            array(
                                'title' => __t('User.Delete_reassignment'),
                                'escape' => false
                            )
                        );
                    } else {
                        echo $this->Html->link(
                            '<span class="ion-shuffle"></span>',
                            array(
                                'controller' => 'users',
								'action' => 'revert_and_delete_reassignment',
								$user_reassignment['UserReassignment']['user_id_origin'],
								$user_reassignment['UserReassignment']['user_id_destination'],
								$user_reassignment['UserReassignment']['date_from']
                            ),
                            array(
                                'title' => __t('User.Revert_reassignment'),
                                'escape' => false,
                            )
                        );
                    } ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
