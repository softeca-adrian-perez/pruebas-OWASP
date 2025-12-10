<?php
App::uses('Feefo', 'Lib');
App::uses('Kiyoh', 'Lib');

class ReviewsController extends AppController
{
    public $uses = array(
        "GarageNetwork",
        "Network",
        "Garage",
        "Work",
        "City",
        "GarageNetworkWork",
        "Service",
        "GarageNetworkService",
        "Vehicle",
        "GarageNetworkVehicle",
        "ReviewRequest",
        "NetworkCity"
    );

    const NUMBER_REVIEWS_RETURN = 100;

    /**
     * View to get all reviews of a garage, if its a GV or GC garage kiyoh can be configured here too .
     *
     * @param garageNetworkId $reviews
     */
    public function home($garageNetworkId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);
        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        if (empty($garageNetwork) || empty($garage)) {
            throw new UnauthorizedException();
        }

        $pagination_size = ConstantsPagination::SIZE_PAGE_SMALL;
        $pagination_page = isset($this->request->named['page']) ? $this->request->named['page'] : 1;

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        $reviewsInfo = json_decode($garageNetwork['GarageNetwork']['reviews_info']);
        $averageRating = $garageNetwork['GarageNetwork']['rating'];
        $reviewsNumber = $garageNetwork['GarageNetwork']['reviews_number'];

        $this->Acceso->checkGarageAccess($garageId);

        $reviewData = [];
        if (!empty($reviewsInfo)) {
            foreach ($reviewsInfo as $review) {
                $review = get_object_vars($review);

                $name = $review['name'];
                $date = $review['date'];
                $title = $review['title'];
                $rating = $review['rating'];
                $text = $review['text'];

                $reviewEntry = [
                    'name' => $name,
                    'date' => $date,
                    'title' => $title,
                    'rating' => $rating,
                    'text' => $text,
                    'source' => '',
                ];

                switch ($networkId) {
                    case NETWORK_ID_AGN:
                        $reviewEntry['source'] = 'Feefo';
                        $link = 'www.feefo.com';
                        $urlReview = 'https://www.feefo.com/en-GB/reviews/approved-garages?displayFeedbackType=BOTH&timeFrame=YEAR';
                        break;
                    case NETWORK_ID_GV:
                    case NETWORK_ID_GC:
                        $reviewEntry['source'] = 'Google';
                        $link = 'www.google.com';
                        $urlReview = Configure::read('GOOGLE_REVIEWS_LINK');
                        break;
                    default:
                        // If networkId does not match any known type, return an error or handle as needed.
                        break;
                }
                if (($networkId == NETWORK_ID_GV || $networkId == NETWORK_ID_GC) && isset($review['location_id'])) {
                    $reviewEntry['source'] = 'Kiyoh';
                    $linkKiyoh = 'www.kiyoh.com';
                    $urlKiyoh = 'https://www.kiyoh.com/';
                    $this->set('link_kiyoh', $linkKiyoh);
                    $this->set('url_kiyoh', $urlKiyoh);
                }

                $reviewData[] = $reviewEntry;
            }
        }

        $this->set(array(
            'garage_network_id' => $garageNetworkId,
            'garage_name' => $garage['Garage']['name'],
            'rating' => isset($rating) ? $rating : null,
            'review_data' => $reviewData,
            'url_review' => isset($urlReview) ? $urlReview : null,
            'link' => isset($link) ? $link : null,
            'network_id' => $networkId,
            'kiyoh_api_key' => isset($garageNetwork['GarageNetwork']['kiyoh_api_key']) ? $garageNetwork['GarageNetwork']['kiyoh_api_key'] : null,
            'location_id' => isset($garageNetwork['GarageNetwork']['location_id']) ? $garageNetwork['GarageNetwork']['location_id'] : null,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active']
        ));

        $this->set('reviewsPaginator', array_slice($reviewData, $pagination_size * ($pagination_page - 1), $pagination_size));
        $this->set('pagination_page', $pagination_page);
        $this->set('pagination_size', $pagination_size);
        $this->set('pagination_count', count($reviewData));
        $this->set('average_rating', $averageRating);
        $this->set('total_reviews', $reviewsNumber);

        if (!$this->request->is('get')) {
            $data = $this->request->data;
            if ($networkId == NETWORK_ID_GV || $networkId == NETWORK_ID_GC) {
                if (isset($data['kiyoh_active'])) {
                    $garageNetwork['GarageNetwork']['location_id'] = $data['GarageNetwork']['location_id'];
                    $garageNetwork['GarageNetwork']['kiyoh_api_key'] = $data['GarageNetwork']['kiyoh_api_key'];
                } else {
                    $garageNetwork['GarageNetwork']['location_id'] = null;
                    $garageNetwork['GarageNetwork']['kiyoh_api_key'] = null;
                }

                $result = $this->GarageNetwork->save($garageNetwork);
                if ($result) {
                    // Save reviews Kiyoh
                    $kiyohReviews = new Kiyoh();
                    $kiyohSaved = $kiyohReviews->saveKiyohReviews($garageNetwork);
                    if ($kiyohSaved) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED, __l()));
                    }
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED, __l()));
                }
            } elseif ($networkId == NETWORK_ID_AGN) {
                //Check none of the three fields is empty
                if (isset($data['RequestFeefoReview'])) {
                    foreach ($data['RequestFeefoReview'] as $key => $value) {
                        if (empty(trim($value))) {
                            $this->Session->setFlashError(__t('Config.Empty_field', __l()));
                            $this->redirect(
                                array(
                                    'controller' => 'reviews',
                                    'action' => 'home',
                                    $garageNetworkId
                                )
                            );
                        }
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED, __l()));
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                }
                //Check its a valid email
                if (!isset($data['RequestFeefoReview']['email']) || !filter_var($data['RequestFeefoReview']['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->Session->setFlashError(__t('Validation.Email_incorrect_format', __l()));
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                }
                //Check if its a valid date
                $dateTime = DateTime::createFromFormat('d-m-Y', $data['RequestFeefoReview']['date']);
                if ($dateTime == false || array_sum($dateTime::getLastErrors())) {
                    $this->Session->setFlashError(__t('CRM.Enter_valid_date', __l()));
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                }
                $finalDate = $dateTime->format('Y-m-d');
                //Check if name is valid
                $nameLength = strlen(trim($data['RequestFeefoReview']['name']));
                if ($nameLength > 190) {
                    $this->Session->setFlashError(__t('Validation.Name_is_too_long', __l()));
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                }

                if (!empty($garage['Garage']['g_number_id']) && !empty(trim($garage['Garage']['business_name']))) {
                    $requestGuid = CakeText::uuid();
                    $resp = Feefo::requestReview(
                        $garage['Garage']['g_number_id'],
                        $data['RequestFeefoReview']['email'],
                        $finalDate,
                        $requestGuid,
                        $data['RequestFeefoReview']['name'],
                        $garage['Garage']['business_name'],
                        '[booking_type=Manual]'
                    );

                    $request = array(
                        $garage['Garage']['g_number_id'],
                        Texto::encryptDecryptText($data['RequestFeefoReview']['email'], true),
                        $finalDate,
                        $requestGuid,
                        Texto::encryptDecryptText($data['RequestFeefoReview']['name'], true),
                        $garage['Garage']['business_name'],
                        '[booking_type=Manual]',
                    );
                    $this->ReviewRequest->add(
                        null,
                        $garage['Garage']['aag_region_id'],
                        json_encode($request),
                        json_encode($resp)
                    );
                    $this->Session->setFlashSuccess(__t('Review.Request_successful', __l()));
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED, __l()));
                    $this->redirect(
                        array(
                            'controller' => 'reviews',
                            'action' => 'home',
                            $garageNetworkId
                        )
                    );
                }
            }
        }
    }

    /**
     * API to get all the information about a review.
     *
     * @param garage_id $garageId, is the garage guid
     * @param network_id $networkId
     * @param page $page
     * @param pae_size $pageSize
     */
    public function review_info()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $garageId = isset($dataReceived->garage_id) ? $dataReceived->garage_id : null;
            //AGN sends us a guid as the garage_id, so we turn it into the id
            if ($garageId) {
                $foundGarage = $this->Garage->findByGuid($garageId, ['id']);
                if (isset($foundGarage['Garage']['id'])) {
                    $garageId = $foundGarage['Garage']['id'];
                } else {
                    $garageId = null;
                }
            }
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;

            $sendData = array();

            if (empty($garageId) || empty($networkId)) {
                return $this->returnJsonResult($sendData);
            }

            $garageNetworkInfo = $this->GarageNetwork->find("first", array(
                "conditions" => array(
                    "garage_id" => $garageId,
                    "network_id" => $networkId,
                    "status" => ConstantsNetworksStatus::LIVE
                )
            ));

            if (!$garageNetworkInfo) {
                return $this->returnJsonResult($sendData);
            }

            $review_info = $garageNetworkInfo['GarageNetwork']['reviews_info'];

            if (!$review_info) {
                return $this->returnJsonResult($sendData);
            }

            $review_decoded = json_decode($review_info, true);

            foreach ($review_decoded as $review) {
                $sendData['garage_id'] = $dataReceived->garage_id; //We return the garages guid as the id
                if ($review['rating'] >= 3) {
                    $sendData['reviews'][] = array(
                        'name' => Texto::encryptDecryptText($review['name'], false),
                        'date' => $review['date'],
                        'title' => $review['title'],
                        'rating' => $review['rating'],
                        'review' => $review['text'],
                    );
                }
            }

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of network review.
     */
    public function review_network()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;

            $sendData = array();

            if (empty($networkId)) {
                return $this->returnJsonResult($sendData);
            }

            $networkInfo = $this->Network->findById($networkId);

            if (!$networkInfo) {
                return $this->returnJsonResult($sendData);
            }

            $review_info = $networkInfo['Network']['reviews_info'];
            $network_rating = $networkInfo['Network']['rating'];
            $reviews_number =  $networkInfo['Network']['reviews_number'];

            $sendData['network_id'] = $networkId;
            if ($network_rating) {
                $sendData['rating'] = $network_rating;
            }
            if ($reviews_number) {
                $sendData['reviews_number'] = $reviews_number;
            }
            //reviews_final is used weather there are reviews for a network or not
            $reviews_final = array();

            //There are reviews for the network
            if ($review_info) {
                $review_decoded = json_decode($review_info, true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                            'garage_name' => $review['garage_name']
                        );
                        $this->getReviewsByDates($reviews_final, $reviewFormat); // $reviews_final -> by reference
                    }
                }
                //Sorts reviews by date (newest first)
                usort($reviews_final, function ($item1, $item2) {
                    return $item2['date'] <=> $item1['date'];
                });
            }

            //No reviews for the network, so it searches reviews for the garages within the network
            if (empty($review_info)) {

                //SQL query
                $joins = array();

                $joins[] = array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id' => $networkId,
                        'GarageNetwork.network_id = Network.id',
                        'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        'GarageNetwork.reviews_info IS NOT NULL',
                    ),
                );

                $fields = array(
                    'GarageNetwork.reviews_info'
                );

                $find = array(
                    'fields' => $fields,
                    'joins' => $joins
                );

                $garages_networks = $this->GarageNetwork->find('list', $find);

                if (!$garages_networks) {
                    return $this->returnJsonResult($sendData);
                }

                $sendData['network_id'] = $networkId;
                if ($network_rating) {
                    $sendData['rating'] = $network_rating;
                }
                if ($reviews_number) {
                    $sendData['reviews_number'] = $reviews_number;
                }

                foreach ($garages_networks as $garage_network) {
                    $review_decoded = json_decode($garage_network, true);

                    foreach ($review_decoded as $review) {
                        if ($review['rating'] >= 3) {
                            $reviewFormat = array(
                                'name' => Texto::encryptDecryptText($review['name'], false),
                                'date' => $review['date'],
                                'title' => $review['title'],
                                'rating' => $review['rating'],
                                'review' => $review['text'],
                            );
                            $this->getReviewsByDates($reviews_final, $reviewFormat); // $reviews_final -> by reference
                        }
                    }
                }
                //Sorts final array of reviews by date (newest first)
                usort($reviews_final, function ($item1, $item2) {
                    return $item2['date'] <=> $item1['date'];
                });
            }

            //Divides array of reviews by page_size and selects the page.
            $sendData['reviews'] = $reviews_final;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_city()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $cityId = isset($dataReceived->city_id) ? $dataReceived->city_id : '';

            $sendData = array();

            if (empty($networkId) || empty($cityId)) {
                return $this->returnJsonResult($sendData);
            }

            $cityInfo = $this->City->getNetworkCityInfo($networkId, $cityId);

            if ($cityInfo) {
                $latitude = $cityInfo['City']['latitude'];
                $longitude = $cityInfo['City']['longitude'];
            }

            $networkInfo = $this->Network->findById($networkId);

            if (!$networkInfo || !$cityInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query
            $joins = array();

            $joins[] = array(
                'alias' => 'Garage',
                'table' => 'garages',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.garage_id = Garage.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.reviews_info IS NOT NULL',
                    'Garage.latitude IS NOT NULL',
                    'Garage.longitude IS NOT NULL',
                ),
            );

            $fields = array(
                'GarageNetwork.reviews_info'
            );

            //Calculates distance between city and garage
            if (!empty($latitude) && !empty($longitude)) {
                $distanceKmsSQL = '60 * 1.1515 * 180/PI()
                    * acos(
                        cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . '))
                        + sin(radians(' . $latitude . ')) * sin(radians(latitude))
                    )
                    * 1.609344';
            }

            //If city doesn't have coordinates, distance will be null
            if (empty($distanceKmsSQL)) {
                return $this->returnJsonResult($sendData);
            }

            //Sorts by nearest
            $order = array($distanceKmsSQL);

            $find = array(
                'fields' => $fields,
                'joins' => $joins,
                'order' => $order
            );

            $garages_networks = $this->GarageNetwork->find('all', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network['GarageNetwork']['reviews_info'], true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_work()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $workId = isset($dataReceived->work_id) ? $dataReceived->work_id : null;

            $sendData = array();

            if (empty($networkId) || empty($workId)) {
                return $this->returnJsonResult($sendData);
            }

            $networkInfo = $this->Network->findById($networkId);

            $workInfo = $this->Work->find("all", array(
                "conditions" => array(
                    "id" => $workId,
                    "network_id" => $networkId,
                    "active" => "1"
                )
            ));

            if (!$networkInfo || !$workInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query

            $joins = array();

            $joins[] = array(
                'alias' => 'GarageNetworkWork',
                'table' => 'garages_networks_works',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkWork.work_id' => $workId,
                    'GarageNetworkWork.garage_network_id = GarageNetwork.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.reviews_info IS NOT NULL',
                ),
            );

            $find = array(
                'fields' => array('GarageNetwork.reviews_info'),
                'joins' => $joins
            );

            $garages_networks = $this->GarageNetwork->find('list', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $sendData['work_id'] = $workId;

            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network, true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_work_city()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $workId = isset($dataReceived->work_id) ? $dataReceived->work_id : null;
            $cityId = isset($dataReceived->city_id) ? $dataReceived->city_id : '';

            $sendData = array();

            if (empty($networkId) || empty($workId) || empty($cityId)) {
                return $this->returnJsonResult($sendData);
            }

            $cityInfo = $this->City->getNetworkCityInfo($networkId, $cityId);

            if ($cityInfo) {
                $latitude = $cityInfo['City']['latitude'];
                $longitude = $cityInfo['City']['longitude'];
            }

            $networkInfo = $this->Network->findById($networkId);

            $workInfo = $this->Work->find("all", array(
                "conditions" => array(
                    "id" => $workId,
                    "network_id" => $networkId,
                    "active" => "1"
                )
            ));

            if (!$networkInfo || !$workInfo || !$cityInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query
            $joins = array();

            $joins[] = array(
                'alias' => 'GarageNetworkWork',
                'table' => 'garages_networks_works',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkWork.work_id' => $workId,
                    'GarageNetworkWork.garage_network_id = GarageNetwork.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.reviews_info IS NOT NULL',
                ),
            );

            $joins[] = array(
                'alias' => 'Garage',
                'table' => 'garages',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetwork.garage_id = Garage.id',
                    'Garage.latitude IS NOT NULL',
                    'Garage.longitude IS NOT NULL',
                ),
            );

            $fields = array(
                'GarageNetwork.reviews_info'
            );

            //Calculates distance between city and garage
            if (!empty($latitude) && !empty($longitude)) {
                $distanceKmsSQL = '60 * 1.1515 * 180/PI()
                    * acos(
                        cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . '))
                        + sin(radians(' . $latitude . ')) * sin(radians(latitude))
                    )
                    * 1.609344';
            }

            //If city doesn't have coordinates, distance will be null
            if (empty($distanceKmsSQL)) {
                return $this->returnJsonResult($sendData);
            }

            //Sorts by nearest
            $order = array($distanceKmsSQL);

            $find = array(
                'fields' => $fields,
                'joins' => $joins,
                'order' => $order,
            );

            $garages_networks = $this->GarageNetwork->find('all', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $sendData['work_id'] = $workId;
            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network['GarageNetwork']['reviews_info'], true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_service()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $serviceId = isset($dataReceived->service_id) ? $dataReceived->service_id : null;

            $sendData = array();

            if (empty($networkId) || empty($serviceId)) {
                return $this->returnJsonResult($sendData);
            }

            $networkInfo = $this->Network->findById($networkId);

            $serviceInfo = $this->Service->findById($serviceId);

            if (!$networkInfo || !$serviceInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query

            $joins = array();

            $joins[] = array(
                'alias' => 'GarageNetworkService',
                'table' => 'garages_networks_services',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkService.service_id' => $serviceId,
                    'GarageNetworkService.garage_network_id = GarageNetwork.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.reviews_info IS NOT NULL',
                ),
            );

            $find = array(
                'fields' => array('GarageNetwork.reviews_info'),
                'joins' => $joins
            );

            $garages_networks = $this->GarageNetwork->find('list', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $sendData['service_id'] = $serviceId;
            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network, true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_service_city()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $serviceId = isset($dataReceived->service_id) ? $dataReceived->service_id : null;
            $cityId = isset($dataReceived->city_id) ? $dataReceived->city_id : null;

            $sendData = array();

            if (empty($networkId) || empty($serviceId) || empty($cityId)) {
                return $this->returnJsonResult($sendData);
            }

            $cityInfo = $this->City->getNetworkCityInfo($networkId, $cityId);

            if ($cityInfo) {
                $latitude = $cityInfo['City']['latitude'];
                $longitude = $cityInfo['City']['longitude'];
            }

            $networkInfo = $this->Network->findById($networkId);
            $serviceInfo = $this->Service->findById($serviceId);

            if (!$networkInfo || !$serviceInfo || !$cityInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query
            $joins = array();

            $joins[] = array(
                'alias' => 'GarageNetworkService',
                'table' => 'garages_networks_services',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkService.service_id' => $serviceId,
                    'GarageNetworkService.garage_network_id = GarageNetwork.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.reviews_info IS NOT NULL',
                ),
            );

            $joins[] = array(
                'alias' => 'Garage',
                'table' => 'garages',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetwork.garage_id = Garage.id',
                    'Garage.latitude IS NOT NULL',
                    'Garage.longitude IS NOT NULL',
                ),
            );

            $fields = array(
                'GarageNetwork.reviews_info'
            );

            //Calculates distance between city and garage
            if (!empty($latitude) && !empty($longitude)) {
                $distanceKmsSQL = '60 * 1.1515 * 180/PI()
                    * acos(
                        cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . '))
                        + sin(radians(' . $latitude . ')) * sin(radians(latitude))
                    )
                    * 1.609344';
            }

            //If city doesn't have coordinates, distance will be null
            if (empty($distanceKmsSQL)) {
                return $this->returnJsonResult($sendData);
            }

            //Sorts by nearest
            $order = array($distanceKmsSQL);

            $find = array(
                'fields' => $fields,
                'joins' => $joins,
                'order' => $order,
            );

            $garages_networks = $this->GarageNetwork->find('all', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $sendData['service_id'] = $serviceId;
            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network['GarageNetwork']['reviews_info'], true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_vehicle()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $vehicleId = isset($dataReceived->vehicle_id) ? $dataReceived->vehicle_id : null;

            $sendData = array();

            if (empty($networkId) || empty($vehicleId)) {
                return $this->returnJsonResult($sendData);
            }

            $networkInfo = $this->Network->findById($networkId);
            $vehicleInfo = $this->Vehicle->findById($vehicleId);

            if (!$networkInfo || !$vehicleInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query

            $joins = array();

            $joins[] = array(
                'alias' => 'GarageNetworkVehicle',
                'table' => 'garages_networks_vehicles',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkVehicle.vehicle_id' => $vehicleId,
                    'GarageNetworkVehicle.garage_network_id = GarageNetwork.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.reviews_info IS NOT NULL',
                ),
            );

            $find = array(
                'fields' => array('GarageNetwork.reviews_info'),
                'joins' => $joins
            );

            $garages_networks = $this->GarageNetwork->find('list', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $sendData['vehicle_id'] = $vehicleId;
            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network, true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get info of review.
     */
    public function review_network_vehicle_city()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $vehicleId = isset($dataReceived->vehicle_id) ? $dataReceived->vehicle_id : null;
            $cityId = isset($dataReceived->city_id) ? $dataReceived->city_id : null;

            $sendData = array();

            if (empty($networkId) || empty($vehicleId) || empty($cityId)) {
                return $this->returnJsonResult($sendData);
            }

            $cityInfo = $this->City->getNetworkCityInfo($networkId, $cityId);

            if ($cityInfo) {
                $latitude = $cityInfo['City']['latitude'];
                $longitude = $cityInfo['City']['longitude'];
            }

            $networkInfo = $this->Network->findById($networkId);
            $vehicleInfo = $this->Vehicle->findById($vehicleId);

            if (!$networkInfo || !$vehicleInfo || !$cityInfo) {
                return $this->returnJsonResult($sendData);
            }

            //SQL query
            $joins = array();

            $joins[] = array(
                'alias' => 'GarageNetworkVehicle',
                'table' => 'garages_networks_vehicles',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkVehicle.vehicle_id' => $vehicleId,
                    'GarageNetworkVehicle.garage_network_id = GarageNetwork.id',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.reviews_info IS NOT NULL',
                ),
            );

            $joins[] = array(
                'alias' => 'Garage',
                'table' => 'garages',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetwork.garage_id = Garage.id',
                    'Garage.latitude IS NOT NULL',
                    'Garage.longitude IS NOT NULL',
                ),
            );

            $fields = array(
                'GarageNetwork.reviews_info'
            );

            //Calculates distance between city and garage
            if (!empty($latitude) && !empty($longitude)) {
                $distanceKmsSQL = '60 * 1.1515 * 180/PI()
                    * acos(
                        cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . '))
                        + sin(radians(' . $latitude . ')) * sin(radians(latitude))
                    )
                    * 1.609344';
            }

            //If city doesn't have coordinates, distance will be null
            if (empty($distanceKmsSQL)) {
                return $this->returnJsonResult($sendData);
            }

            //Sorts by nearest
            $order = array($distanceKmsSQL);

            $find = array(
                'fields' => $fields,
                'joins' => $joins,
                'order' => $order,
            );

            $garages_networks = $this->GarageNetwork->find('all', $find);

            if (!$garages_networks) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['network_id'] = $networkId;
            $sendData['vehicle_id'] = $vehicleId;
            $reviews_garages = array();

            foreach ($garages_networks as $garage_network) {
                $review_decoded = json_decode($garage_network['GarageNetwork']['reviews_info'], true);

                foreach ($review_decoded as $review) {
                    if ($review['rating'] >= 3) {
                        $reviewFormat = array(
                            'name' => Texto::encryptDecryptText($review['name'], false),
                            'date' => $review['date'],
                            'title' => $review['title'],
                            'rating' => $review['rating'],
                            'review' => $review['text'],
                        );
                        $this->getReviewsByDates($reviews_garages, $reviewFormat); // $reviews_garages -> by reference
                    }
                }
            }

            //Sorts final array of reviews by date (newest first)
            usort($reviews_garages, function ($item1, $item2) {
                return $item2['date'] <=> $item1['date'];
            });

            $sendData['reviews'] = $reviews_garages;
            return $this->returnJsonResult($sendData);
        }
    }

    private function getReviewsByDates(&$reviews, $reviewFormat)
    {
        if (count($reviews) > self::NUMBER_REVIEWS_RETURN) {
            $dates = array_column($reviews, 'date');
            $min_date = min($dates);
            if ($reviewFormat['date'] > $min_date) {
                $key_min_date = array_search($min_date, $dates);
                $reviews[$key_min_date] = $reviewFormat;
            }
        } else {
            $reviews[] = $reviewFormat;
        }
    }
}
