<div class="medium-12 columns p-right-0">

    <?php if(!empty($garage)){ ?>
        <h1><?php echo __t('Garage.Garage') . ': ' . h($garage['Garage']['name']); ?></h1>
    <?php }?>
    <h5><?php echo __t('Task.Completed_garage_tasks'); ?></h5><br/>

    <div class="medium-12 columns p-0 m-top-1 m-bottom-1">
        <div class="o-auto">
            <table class="table-tracking">

                <thead>
                <tr>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Task.Title', __t('Task.Title')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Task.creation_date', __t('Task.Creation_date')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('TaskGarage.completed_date', __t('Task.Completed_date')); ?></th>
                </tr>
                </thead>

                <tbody id="table_completed_garage_task">
                <?php if( isset($tasks_garages_completed) ) { ?>
                    <?php foreach ($tasks_garages_completed as $task) { ?>
                        <tr>
                            <td class="c-defecto ta-center">
                                <?php echo h($task['Task']['title']); ?>
                            </td>
                            <td class="c-defecto fw-bold ta-center">
                                <?php echo h($task['Task']['creation_date']); ?>
                            </td>
                            <td class="c-defecto fw-bold ta-center">
                                <?php echo h($task['TaskGarage']['completed_date']); ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <button type="button" class="button-general tres ta-center btn-full-width z-index-priority" id="btn_load_more_task" data-page="2" data-url="<?php echo Router::url(array(
        'controller' => 'appointments',
        'action' => 'ajax_load_more_completed_garage_task'
    ));?>">
        <?php echo __t('Distributor.View_more'); ?>
    </button>


</div>