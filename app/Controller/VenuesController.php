<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class VenuesController extends AppController
{
    public $uses = array(
        'Venue',
        'Conference',
        'User',
        'AagRegion',
        'VenueType',
        'SalesArea'
    );

    /**
     * Venues home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $searcher = $this->request->query;
            if (!isset($searcher['aag_region_id'])) {
                $searcher['aag_region_id'] = $aagRegionId;
            }
            $this->request->data['Search'] = $searcher;

            $venues = $this->custom_pagination(
                $this->Venue->_query('home'),
                $this->Venue->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Venue',
                null,
                'PaginatorOrderCustom'

            );
            $this->setVarForm();
            $this->set(array(
                'venues' => $venues,
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create Venue.
     */
    public function add()
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)) {
            $this->request->data['Venue']['active'] = ConstantsBooleans::YES;
            if (!$this->request->is('get')) {
                if ($this->Venue->add($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'venues',
                            'action' => 'edit',
                            $this->Venue->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $this->setVarForm();
            $this->set(array(
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Venue.
     */
    public function edit($venue_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $venue = $this->Venue->findByIdAndAagRegionId($venue_id, $aagRegionId);
        if (
            $venue &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)
        ) {

            if ($this->request->is('get')) {
                $this->request->data = $venue;
            } else {
                $this->request->data['User']['id'] = $venue_id;
                if ($this->Venue->edit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $this->setVarForm();
            $this->set(array(
                'venue' => $venue,
                'venue_id' => $venue_id,
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete Venue.
     */
    public function ajax_delete($venue_id)
    {
        $this->verify_ajax($this->request);

        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $venue = $this->Venue->findByIdAndAagRegionId($venue_id, $aagRegionId);
        if (
            $venue &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)
        ) {
            $this->autoRender = false;
            if ($this->Conference->findAllByVenueId($venue_id)) {
                $result = $this->deactivateActive($venue_id);
                return $result;
            } else {
                return $this->Venue->delete($venue_id);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function deactivateActive($venue_id)
    {
        $venue = $this->Venue->findById($venue_id);
        $this->Venue->deactivateActiveByVenue($venue);
        return 2;
    }

    private function setVarForm()
    {
        $active = array(
            ConstantsBooleans::NO => __t('General.No_active'),
            ConstantsBooleans::YES => __t('General.Active')
        );

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];
        $aagRegions = $this->AagRegion->region_list();
        $conditionsUser = array('AagRegion.id' => $aagRegionId);

        $aagRegionsUser = $this->AagRegion->region_list_conditions($conditionsUser);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'venues',
                'action' => 'home',
            ),
        );
        $this->set(array(
            'cancel_action' => $cancelAction,
            'active' => $active,
            'aag_regions' => $aagRegions,
            'user_aag_region_id' => $aagRegionId,
            'user_role_id' => $roleId,
            'aag_regions_user' => $aagRegionsUser,
            'venues_types' => $this->VenueType->search_list(),
            'user_aag_region_id' => CakeSession::read('Auth.User.aag_region_id'),
        ));
    }

    /**
     * Venue Excel generation.
     */
    public function venue_excel()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = array();
            if (isset($aagRegionId)) {
                $conditions['Venue.aag_region_id'] = $aagRegionId;
            }

            $venues = $this->Venue->find(
                'all',
                array(
                    'conditions' => array(
                        $this->Venue->conditions($searcher),
                        $conditions
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'VenueType',
                            'table' => 'venues_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'VenueType.id = Venue.venue_type_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'Venue.*',
                        'VenueType.*',
                    ),
                    'order' => 'Venue.name ASC',
                )
            );

            $this->set(array(
                'venues' => $venues,
                'sales_area' => $salesArea
            ));
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Venues/Elements/export_excel_venues');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get Venues name. Used for dynamic selects.
     */
    public function get_venues_name()
    {
        $this->verify_ajax($this->request);
        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $venues_name = $this->Venue->getPossiblesVenuesAjax($this->request->query, $aagRegionId);

        $list_venues = array();
        foreach ($venues_name as $key => $venue) {
            $list_venues[] = array(
                'id' => $key,
                'text' => $venue,
            );
        }

        $list_venues_complete['items'] = $list_venues;

        return json_encode($list_venues_complete);
    }

    /**
     * AJAX get Venues count.
     */
    public function ajax_count_venues()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)
        ) {
            $this->layout = $this->autoRender = false;
            if (!$this->request->is('get')) {
                $user = $this->Acceso->user();
                $aagRegionId = $user['aag_region_id'];

                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;
                $conditions = $this->Venue->conditions($searcher);

                $queryType = ConstantsQueryTypes::COUNT;
                $countVenues = $this->Venue->dynamicTypeVenuesExportQuery($queryType, $aagRegionId, $conditions);
                $countVenues = $countVenues ? $countVenues : 0;
            }
            return json_encode($countVenues);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX generate Venues CSV.
     */
    public function ajax_get_csv_data()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES)
        ) {
            $this->layout = $this->autoRender = false;
            $email = $this->request->data['contact-email'];
            if (!$this->request->is('get')) {
                // Here we stop the ajax conexion
                // We need to pass $user as a parameter
                $user = $this->Acceso->user();

                header("Content-Length: 0");
                header('Connection: close');
                flush();
                session_write_close();
                if (is_callable('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                }

                // wait 10s
                sleep(10);

                $aagRegionId = $user['aag_region_id'];

                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = $this->Venue->conditions($searcher);
                $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());

                $queryType = ConstantsQueryTypes::ALL;
                $venues = $this->Venue->dynamicTypeVenuesExportQuery($queryType, $aagRegionId, $conditions);
                $this->generateCsv($venues, $email, $user);
            }
            exit;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function generateCsv($data, $email, $user)
    {
        $languageCode = $user['language_code'];

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Venues' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;

        $controller = $this->params['controller'];

        $file = fopen($fileFullName, 'a');

        $table = array(
            __t('Venue.Name', $languageCode),
            __t('Venue.Address_1', $languageCode),
            __t('Venue.Address_2', $languageCode),
            __t('Venue.Town', $languageCode),
            __t('Venue.Post_code', $languageCode),
            __t('Venue.Telephone', $languageCode),
            __t('Venue.Venue_type', $languageCode),
            __t('General.Active', $languageCode),
        );

        fputcsv($file, $table);
        foreach ($data as $venue) {
            $venue_row = array();
            $venue_row[] = $venue['Venue']['name'];
            $venue_row[] = $venue['Venue']['address_1'];
            $venue_row[] = $venue['Venue']['address_2'];
            $venue_row[] = $venue['Venue']['town'];
            $venue_row[] = $venue['Venue']['post_code'];
            $venue_row[] = $venue['Venue']['telephone'];
            $venue_row[] = $venue['VenueType']['name' . __s()];
            $venue_row[] = $venue['Venue']['active'];
            fputcsv($file, $venue_row);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }
}
