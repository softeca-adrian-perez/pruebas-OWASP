<?php
class DeleteAllShell extends Shell
{
    public $uses = array('DeleteAll');

    // console\cake deleteall delete_all_distributors_objectives
    public function delete_all_distributors_objectives()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Delete All Scheduled Tasks - delete_all_distributor_objectives - Start", true));
            }
            $this->DeleteAll->delete_all_distributors_objectives();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Delete All Scheduled Tasks - delete_all_distributor_objectives - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Delete All Scheduled Tasks - delete_all_distributor_objectives - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
