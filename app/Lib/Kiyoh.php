<?php

App::uses('Agn', 'Lib');
App::uses('Network', 'Model');

class Kiyoh
{
    const LOCATION_ID = '?locationId=';
    const TENANT_ID_PARAM = '&tenantId=';
    const DATE_SINCE_PARAM = '?dateSince=';
    const LIMIT_PARAM = '&limit=';
    const DATE_SINCE = '2022-06-01';
    const LIMIT = '50';
    const TENANT_ID = '98';
    const DELAY = '0';
    const LANGUAGE = 'nl';

    private static $kiyohNetworkIds = array(NETWORK_ID_GV, NETWORK_ID_GC);

    /**
     * Get review.
     */
    public static function requestReview($email, $locationId, $name = null)
    {
        try {
            $inviteUrl = Configure::read('KIYOH_REQUEST_REVIEW_URL');

            $delay = self::DELAY;
            $language = self::LANGUAGE;

            $data = array(
                "location_id" => $locationId,
                "invite_email" => $email,
                "delay" => $delay,
                "language" => $language,
            );

            if ($name !== null) {
                $data["first_name"] = $name;
            }

            $ch = curl_init($inviteUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                CakeLog::debug(print_r("Kiyoh - Request reviews - Error making CURL request: " . curl_error($ch), true));
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Kiyoh - Request reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    public function saveKiyohReviews($garageNetworkInfo = null)
    {
        try {
            $networkClass = ClassRegistry::init('Network');
            $agn = new Agn();
            if (isset($garageNetworkInfo)) {
                $response = $this->saveKiyohReviewsByNetwork([$garageNetworkInfo], null);
                $network = $networkClass->findById($garageNetworkInfo['GarageNetwork']['network_id']);
                $agn->purgeCacheReviewsData($network['Network']['guid']);
            } else {
                $response = true;
                foreach (self::$kiyohNetworkIds as $networkId) {

                    $network = $networkClass->findById($networkId);
                    $responsePerNetwork = $this->saveKiyohReviewsByNetwork(null, $networkId);
                    $agn->purgeCacheReviewsData($network['Network']['guid']);
                    if (!$responsePerNetwork) {
                        $response = false;
                    }
                }
            }
            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Kiyoh - Save Kiyoh Reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Save Kiyoh reviews.
     */
    private function saveKiyohReviewsByNetwork($garageNetworkInfo = null, $networkId = null)
    {
        try {
            $garageNetworkClass = ClassRegistry::init('GarageNetwork');
            if (!isset($garageNetworkInfo)) {
                $garageNetworkInfo = $garageNetworkClass->find('all', array(
                    'conditions' => array(
                        'GarageNetwork.id' => 16511,
                        'GarageNetwork.network_id' => $networkId,
                        'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        'GarageNetwork.location_id IS NOT NULL',
                        'GarageNetwork.location_id !=' => '',
                        'GarageNetwork.kiyoh_api_key IS NOT NULL',
                        'GarageNetwork.kiyoh_api_key !=' => '',
                    ),
                    'fields' => array('GarageNetwork.location_id', 'GarageNetwork.kiyoh_api_key', 'GarageNetwork.id')
                ));
            } else {
                $networkId = $garageNetworkInfo[0]['GarageNetwork']['network_id'];
            }
            $reviewsArrayKiyoh = $this->getKiyohReviews($garageNetworkInfo);

            if ($reviewsArrayKiyoh == null || !is_array($reviewsArrayKiyoh)) {
                return false;
            }

            foreach ($reviewsArrayKiyoh['reviewsKiyoh'] as $garageNetworkId => $reviews) {
                $garageNetwork = $garageNetworkClass->findById($garageNetworkId);
                $existingReviews = json_decode($garageNetwork['GarageNetwork']['reviews_info'], true);
                $combinedReviews = $existingReviews == null ? $reviews : array_merge($existingReviews, $reviews);
                usort($combinedReviews, function ($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });
                $limitedReviewsGoogleKiyoh = array_slice($combinedReviews, 0, 50);
                $garageNetwork['GarageNetwork']['reviews_info'] = json_encode($limitedReviewsGoogleKiyoh);
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

            foreach ($reviewsArrayKiyoh['reviewsValuesKiyoh'] as $valueKiyoh) {
                $locationId = $valueKiyoh['locationId'];
                $garageNetworkId = $reviewsArrayKiyoh['locationIdGarageNetworkId'][$locationId] ?? '';
                if (!empty($garageNetworkId)) {
                    $garageNetwork = $garageNetworkClass->findById($garageNetworkId);
                    $googleRating = $garageNetwork['GarageNetwork']['rating'];
                    $googleReviewsNumber = $garageNetwork['GarageNetwork']['reviews_number'];
                    $kiyohRating = ($valueKiyoh['rating']);
                    $kiyohReviewsNumber = $valueKiyoh['reviews_number'];
                    if ($googleReviewsNumber + $kiyohReviewsNumber == 0) {
                        $averageRating = $kiyohRating;
                    } else {
                        $averageRating = (($googleRating * $googleReviewsNumber) + ($kiyohRating * $kiyohReviewsNumber)) / ($googleReviewsNumber + $kiyohReviewsNumber);
                    }
                    $reviewsNumber = $googleReviewsNumber + $kiyohReviewsNumber;
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
            return true;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Kiyoh - Get all reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Get all Kiyoh reviews.
     */
    private function getAllReviews($kiyohApiKey)
    {
        try {
            $url = Configure::read('KIYOH_GET_REVIEWS_URL')
                . self::DATE_SINCE_PARAM . self::DATE_SINCE;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Publication-Api-Token: ' . $kiyohApiKey,
                'User-Agent: CakePHP'
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                CakeLog::debug(print_r("Kiyoh - Get all reviews - Error making CURL request: " . curl_error($ch), true));
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Kiyoh - Get all reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Get Kiyoh garage reviews.
     */
    private function getGarageReviews($locationId, $kiyohApiKey)
    {
        try {
            $url = Configure::read('KIYOH_LATEST_REVIEWS_URL')
                . self::LOCATION_ID . urlencode($locationId)
                . self::LIMIT_PARAM . self::LIMIT
                . self::TENANT_ID_PARAM . self::TENANT_ID;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Publication-Api-Token: ' . $kiyohApiKey,
                'User-Agent: CakePHP'
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                CakeLog::debug(print_r("Kiyoh - Get garages reviews - Error making CURL request: " . curl_error($ch), true));
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Kiyoh - Get garages reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Get Kiyoh reviews GV.
     */
    private function getKiyohReviews($garageNetworkInfo)
    {
        try {
            $locationIdGarageNetworkId = array();

            $uniqueApiKeys = array_unique(array_map(function ($item) {
                return $item['GarageNetwork']['kiyoh_api_key'];
            }, $garageNetworkInfo));

            $allReviewsDataKiyoh = array();
            $allReviewsNumberRating = array();
            foreach ($garageNetworkInfo as $garageNetwork) {
                $locationIdGarageNetworkId[$garageNetwork['GarageNetwork']['location_id']] = $garageNetwork['GarageNetwork']['id'];
                $reviews = $this->getGarageReviews(
                    $garageNetwork['GarageNetwork']['location_id'],
                    $garageNetwork['GarageNetwork']['kiyoh_api_key']
                );
                $reviews = json_decode($reviews, true);
                if (isset($reviews['reviews'])) {
                    foreach ($reviews['reviews'] as $review) {
                        foreach ($review['reviewContent'] as $reviewContent) {
                            if ($reviewContent['questionGroup'] == 'DEFAULT_ONELINER') {
                                $title = $reviewContent['rating'];
                            } elseif ($reviewContent['questionGroup'] == 'DEFAULT_OPINION') {
                                $text = $reviewContent['rating'];
                            }
                        }
                        $reviewData = array(
                            'name' => Texto::encryptDecryptText($review['reviewAuthor'], true),
                            'date' => (new DateTime($review['dateSince']))->format("Y-m-d"),
                            'title' => $title ?? null,
                            'rating' => ($review['rating']) / 2,
                            'text' => $text ?? null,
                            'locationId' => $garageNetwork['GarageNetwork']['location_id'],
                            'kiyoh_api_key' => $garageNetwork['GarageNetwork']['kiyoh_api_key'],
                        );
                        $allReviewsDataKiyoh[$garageNetwork['GarageNetwork']['id']][] = $reviewData;
                    }
                }
            }
            foreach ($uniqueApiKeys as $apiKey) {
                $reviewsNumberRating = json_decode($this->getAllReviews($apiKey), true);
                if (isset($reviewsNumberRating)) {
                    foreach ($reviewsNumberRating as $reviewAverage) {
                        if (isset($reviewAverage['averageRating']) && isset($reviewAverage['numberReviews']) && isset($reviewAverage['locationId'])) {
                            $allReviewsNumberRating[] = array(
                                'rating' => ($reviewAverage['averageRating']) / 2, // Max rating in Kiyoh is 10. We need 5
                                'reviews_number' => $reviewAverage['numberReviews'],
                                'locationId' => $reviewAverage['locationId'],
                            );
                        }
                    }
                }
            }

            return array(
                'reviewsKiyoh' => $allReviewsDataKiyoh,
                'reviewsValuesKiyoh' => $allReviewsNumberRating,
                'locationIdGarageNetworkId' => $locationIdGarageNetworkId,
            );
        } catch (Exception $e) {
            CakeLog::debug(print_r("Kiyoh - Get Kiyoh reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }
}
