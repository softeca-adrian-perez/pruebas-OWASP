<?php

App::uses('Agn', 'Lib');

class FeeFo
{
    public $uses = array(
        "GarageNetwork",
        "Garage",
        "Network"
    );

    const MERCHANT_IDENTIFIER_PARAM = '?merchant_identifier=';
    const REVIEW_COUNT_PARAM = '&review_count=';
    const PRODUCT_SKU = '&product_sku=';
    const PAGE_SIZE_PARAM = '&page_size=';
    const PAGE_PARAM = '&page=';
    const MERCHANT_IDENTIFIER = 'approved-garages';
    const REVIEW_COUNT = 'true';
    const PAGE_SIZE = '100';
    const PAGE = '1';

    /**
     * Get access token.
     */
    private static function getAccessToken()
    {
        try {
            $clientId = FEEFO_CLIENT_ID;
            $clientSecret = Texto::encryptDecryptText(FEEFO_CLIENT_SECRET, false);
            $tokenUrl = FEEFO_REQUEST_TOKEN;

            $data = array(
                "client_id" => $clientId,
                "client_secret" => $clientSecret,
                "grant_type" => "client_credentials",
            );

            $dataEncoded = http_build_query($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataEncoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded",
                "User-Agent: CakePHP"
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (!isset($data['access_token'])) {
                return null;
            }

            return $data['access_token'];
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Get access token - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Make request.
     */
    private function sendRequests($url, $accessToken = null)
    {
        try {
            // if access token is null, try to get a new one
            if ($accessToken == null) {
                $accessToken = $this->getAccessToken();
            }

            if ($accessToken == null) {
                return null;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $accessToken",
                "User-Agent: CakePHP"
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response, true);

            if ($result == null) {
                // it has returned "You cannot consume this service; invalid token"
                // repeat with new token only a second time
                sleep(5);
                $accessToken = $this->getAccessToken();

                if ($accessToken == null) {
                    return null;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Authorization: Bearer $accessToken",
                    "User-Agent: CakePHP"
                ));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($response, true);
            }

            return $result;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Send requests - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * Request review.
     */
    public static function requestReview($productSearchCode, $email, $date, $guid, $name = null, $description = null, $tags = null)
    {
        try {
            $apiKey = Texto::encryptDecryptText(FEEFO_API_KEY, false);
            $merchantIdentifier = self::MERCHANT_IDENTIFIER;

            $accessToken = self::getAccessToken();

            $url = FEEFO_REQUEST_REVIEW_URL;
            $data = array(
                'apiKey' => $apiKey,
                'merchantidentifier' => $merchantIdentifier,
                'description' => $description,
                'productsearchcode' => $productSearchCode,
                'email' => $email,
                'date' => $date,
                'orderref' => $guid
            );
            if ($name !== null) {
                $data['name'] = $name;
            }

            if ($tags !== null) {
                $data['tags'] = $tags;
            }

            $dataEncoded = http_build_query($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataEncoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $accessToken",
                "x-api-key: $apiKey",
                "Content-Type: application/x-www-form-urlencoded",
                "User-Agent: CakePHP"
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            return json_decode($response, true);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Request review - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * Save Feefo garage reviews for AGN.
     */
    public function saveReviews()
    {
        try {
            $reviewsArray = $this->getGaragesReviews();
            if ($reviewsArray == null || !is_array($reviewsArray)) {
                CakeLog::debug(print_r("Feefo - Request review - Error getting data", true));
                return;
            }

            $garageNetworkClass = ClassRegistry::init('GarageNetwork');
            $networkClass = ClassRegistry::init('Network');

            $reviewsRatingGreater3 = array();

            // Last 100 reviews with rating higher than 3
            foreach ($reviewsArray['reviews'] as $garageNetworkId => $reviews) {
                usort($reviews, function ($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });
                foreach ($reviews as $review) {
                    if ($review['rating'] > 3) {
                        if (count($reviewsRatingGreater3) < 100) {
                            $reviewsRatingGreater3[] = $review;
                        } else {
                            $arrayDates = array_column($reviewsRatingGreater3, 'date');
                            $dateMin = min($arrayDates);
                            $keyMinDate = array_search($dateMin, $arrayDates);
                            if ($review['date'] > $dateMin) {
                                $reviewsRatingGreater3[$keyMinDate] = $review;
                            }
                        }
                    }
                }

                $fields = array('reviews_info', 'modification_date');
                $garageNetwork = array(
                    'GarageNetwork' => array(
                        'id' => $garageNetworkId,
                        'reviews_info' => json_encode($reviews),
                        'modification_date' => date('Y-m-d H:i:s')
                    )
                );
                $garageNetworkClass->guardar($garageNetwork, $fields);
            }

            foreach ($reviewsArray['rewiews_values'] as $garageNetworkId => $value) {
                $averageRating = $value[0]['rating'];
                $reviewsNumber = $value[0]['reviews_number'];

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

            // Save reviews networks -> last 100 reviews with rating higher than 3
            usort($reviewsRatingGreater3, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            $fields = array('reviews_info');
            $network = array(
                'Network' => array(
                    'id' => NETWORK_ID_AGN,
                    'reviews_info' => json_encode($reviewsRatingGreater3),
                    'modification_date' => date('Y-m-d H:i:s')
                )
            );
            $networkClass->guardar($network, $fields);

            $agn = new Agn();
            $network = $networkClass->findById(NETWORK_ID_AGN);
            $agn->purgeCacheReviewsData($network['Network']['guid']);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Save reviews - An error has occured: " . $e->getMessage(), true));
        }
    }

    /*
    * Save Feefo network reviews for AGN.
    */
    public function saveNetworkReviews()
    {
        try {
            $networkClass = ClassRegistry::init('Network');

            $networkValues = $this->fetchNetworkRating();
            if ($networkValues == null || !is_array($networkValues) || !isset($networkValues['rating']) || !isset($networkValues['service']['count'])) {
                CakeLog::debug(print_r("Feefo - Request review - Error getting data", true));
                return;
            }

            $networkRating = $networkValues['rating'];
            $networkReviewsNum = $networkValues['service']['count'];

            $fields = array('rating', 'reviews_number');
            $network = array(
                'Network' => array(
                    'id' => NETWORK_ID_AGN,
                    'rating' => $networkRating,
                    'reviews_number' => $networkReviewsNum,
                    'modification_date' => date('Y-m-d H:i:s')
                )
            );
            $networkClass->guardar($network, $fields);
            $agn = new Agn();
            $network = $networkClass->findById(NETWORK_ID_AGN);
            $agn->purgeCacheReviewsData($network['Network']['guid']);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Save network reviews - An error has occured: " . $e->getMessage(), true));
        }
    }

    /**
     * Get Feefo Garage reviews.
     */
    private function getGaragesReviews()
    {
        try {
            $allReviewsData = array();
            $allRatingsCounts = array();

            $garageNetworkClass = ClassRegistry::init('GarageNetwork');

            $productSkus = $garageNetworkClass->find('all', array(
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
                    'GarageNetwork.network_id' => NETWORK_ID_AGN,
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'Garage.g_number_id IS NOT NULL',
                ),
                'fields' => array('Garage.g_number_id', 'GarageNetwork.id', 'Garage.business_name', 'Garage.name'),
            ));

            $accessToken = $this->getAccessToken();
            if ($accessToken != null) {
                foreach ($productSkus as $productSku) {
                    $reviews = $this->fetchGarageReviews($productSku['Garage']['g_number_id'], $accessToken);
                    $reviewsValues = $this->fetchGarageRating($productSku['Garage']['g_number_id'], $accessToken);
                    if ((isset($reviews['reviews'])) && is_array($reviews) && !empty($reviews['reviews'])) {
                        foreach ($reviews['reviews'] as $review) {
                            if (isset($review['products'][0]['review']) && !empty($review['products'][0]['review'])) {
                                $reviewText = $review['products'][0]['review'] ?? '';
                                $words = explode(' ', $reviewText);
                                $title = implode(' ', array_slice($words, 0, 4));
                                if (count($words) > 4) {
                                    $title .= '...';
                                }
                            }

                            $reviewData = array(
                                'name' => isset($review['customer']['display_name']) ? Texto::encryptDecryptText($review['customer']['display_name'], true) : '',
                                'date' => date('Y-m-d', strtotime($review['products'][0]['created_at'])),
                                'title' => $title ?? '',
                                'rating' => $review['products'][0]['rating']['rating'],
                                'text' => $review['products'][0]['review'] ?? '',
                                'sku' => $review['products'][0]['product']['sku'],
                                'garage_name' => !empty($productSku['Garage']['business_name']) ? $productSku['Garage']['business_name'] : $productSku['Garage']['garage_name']
                            );
                            $allReviewsData[$productSku['GarageNetwork']['id']][] = $reviewData;
                        }
                    }
                    if (isset($reviewsValues['meta']['count']) && isset($reviewsValues['rating']['rating'])) {
                        $values = array(
                            'rating' => $reviewsValues['rating']['rating'],
                            'reviews_number' => $reviewsValues['meta']['count'],
                            'sku' => $reviewsValues['product']['sku'],
                        );
                        $allRatingsCounts[$productSku['GarageNetwork']['id']][] = $values;
                    }
                }
            }

            return array(
                'reviews' => $allReviewsData,
                'rewiews_values' => $allRatingsCounts,
            );
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Get garages reviews reviews - An error has occured: " . $e->getMessage(), true));
        }
    }

    /**
     * Fetch garage rating.
     */
    private function fetchGarageRating($productSku, $accessToken)
    {
        try {
            $url = FEEFO_GARAGE_RATING_URL
                . self::MERCHANT_IDENTIFIER_PARAM . self::MERCHANT_IDENTIFIER
                . self::PAGE_SIZE_PARAM . self::PAGE_SIZE
                . self::PRODUCT_SKU . urlencode($productSku);

            $data = $this->sendRequests($url, $accessToken);

            return $data;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Fetch Garage Rating - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Fetch garage reviews.
     */
    private function fetchGarageReviews($productSku, $accessToken)
    {
        try {
            if ($productSku == null) {
                return array();
            }

            $url = FEEFO_GARAGE_REVIEWS_URL
                . self::MERCHANT_IDENTIFIER_PARAM . self::MERCHANT_IDENTIFIER
                . self::PAGE_SIZE_PARAM . self::PAGE_SIZE
                . self::PRODUCT_SKU . urlencode($productSku);

            return $this->sendRequests($url, $accessToken);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Fetch garage reviews - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Fetch network rating.
     */
    private function fetchNetworkRating()
    {
        try {
            $url = FEEFO_NETWORK_RATING_URL
                . self::MERCHANT_IDENTIFIER_PARAM . self::MERCHANT_IDENTIFIER
                . self::PAGE_SIZE_PARAM . self::PAGE_SIZE;

            $data = $this->sendRequests($url);

            return $data['rating'];
        } catch (Exception $e) {
            CakeLog::debug(print_r("Feefo - Fetch network rating - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }
}
