<?php
class TrainingDeleteOldEntriesShell extends Shell
{
    public $uses = array('TrainingAllowance', 'TrainingDelegate', 'TrainingPlannedCourse', 'TrainingCourse');
    private static $date = '2020-01-01';

    // console\cake trainingdeleteoldentries deleteOldEntries
    public function deleteOldEntries()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - Start", true));
            }
            //All garage training allowances that end before the date.
            $trainingAllowancesIdsToDelete = $this->TrainingAllowance->getListOfAllowancesIdsByDateTo(self::$date);

            //All courses that do not have a planned course after the date
            $coursesIdsToKeep = $this->TrainingPlannedCourse->getListOfTrainingCoursesIdsAfterPlannedDateOrDateNull(self::$date);
            $coursesIdsToDelete = $this->TrainingCourse->getListofCoursesWithoutIds($coursesIdsToKeep);

            //All planned courses with a "date to" before the date
            $plannedCoursesIdsToDelete = $this->TrainingPlannedCourse->getListOfIdsBeforeDateTo(self::$date);

            //All delegates related with previous planned courses
            $delegatesIdsToDelete = $this->TrainingDelegate->getListofDelegatesByPlannedCoursesIds($plannedCoursesIdsToDelete);


            $resultDelegates = $this->TrainingDelegate->deleteAll(array('id IN' => $delegatesIdsToDelete));
            $resultPlannedCourse = $this->TrainingPlannedCourse->deleteAll(array('id IN' => $plannedCoursesIdsToDelete));
            $resultTrainingCourse = $this->TrainingCourse->deleteAll(array('id IN' => $coursesIdsToDelete));
            $resultTrainingAllowance = $this->TrainingAllowance->deleteAll(array('id IN' => $trainingAllowancesIdsToDelete));

            if (CRON_DEBUG_ACTIVE) {
                if ($resultDelegates) {
                    CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - Count: ". count($delegatesIdsToDelete) ." Delegates deleted:", true));
                    CakeLog::write('scheduled_task', print_r($delegatesIdsToDelete, true));
                }
                if ($resultPlannedCourse) {
                    CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - Count: ". count($plannedCoursesIdsToDelete) ." Training Planned Courses deleted:", true));
                    CakeLog::write('scheduled_task', print_r($plannedCoursesIdsToDelete, true));
                }
                if ($resultTrainingCourse) {
                    CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - Count: ". count($coursesIdsToDelete) ." Training Courses deleted:", true));
                    CakeLog::write('scheduled_task', print_r($coursesIdsToDelete, true));
                }
                if ($resultTrainingAllowance) {
                    CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - Count: ". count($trainingAllowancesIdsToDelete) ." Training Allowances deleted:", true));
                    CakeLog::write('scheduled_task', print_r($trainingAllowancesIdsToDelete, true));
                }
                CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Training Delete Old Entries Scheduled Tasks - deleteOldEntries - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
