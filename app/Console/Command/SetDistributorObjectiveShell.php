<?php
class SetDistributorObjectiveShell extends Shell
{
    public $uses = array('SetDistributorObjective');

    // console\cake setdistributorobjective set_new_objectives
    public function set_new_objectives()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Set Distributors Objectives Scheduled Tasks - create_new_distributor_objectives - Start", true));
            }
            $this->SetDistributorObjective->setObjectives();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Set Distributors Objectives Scheduled Tasks - create_new_distributor_objectives - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Set Distributors Objectives Scheduled Tasks - create_new_distributor_objectives - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
