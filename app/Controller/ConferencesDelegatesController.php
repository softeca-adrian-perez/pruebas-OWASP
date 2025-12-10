<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class ConferencesDelegatesController extends AppController
{
    public $uses = array(
        'ConferenceDelegate',
        'Conference',
        'Venue',
        'Garage',
        'Supplier',
        'Distributor',
        'RoomType',
        'StandSize',
        'Contact',
        'GarageContactStaff',
        'DistributorContactStaff',
        'Week',
        'Room',
        'TradeShow',
        'Dinner',
        'Distributor',
    );

    /**
     * Conferences delegates home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)
        ) {
            $searcher = $this->request->query;
            $this->request->data['Buscador'] = $searcher;

            $arrayDistributorName = array();
            $arrayGarageName = array();
            $arrayVenueName = array();
            $arrayContactName = array();
            $arraySupplierName = array();

            if (!empty($searcher['distributor_name'])) {
                $arrayDistributorName = $this->Distributor->getDistributorsNameByIdDistributor($searcher['distributor_name']);
            }
            if (!empty($searcher['garage_name'])) {
                $arrayGarageName = $this->Garage->getListByIdGarage($searcher['garage_name']);
            }
            if (!empty($searcher['supplier_name'])) {
                $arraySupplierName = $this->Supplier->getSupplierNameById($searcher['supplier_name']);
            }
            if (!empty($searcher['venue_name'])) {
                $arrayVenueName = $this->Venue->getVenueNameById($searcher['venue_name']);
            }
            if (!empty($searcher['contact'])) {
                $arrayContactName = $this->Contact->getDelegateNameById($searcher['contact']);
            }

            $delegates = $this->custom_pagination(
                $this->ConferenceDelegate->_query($aagRegionId),
                $this->ConferenceDelegate->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'ConferenceDelegate',
                null,
                'PaginatorOrderCustom'
            );
            $conferences = $this->Conference->getCompleteListRegionActive($aagRegionId);

            $this->set(array(
                'delegates' => $delegates,
                'conferences' => $conferences,
                'array_distributor_name' => $arrayDistributorName,
                'array_garage_name' => $arrayGarageName,
                'array_venue_name' => $arrayVenueName,
                'array_contact_name' => $arrayContactName,
                'array_supplier_name' => $arraySupplierName
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create ConferenceDelegate.
     */
    public function add()
    {
        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $conferences = $this->Conference->getCompleteListRegionActive($aagRegionId);
            $hotels = $this->Venue->getVenuesHotel($aagRegionId);
            $roomsTypes = $this->RoomType->getListOfData();
            $standsSizes = $this->StandSize->getListOfData();
            $weekend = $this->Week->getListWeekend();
            $allWeek = $this->Week->search_list();

            $booleanOption = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'conferences_delegates',
                    'action' => 'home',
                ),
            );

            if (!$this->request->is('get')) {
                if ($this->ConferenceDelegate->add($this->request->data)) {
                    $rooms = $this->request->data['ConferenceDelegate']['room'];
                    if (!empty($rooms)) {
                        $dataRooms = array();
                        foreach ($rooms as $room) {
                            $dataRooms[] = array(
                                'conferences_delegates_id' => $this->ConferenceDelegate->id,
                                'weeks_id' => $room
                            );
                        }
                        $this->Room->saveRoomData($dataRooms);
                    }
                    $tradesShows = $this->request->data['ConferenceDelegate']['trade_show'];
                    if (!empty($tradesShows)) {
                        $dataTradesShows = array();
                        foreach ($tradesShows as $tradeShow) {
                            $dataTradesShows[] = array(
                                'conferences_delegates_id' => $this->ConferenceDelegate->id,
                                'weeks_id' => $tradeShow
                            );
                        }
                        $this->TradeShow->saveTradeShowData($dataTradesShows);
                    }
                    $dinners = $this->request->data['ConferenceDelegate']['dinner'];
                    if (!empty($dinners)) {
                        $dataDinners = array();
                        foreach ($dinners as $dinner) {
                            $dataDinners[] = array(
                                'conferences_delegates_id' => $this->ConferenceDelegate->id,
                                'weeks_id' => $dinner
                            );
                        }
                        $this->Dinner->saveDinnerData($dataDinners);
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'conferences_delegates',
                            'action' => 'edit',
                            $this->ConferenceDelegate->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'conferences' => $conferences,
                'hotels' => $hotels,
                'rooms_types' => $roomsTypes,
                'booleanOption' => $booleanOption,
                'stands_sizes' => $standsSizes,
                'select_checked' => null,
                'id_selected_check' => null,
                'weekend' => $weekend,
                'all_week' => $allWeek,
                'room_selected' => null,
                'trade_show_selected' => null,
                'dinner_selected' => null,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit ConferenceDelegate.
     */
    public function edit($conference_delegate_id)
    {
        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $conferenceDelegate = $this->ConferenceDelegate->findById($conference_delegate_id);
            if (!$conferenceDelegate) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_CONFERENCE));
                $this->redirect(
                    array(
                        'controller' => 'conferences_delegates',
                        'action' => 'home',
                    )
                );
            }

            $conference = $this->Conference->findByIdAndAagRegionId($conferenceDelegate['ConferenceDelegate']['conference_id']);
            if (!$conference) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_CONFERENCE));
                $this->redirect(
                    array(
                        'controller' => 'conferences_delegates',
                        'action' => 'home',
                    )
                );
            }

            $selectChecked = null;
            $idSelectedCheck = null;
            $venues = $this->Venue->findListByActive(ConstantsBooleans::YES);
            $conferences = $this->Conference->findListByStatus(ConstantsBooleans::YES);
            $hotels = $this->Venue->getVenuesHotel($aagRegionId);
            $roomsTypes = $this->RoomType->getListOfData();
            $standsSizes = $this->StandSize->getListOfData();
            $garage = $conferenceDelegate['ConferenceDelegate']['garages_id'];
            $distributor = $conferenceDelegate['ConferenceDelegate']['distributors_id'];
            $supplier = $conferenceDelegate['ConferenceDelegate']['suppliers_id'];
            $weekend = $this->Week->getListWeekend();
            $allWeek = $this->Week->search_list();

            if (!empty($garage)) {
                $selectChecked = $this->Garage->getCompleteList($aagRegionId);
                $idSelectedCheck = $garage;
                $distributor = null;
                $supplier = null;
                $arrayDistributorName = null;
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($garage);
            } elseif (!empty($distributor)) {
                $selectChecked = $this->Distributor->getCompleteList($aagRegionId);
                $idSelectedCheck = $distributor;
                $garage = null;
                $supplier = null;
                $arrayGarageName = null;
                $arrayDistributorName = $this->Distributor->getDistributorsNameByIdDistributor($distributor);
            }

            $booleanOption = array(
                ConstantsBooleans::NO => __t('General.DNo'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'conferences_delegates',
                    'action' => 'home',
                ),
            );

            if ($this->request->is('get')) {
                $conferenceDelegate['ConferenceDelegate']['booking_date'] = Fecha::toFormatoVistaFecha($conferenceDelegate['ConferenceDelegate']['booking_date']);
                $this->request->data = $conferenceDelegate;
            } else {
                if ($this->ConferenceDelegate->edit($this->request->data)) {
                    $rooms = $this->request->data['ConferenceDelegate']['room'];
                    if ($rooms) {
                        $dataRooms = array();
                        foreach ($rooms as $room) {
                            $dataRooms[] = array(
                                'conferences_delegates_id' => $this->ConferenceDelegate->id,
                                'weeks_id' => $room
                            );
                        }
                        $this->Room->deleteRoomByConferenceDelegate($conference_delegate_id);
                        $this->Room->saveRoomData($dataRooms);
                    } else {
                        $this->Room->deleteRoomByConferenceDelegate($conference_delegate_id);
                    }
                    $tradesShows = $this->request->data['ConferenceDelegate']['trade_show'];
                    if ($tradesShows) {
                        $dataTradesShows = array();
                        foreach ($tradesShows as $tradeShow) {
                            $dataTradesShows[] = array(
                                'conferences_delegates_id' => $this->ConferenceDelegate->id,
                                'weeks_id' => $tradeShow
                            );
                        }
                        $this->TradeShow->deleteTradeShowByConferenceDelegate($conference_delegate_id);
                        $this->TradeShow->saveTradeShowData($dataTradesShows);
                    } else {
                        $this->TradeShow->deleteTradeShowByConferenceDelegate($conference_delegate_id);
                    }
                    $dinners = $this->request->data['ConferenceDelegate']['dinner'];
                    if ($dinners) {
                        $dataDinners = array();
                        foreach ($dinners as $dinner) {
                            $dataDinners[] = array(
                                'conferences_delegates_id' => $this->ConferenceDelegate->id,
                                'weeks_id' => $dinner
                            );
                        }
                        $this->Dinner->deleteDinnerByConferenceDelegate($conference_delegate_id);
                        $this->Dinner->saveDinnerData($dataDinners);
                    } else {
                        $this->Dinner->deleteDinnerByConferenceDelegate($conference_delegate_id);
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'delegate' => $conferenceDelegate,
                'venues' => $venues,
                'conferences' => $conferences,
                'hotels' => $hotels,
                'rooms_types' => $roomsTypes,
                'booleanOption' => $booleanOption,
                'stands_sizes' => $standsSizes,
                'cancel_action' => $cancelAction,
                'garage' => $garage,
                'distributor' => $distributor,
                'supplier' => $supplier,
                'select_checked' => $selectChecked,
                'id_selected_check' => $idSelectedCheck,
                'weekend' => $weekend,
                'all_week' => $allWeek,
                'room_selected' => $this->Week->getWeekFromRoom($conference_delegate_id),
                'trade_show_selected' => $this->Week->getWeekFromTradeShow($conference_delegate_id),
                'dinner_selected' => $this->Week->getWeekFromDinner($conference_delegate_id),
                'array_distributor_name' => $arrayDistributorName,
                'array_garage_name' => $arrayGarageName,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete ConferenceDelegate.
     */
    public function ajax_delete($delegate_id)
    {
        $this->verify_ajax($this->request);

        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $this->autoRender = false;
            $this->Room->deleteRoomByConferenceDelegate($delegate_id);
            $this->TradeShow->deleteTradeShowByConferenceDelegate($delegate_id);
            $this->Dinner->deleteDinnerByConferenceDelegate($delegate_id);
            return $this->ConferenceDelegate->delete($delegate_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get ConferenceDelegate select for garage, distributor or supplier.
     */
    public function ajax_select_delegate()
    {
        $this->verify_ajax($this->request);

        $this->autoRender = false;
        if ($this->request->data['type'] == 'search_garage') {
            $arrayGarages = $this->GarageContactStaff->findAllByGarageId($this->request->data['element_id']);
            foreach ($arrayGarages as $arrayGarage) {
                $contact = $this->Contact->find('all', ['conditions' => ['Contact.id' => $arrayGarage['GarageContactStaff']['contact_id']]]);
                $arrayGarageDelegates[] = $contact[0];
            }
            return json_encode($arrayGarageDelegates);
        } elseif ($this->request->data['type'] == 'search_distributor') {
            $arrayDistributors = $this->DistributorContactStaff->findAllByDistributorId($this->request->data['element_id']);
            foreach ($arrayDistributors as $arrayDistributor) {
                $contact = $this->Contact->find('all', ['conditions' => ['Contact.id' => $arrayDistributor['DistributorContactStaff']['contact_id']]]);
                $arrayDistributorDelegates[] = $contact[0];
            }
            return json_encode($arrayDistributorDelegates);
        } elseif ($this->request->data['type'] == 'search_supplier') {
            return json_encode($this->Contact->findListByGarageId($this->request->data['element_id']));
        }
    }

    /**
     * Conferences delegates excel generation.
     * Not AJAX call.
     */
    public function ajax_delegates_excel()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $searcher = $this->request->query;
            $this->request->data['Buscador'] = $searcher;

            $conferencesDelegates = $this->ConferenceDelegate->find(
                'all',
                array(
                    'conditions' => array(
                        $this->ConferenceDelegate->conditions($searcher),
                        'Venue.aag_region_id' => $aagRegionId,
                    ),
                    'fields' => array(
                        'all',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'Conference',
                            'table' => 'conferences',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Conference.id = ConferenceDelegate.conferences_id',
                            ),
                        ),
                        array(
                            'alias' => 'Venue',
                            'table' => 'venues',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Venue.id = Conference.venue_id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = ConferenceDelegate.distributors_id',
                            ),
                        ),
                        array(
                            'alias' => 'Supplier',
                            'table' => 'suppliers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Supplier.id = ConferenceDelegate.suppliers_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = ConferenceDelegate.garages_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'ConferenceDelegate.*',
                        'Conference.name',
                        'Venue.name',
                        'Distributor.name',
                        'Supplier.name',
                        'Garage.name'
                    ),
                    'order' => 'ConferenceDelegate.contact'
                )
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'conferences_delegates' => $conferencesDelegates,
                'config' => $config,
            ));

            $this->render('/ConferencesDelegates/Elements/export_excel_delegates');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get conferences delegares count.
     */
    public function ajax_count_conferences_delegates()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)
        ) {
            $this->layout = $this->autoRender = false;
            if (!$this->request->is('get')) {
                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = $this->ConferenceDelegate->conditions($searcher);

                $queryType = ConstantsQueryTypes::COUNT;

                $countConferencesDelegates = $this->ConferenceDelegate->dynamicTypeConferencesDelegatesExportQuery($queryType, $aagRegionId, $conditions);
                $countConferencesDelegates = $countConferencesDelegates ? $countConferencesDelegates : 0;
                return json_encode($countConferencesDelegates);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX CSV conferences delegates generation.
     */
    public function ajax_get_csv_data()
    {
        $this->verify_ajax($this->request);
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)
        ) {
            $this->layout = $this->autoRender = false;
            $email = $this->request->data['contact-email'];

            if (!$this->request->is('get')) {
                // Here we stop the ajax conexion
                // We need to pass $user as a parameter
                header("Content-Length: 0");
                header('Connection: close');
                flush();
                session_write_close();
                if (is_callable('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                }

                // wait 10s
                sleep(10);

                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = $this->ConferenceDelegate->conditions($searcher);

                $queryType = ConstantsQueryTypes::ALL;
                $conferencesDelegates = $this->ConferenceDelegate->dynamicTypeConferencesDelegatesExportQuery($queryType, $aagRegionId, $conditions);

                $this->generateCsv($conferencesDelegates, $email, $user);
            }
            exit;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Generate CSV.
     */
    private function generateCsv($data, $email, $user)
    {
        $languageCode = $user['language_code'];

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Conferences_Delegates' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;

        $controller = $this->params['controller'];

        $file = fopen($fileFullName, 'a');

        $table = array(
            __t('Delegate.Conference_name', $languageCode),
            __t('Delegate.Delegate_name', $languageCode),
            __t('Delegate.Venue_name', $languageCode),
            __t('Delegate.Distributor_name', $languageCode),
            __t('Delegate.Supplier_name', $languageCode),
            __t('Delegate.Garage_name', $languageCode),
        );

        fputcsv($file, $table);
        foreach ($data as $conference) {
            $conferenceRow = array();
            $conferenceRow[] = $conference['Conference']['name'];
            $conferenceRow[] = $conference['ConferenceDelegate']['contact'];
            $conferenceRow[] = $conference['Venue']['name'];
            $conferenceRow[] = $conference['Distributor']['name'];
            $conferenceRow[] = $conference['Supplier']['name'];
            $conferenceRow[] = $conference['Garage']['name'];
            fputcsv($file, $conferenceRow);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }
}
