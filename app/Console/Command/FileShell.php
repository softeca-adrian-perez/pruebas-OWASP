<?php
class FileShell extends Shell
{
    /**
     * Removes export csv files.
     *
     * console\cake file removeCsvExportFiles
     */
    public function removeCsvExportFiles()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("File Scheduled Tasks - delete_old_csv_export_files - Start", true));
            }
            $path = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE;
            $files = array_diff(scandir($path), ['.', '..']);
            $check_date = date('Y-m-d', strtotime('-30 days'));
            foreach ($files as $file) {
                $file_path = $path . DS . "$file";
                $file_date = date('Y-m-d', filemtime($file_path));
                if ($check_date > $file_date) {
                    unlink($path . DS . "$file");
                }
            }
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("File Scheduled Tasks - delete_old_csv_export_files - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("File Scheduled Tasks - delete_old_csv_export_files - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake file removeObjectivesCsvExportFiles
    public function removeObjectivesCsvExportFiles()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("File Scheduled Tasks - delete_old_csv_objectives_files - Start", true));
            }
            $path = ConstantsPath::DIR_CSV_OBJECTIVES_FILES_ABSOLUTE;
            $files = array_diff(scandir($path), ['.', '..']);
            $check_date = date('Y-m-d', strtotime('-30 days'));
            foreach ($files as $file) {
                $file_path = $path . DS . "$file";
                $file_date = date('Y-m-d', filemtime($file_path));
                if ($check_date > $file_date) {
                    unlink($path . DS . "$file");
                }
            }
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("File Scheduled Tasks - delete_old_csv_objectives_files - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("File Scheduled Tasks - delete_old_csv_objectives_files - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
