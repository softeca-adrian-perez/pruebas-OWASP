<?php
class TrainingAllowanceShell extends Shell
{
    public $uses = array('TrainingAllowance');

    // console\cake trainingallowance update_allowance_7days
    public function update_allowance_7days()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Allowance Scheduled Tasks - update_training_allowance_seven_days - Start", true));
            }
            $this->TrainingAllowance->updateAllowance7days();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Allowance Scheduled Tasks - update_training_allowance_seven_days - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Allowance Scheduled Tasks - update_training_allowance_seven_days - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake trainingallowance update_actual_allowance
    public function update_actual_allowance()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Allowance Scheduled Tasks - update_training_actual_allowance - Start", true));
            }
            $this->TrainingAllowance->updateActualAllowance();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Allowance Scheduled Tasks - update_training_actual_allowance - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Allowance Scheduled Tasks - update_training_actual_allowance - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
