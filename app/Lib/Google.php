<?php

App::uses('Agn', 'Lib');

class Google
{
    const LOCATION_ID = '?location=';
    const RADIUS_PARAM = '&radius=';
    const KEYWORD_PARAM = '&keyword=';
    const TYPE_PARAM = '&type=';
    const KEY_PARAM = '&key=';
    const PLACE_ID = '?place_id=';
    const FIELDS_PARAM = '&fields=';
    const LANGUAGE_PARAM = '&language=';
    const REVIEWS_NO_TRANSLATIONS = '&reviews_no_translations=';
    const RADIUS = '100';
    const TYPE = 'car_repair';
    const FIELDS = 'rating,user_ratings_total,reviews';
    const TRUE = 'true';

    private static $networksData = array(
        NETWORK_ID_GV => GOOGLE_API_KEY_REVIEWS_GV,
        NETWORK_ID_GC => GOOGLE_API_KEY_REVIEWS_GC
    );

    /**
     * Save Google Reviews for all networks with Google Reviews.
     */
    public function saveGoogleReviews()
    {
        try {
            $networkClass = ClassRegistry::init('Network');
            $agn = new Agn();
            $response = true;
            foreach (self::$networksData as $key => $value) {
                $responsePerNetwork = $this->saveGoogleReviewsByNetwork($key);
                $network = $networkClass->findById($key);
                $agn->purgeCacheReviewsData($network['Network']['guid']);
                if (!$responsePerNetwork) {
                    $response = false;
                }
            }
            return $response;
        } catch (Throwable $e) {
            CakeLog::debug(print_r("Google - Save reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Save Google Reviews for the given network Id.
     */
    public function saveGoogleReviewsByNetwork($networkId)
    {
        try {
            $garageNetworkClass = ClassRegistry::init('GarageNetwork');
            $googleReviewsArray = $this->getGoogleReviews($networkId);

            if ($googleReviewsArray == null || !is_array($googleReviewsArray)) {
                return false;
            }

            foreach ($googleReviewsArray['reviews'] as $garageNetworkId => $reviews) {
                usort($reviews, function ($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });
                $reviews = array_slice($reviews, 0, 50);
                $garageNetwork['GarageNetwork']['reviews_info'] = json_encode($reviews);
                $garageNetwork['GarageNetwork']['id'] = $garageNetworkId;
                $garageNetworkClass->guardar($garageNetwork, array('reviews_info'));

                $ratingSum = 0;
                $reviewsCount = count($reviews);
                foreach ($reviews as $review) {
                    $ratingSum += $review['rating'];
                }
                if ($ratingSum > 0) {
                    $ratingAverage = $ratingSum / $reviewsCount;
                }
                $manualAverageRating[$garageNetworkId] = $ratingAverage;
                $manualReviewsNumber[$garageNetworkId] = $reviewsCount;
            }

            foreach ($googleReviewsArray['values'] as $value) {
                if ($value['status'] == 'OK') {
                    $averageRating = isset($value['results'][0]['rating']) ? $value['results'][0]['rating'] : null;
                    $reviewsNumber = isset($value['results'][0]['user_ratings_total']) ? $value['results'][0]['user_ratings_total'] : null;
                    $placeId = $value['results'][0]['place_id'];

                    $garageNetworkId = isset($googleReviewsArray['locationGarageNetworkId'][$placeId])
                        ? $googleReviewsArray['locationGarageNetworkId'][$placeId]
                        : '';
                    if (!empty($garageNetworkId)) {
                        if ($averageRating == null && isset($manualAverageRating[$garageNetworkId])) {
                            $averageRating = $manualAverageRating[$garageNetworkId];
                        }
                        if ($reviewsNumber == null && isset($manualReviewsNumber[$garageNetworkId])) {
                            $reviewsNumber = $manualReviewsNumber[$garageNetworkId];
                        }

                        $fields = array('rating', 'reviews_number', 'modification_date');
                        $garageNetwork = array(
                            'GarageNetwork' => array(
                                'id' => $garageNetworkId,
                                'rating' => $averageRating,
                                'reviews_number' => $reviewsNumber,
                                'modification_date' => date('Y-m-d H:i:s')
                            )
                        );
                        $garageNetworkClass->guardar($garageNetwork, $fields);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Google - Save reviews By Network - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Get Google reviews.
     */
    private function getGoogleReviews($networkId)
    {
        try {
            $garageNetworkClass = ClassRegistry::init('GarageNetwork');
            $locationGarageNetworkId = array();
            $garageNetworkInfo = $garageNetworkClass->find('all', array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id'
                        ),
                    )
                ),
                'conditions' => array(
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'Garage.latitude IS NOT NULL',
                    'Garage.longitude IS NOT NULL',
                ),
                'fields' => array('Garage.latitude', 'Garage.longitude', 'Garage.business_name', 'Garage.name', 'GarageNetwork.id')
            ));

            foreach ($garageNetworkInfo as &$garage) {
                $garage['Garage']['location'] = $garage['Garage']['latitude'] . ',' . $garage['Garage']['longitude'];
            }

            $allReviewsData = array();
            $reviews = array();
            foreach ($garageNetworkInfo as $garageNetwork) {
                $garageNetwork['GarageNetwork']['rating'] = null;
                $garageNetwork['GarageNetwork']['reviews_number'] = null;
                $garageNetwork['GarageNetwork']['reviews_info'] = null;
                $garageNetworkClass->guardar($garageNetwork, array('rating', 'reviews_number', 'reviews_info'));

                $reviews = json_decode($this->fetchTitleReviews($garageNetwork['Garage']['location'], $garageNetwork['Garage']['business_name'] ?? $garageNetwork['Garage']['name'], $networkId), true);

                if ($reviews['status'] == 'OK') {
                    $posArray = array_search($garageNetwork['Garage']['business_name'] ?? '', array_column($reviews['results'], 'name'));
                    $placeId = $reviews['results'][$posArray !== false ? $posArray : 0]['place_id'];
                    $locationGarageNetworkId[$placeId] = $garageNetwork['GarageNetwork']['id'];
                    $reviewsInfo = json_decode($this->fetchGarageReviews($placeId, $networkId), true);
                    if (isset($reviewsInfo['result']['reviews'])) {
                        foreach ($reviewsInfo['result']['reviews'] as $review) {
                            $reviewData = array(
                                'name' => Texto::encryptDecryptText($review['author_name'], true),
                                'date' => date('Y-m-d', $review['time']),
                                'title' => $reviews['results'][$posArray !== false ? $posArray : 0]['name'],
                                'rating' => ($review['rating']),
                                'text' => $review['text'],
                                'location' => $garageNetwork['Garage']['location'],
                            );
                            $allReviewsData[$garageNetwork['GarageNetwork']['id']][] = $reviewData;
                        }
                    }
                }
                $allreviews[] = $reviews;
            }

            return array(
                'reviews' => $allReviewsData,
                'values' => $allreviews,
                'locationGarageNetworkId' => $locationGarageNetworkId,
            );
        } catch (Exception $e) {
            CakeLog::debug(print_r("Google - Get reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Fetch Google title reviews.
     */
    private function fetchTitleReviews($location, $garageName, $networkId)
    {
        try {
            $radius = self::RADIUS;
            $type = self::TYPE;
            $key = Texto::encryptDecryptText(self::$networksData[$networkId], false);

            $url = Configure::read('GOOGLE_TITLE_REVIEWS_URL')
                . self::LOCATION_ID . $location
                . self::RADIUS_PARAM . $radius
                . self::KEYWORD_PARAM . urlencode($garageName)
                . self::TYPE_PARAM . $type
                . self::KEY_PARAM . $key;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                CakeLog::debug(print_r("Google - Fetch title reviews - Error making cURL request: " . curl_error($ch), true));
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Google - Fetch title reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Fetch Google garage reviews.
     */
    private function fetchGarageReviews($placeId, $networkId)
    {
        try {
            $fields = self::FIELDS;
            $key = Texto::encryptDecryptText(self::$networksData[$networkId], false);
            $language = Configure::read('network_language.' . $networkId);

            $url = Configure::read('GOOGLE_GARAGE_REVIEWS_URL')
                . self::PLACE_ID . $placeId
                . self::FIELDS_PARAM . $fields
                . self::KEY_PARAM . $key
                . self::LANGUAGE_PARAM . $language;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                CakeLog::debug(print_r("Google - Fetch garage reviews - Error making cURL request: " . curl_error($ch), true));
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Google - Fetch garage reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }
}
