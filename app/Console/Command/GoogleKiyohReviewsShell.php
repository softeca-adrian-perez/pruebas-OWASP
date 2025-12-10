<?php
App::uses('Google', 'Lib');
App::uses('Kiyoh', 'Lib');
App::uses('Agn', 'Lib');

class GoogleKiyohReviewsShell extends Shell
{
    public $uses = array(
        "GarageNetwork",
        "Garage",
        "Network",
    );

    private static $kiyohGoogleNetworkIds = array(NETWORK_ID_GV, NETWORK_ID_GC);
    /**
     * Sync all the networks linked to GoogleKiyohReviews.
     *
     * console\cake GoogleKiyohReviews saveGoogleReviews
     */
    public function saveGoogleReviews()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Google Reviews - Start", true));
            }
            $google = new Google();
            $google->saveGoogleReviews();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Google Reviews - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Google Reviews - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Sync all the networks linked to GoogleKiyohReviews.
     *
     * console\cake GoogleKiyohReviews saveKiyohReviews
     */
    public function saveKiyohReviews()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Kiyoh Reviews - Start", true));
            }
            $kiyoh = new Kiyoh();
            $kiyoh->saveKiyohReviews();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Kiyoh Reviews - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Kiyoh Reviews - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    public function saveNetworkReviews()
    {
        try {
            $agn = new Agn();
            foreach (self::$kiyohGoogleNetworkIds as $networkId) {
                $this->saveNetworkReviewsByNetwork($networkId);
                $network = $this->Network->findById($networkId);
                $agn->purgeCacheReviewsData($network['Network']['guid']);
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Network Reviews - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
    /**
     * Sync all the networks linked to GoogleKiyohReviews.
     *
     * console\cake GoogleKiyohReviews saveNetworkReviews
     */
    public function saveNetworkReviewsByNetwork($networkId)
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Network Reviews By Network - Start", true));
            }
            $averageRatingQuery = $this->GarageNetwork->find('list', array(
                'conditions' => array(
                    'network_id' => $networkId,
                    'rating IS NOT NULL',
                    'status' => ConstantsNetworksStatus::LIVE,
                ),
                'fields' => array('GarageNetwork.rating'),
            ));

            $totalRatings = 0;
            $numRatings = 0;
            $networkRating = 0;

            foreach ($averageRatingQuery as $rating) {
                $totalRatings += floatval($rating);
                $numRatings++;
            }

            if ($numRatings > 0) {
                $networkRating = $totalRatings / $numRatings;
                $networkRating = number_format($networkRating, 1);
            }

            $reviewsNumQuery = $this->GarageNetwork->find('list', array(
                'conditions' => array(
                    'network_id' => $networkId,
                    'status' => ConstantsNetworksStatus::LIVE,
                    'reviews_number IS NOT NULL',
                ),
                'fields' => array('GarageNetwork.reviews_number'),
            ));
            $networkReviewsNum = 0;
            foreach ($reviewsNumQuery as $reviewNum) {
                $networkReviewsNum += intval($reviewNum);
            }

            $network = $this->Network->find('first', array(
                'conditions' => array('Network.id' => $networkId)
            ));

            if ($network) {
                $network['Network']['rating'] = $networkRating;
                $network['Network']['reviews_number'] = $networkReviewsNum;
                $network['Network']['modification_date'] = date('Y-m-d H:i:s');
                $this->Network->save($network);
            }
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Network Reviews By Network - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Google Kiyoh Reviews Scheduled Tasks - update_kiyoh_and_google_reviews_data - Network Reviews By Network - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
