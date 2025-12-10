<?php
App::uses('Feefo', 'Lib');

class FeefoReviewsShell extends Shell
{
    /**
     * Sync all the networks linked to FeefoReviews.
     *
     * console\cake feefoReviews saveReviews
     */
    public function saveReviews()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Feefo Reviews Scheduled Tasks - update_feefo_reviews_data - Reviews - Start", true));
            }
            $feefo = new Feefo();
            $feefo->saveReviews();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Feefo Reviews Scheduled Tasks - update_feefo_reviews_data - Reviews - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Feefo Reviews Scheduled Tasks - update_feefo_reviews_data - Reviews - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Sync all the networks linked to FeefoReviews.
     *
     * console\cake feefoReviews saveNetworkReviews
     */
    public function saveNetworkReviews()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Feefo Reviews Scheduled Tasks - update_feefo_reviews_data - Network Reviews - Start", true));
            }
            $feefo = new Feefo();
            $feefo->saveNetworkReviews();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Feefo Reviews Scheduled Tasks - update_feefo_reviews_data - Network Reviews - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Feefo Reviews Scheduled Tasks - update_feefo_reviews_data - Network Reviews - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
