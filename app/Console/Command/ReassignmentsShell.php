<?php
class ReassignmentsShell extends Shell
{
    public $uses = array('UserReassignment');

    // console\cake reassignments reassignments
    function reassignments()
    {
        $this->UserReassignment->reassign_user_scheduled_task();
    }
}
