<?php
class TasksShell extends Shell
{
    public $uses = array('Task');

    // console\cake tasks tasks_to_expired
    function tasks_to_expired()
    {
        $this->Task->tasksToExpired();
    }

    // console\cake tasks tasks_to_expired_france
    function tasks_to_expired_france()
    {
        $this->Task->tasksToExpiredFrance();
    }
}
