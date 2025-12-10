<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class ConferencesController extends AppController
{
    public $uses = array(
        'Conference',
        'Venue',
        'ConferenceDelegate',
    );

    /**
     * Conferences home page.
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
            $this->request->data['Search'] = $searcher;

            if (!empty($searcher['venue'])) {
                $searcher['venue'] = $this->Venue->getVenueNameById($searcher['venue']);
            }

            $conferences = $this->custom_pagination(
                $this->Conference->_query($aagRegionId),
                $this->Conference->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Conference',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'conferences' => $conferences,
                'venues' => $this->Venue->search_list_conditions($aagRegionId),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create Conference.
     */
    public function add()
    {
        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $this->request->data['Conference']['status'] = ConstantsBooleans::YES;
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];
            $venues = $this->Venue->search_list_conditions_conferences_form($aagRegionId);

            $status = array(
                ConstantsBooleans::NO => __t('General.No_active'),
                ConstantsBooleans::YES => __t('General.Active')
            );

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'conferences',
                    'action' => 'home',
                ),
            );

            if (!$this->request->is('get')) {
                $conference = $this->Conference->add($this->request->data);
                if ($conference) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'conferences',
                            'action' => 'edit',
                            $this->Conference->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'venues' => $venues,
                'status' => $status
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Conference.
     */
    public function edit($conference_id)
    {
        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $conference = $this->Conference->findByIdAndAagRegionId($conference_id, $aagRegionId);
            if (!$conference) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_CONFERENCE));
                $this->redirect(
                    array(
                        'controller' => 'conferences',
                        'action' => 'home',
                    )
                );
            }

            $venues = $this->Venue->search_list_conditions_conferences_form($aagRegionId);

            $status = array(
                ConstantsBooleans::NO => __t('General.No_active'),
                ConstantsBooleans::YES => __t('General.Active')
            );

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'conferences',
                    'action' => 'home',
                ),
            );

            if ($this->request->is('get')) {
                $conference['Conference']['start_date'] = Fecha::toFormatoVistaFecha($conference['Conference']['start_date']);
                $this->request->data = $conference;
            } else {
                if ($this->Conference->edit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'conference' => $conference,
                'venues' => $venues,
                'cancel_action' => $cancelAction,
                'status' => $status
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change Conference status.
     */
    public function ajax_toggle_status($conference_id)
    {
        $this->verify_ajax($this->request);

        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $this->autoRender = false;
            return $this->Conference->toggle_status($conference_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete Conference.
     */
    public function ajax_delete($conference_id)
    {
        $this->verify_ajax($this->request);

        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)) {
            $this->autoRender = false;
            if ($this->ConferenceDelegate->findAllByConferencesId($conference_id)) {
                return 2;
            } else {
                return $this->Conference->delete($conference_id);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Conferences excel generation.
     * Not AJAX call.
     */
    public function ajax_conferences_excel()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES)
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conferences = $this->Conference->find(
                'all',
                array(
                    'conditions' => array(
                        $this->Conference->conditions($searcher),
                        'Conference.aag_region_id' => $aagRegionId
                    ),
                    'fields' => array(
                        'all',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'Venue',
                            'table' => 'venues',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Venue.id = Conference.venue_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'Conference.*',
                        'Venue.name',
                    ),
                )
            );
            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'conferences' => $conferences,
                'config' => $config,
            ));

            $this->render('/Conferences/Elements/export_excel_conferences');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get conferences count.
     */
    public function ajax_count_conferences()
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

                $conditions = $this->Conference->conditions($searcher);

                $queryType = ConstantsQueryTypes::COUNT;

                $countConferences = $this->Conference->dynamicTypeConferencesExportQuery($queryType, $aagRegionId, $conditions);
                $countConferences = $countConferences ? $countConferences : 0;
                return json_encode($countConferences);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX CSV conferences generation.
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

                $conditions = $this->Conference->conditions($searcher);

                $queryType = ConstantsQueryTypes::ALL;
                $conferences = $this->Conference->dynamicTypeConferencesExportQuery($queryType, $aagRegionId, $conditions);
                $this->generateCsv($conferences, $email, $user);
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

        $filename = 'Conferences' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;
        $controller = $this->params['controller'];

        $file = fopen($fileFullName, 'a');

        $table = array(
            __t('Conference.Name', $languageCode),
            __t('Conference.Start_date', $languageCode),
            __t('Conference.Duration', $languageCode),
            __t('Conference.Venue', $languageCode),
            __t('Conference.Status', $languageCode),
        );

        fputcsv($file, $table);
        foreach ($data as $conference) {
            $conference_row = array();
            $conference_row[] = $conference['Conference']['name'];
            $conference_row[] = $conference['Conference']['start_date'];
            $conference_row[] = $conference['Conference']['duration'];
            $conference_row[] = $conference['Venue']['name'];
            $conference_row[] = ($conference['Conference']['status']) ? __t('General.Active') : __t('General.No_active');
            fputcsv($file, $conference_row);
        }

        fclose($file);
        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }
}
