<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class TrainingsCreditsNetworksController extends AppController
{
    public $uses = array(
        'TrainingCreditNetwork',
        'GarageNetwork',
        'TrainingProvider',
        'Garage',
        'Network',
        'TrainingCredit',
        'Acceso',
        'Contact',
        'PositionConfig',
        'PositionConfigNetwork',
        'PaginatorHelper',
        'ReasonAllowance',
        'TrainingAllowance',
    );

    /**
     * Training credits network home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $contactId = $user['contact_id'];

            $networks = $this->Network->getNetworksTraining($aagRegionId);

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            if (isset($searcher['is_actual']) && $searcher['is_actual'] == ConstantsBooleans::ACTIVE) {
                $is_actual_checked = true;
            } elseif (isset($searcher['is_actual']) && $searcher['is_actual'] == ConstantsBooleans::NO_ACTIVE) {
                $is_actual_checked = false;
            } else {
                $searcher['is_actual'] = 1;
                $is_actual_checked = true;
            }

            $garagesNetworks = $this->custom_pagination(
                $this->Garage->_query('home_training'),
                $this->Garage->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Garage',
                null,
                'PaginatorOrderCustom'
            );

            $networkSelectedData = null;
            if (isset($searcher['Network_name']) && !empty($searcher['Network_name'])) {
                $networkSelectedData = $this->Network->getNetworkNameCreditAndDateByIdNetwork($this->request->data['Search']['Network_name']);
            }

            $arrayGarageName = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['Garage_name']);
            }

            $arrayAllowances = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayAllowances = $this->TrainingAllowance->getListAllowanceFromGarageId($searcher['Garage_name']);
            } else {
                $arrayAllowances = $this->TrainingAllowance->searchList();
            }

            $sumsGivenList = $this->TrainingAllowance->getAllGivenSumsList();
            $sumsSpentList = $this->TrainingAllowance->getAllSpentSumsList();

            $this->set(array(
                'garages_networks' => $garagesNetworks,
                'network_list' => $networks,
                'price_credits' => ($this->TrainingCredit->checkBD() != null) ? $this->TrainingCredit->checkBD() : null,
                'user' => $user['Role']['id'],
                'network_selected_data' => $networkSelectedData,
                'contact_id' => $contactId,
                'array_garage_name' => $arrayGarageName,
                'trainings_allowances' => $arrayAllowances,
                'is_actual_checked' => $is_actual_checked,
                'sums_given_list' => $sumsGivenList,
                'sums_spent_list' => $sumsSpentList,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TrainingNetworkCredit.
     */
    public function add_trainings_credits_networks($garage_network_id, $contact_id, $allowance_id = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $garage_network = $this->GarageNetwork->findById($garage_network_id);
        $garage = $this->Garage->findByIdAndAagRegionId($garage_network['GarageNetwork']['garage_id'], $aagRegionId);
        $contact = $this->Contact->findByIdAndAagRegionId($contact_id, $aagRegionId);
        if (
            $garage &&
            $contact &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $reasons_allowances = $this->ReasonAllowance->getListOfData();
            if (empty($this->request->data['TrainingNetworkCredit']['credit_given'])) {
                $this->request->data['TrainingNetworkCredit']['credit_given'] = 0;
            }
            if ($this->request->is('get')) {
                $this->request->data = $garage_network;
            } else {
                if ($this->request->data['TrainingNetworkCredit']['credit_given'] < 0) {
                    $this->request->data['TrainingNetworkCredit']['credit_given'] = -ceil(abs($this->request->data['TrainingNetworkCredit']['credit_given']) / 50) * 50;
                } else {
                    $this->request->data['TrainingNetworkCredit']['credit_given'] = ceil($this->request->data['TrainingNetworkCredit']['credit_given'] / 50) * 50;
                }
                if ($allowance_id) {
                    $this->request->data['TrainingNetworkCredit']['training_allowance_id'] = $allowance_id;
                } else {
                    $training_allowance = $this->TrainingAllowance->getIdByGarageNetworkId($this->request->data['TrainingNetworkCredit']['garage_network_id']);
                    $this->request->data['TrainingNetworkCredit']['training_allowance_id'] = $training_allowance['TrainingAllowance']['id'];
                }
                if ($this->TrainingCreditNetwork->addTrainingsCreditsNetworks($this->request->data)) {
                    $garage_network = $this->GarageNetwork->findById($this->request->data['TrainingNetworkCredit']['garage_network_id']);
                    $this->GarageNetwork->editCreditGarageNetworkByID($this->request->data, $garage_network);
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'garage_network_id' => $garage_network_id,
                'contact_id' => $contact_id,
                'garage' => $garage,
                'reasons_allowances' => $reasons_allowances,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Network credit.
     */
    public function edit_network_credit($network_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $network = $this->Network->findByIdAndAagRegionId($network_id, $aagRegionId);
        if (
            $network &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {

            if (!$network) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_NETWORK));
                $this->redirect(
                    array(
                        'controller' => 'trainings_credits_networks',
                        'action' => 'home',
                    )
                );
            }

            if ($this->request->is('get')) {
                $this->request->data = $network;
            } else {
                $this->request->data['User']['id'] = $network_id;
                if ($this->Network->edit_network_credit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'network_id' => $network_id,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Network date restarting credit.
     */
    public function edit_network_date_restarting_credit($network_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $network = $this->Network->findByIdAndAagRegionId($network_id, $aagRegionId);
        if (
            $network &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {

            if (!$network) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_NETWORK));
                $this->redirect(
                    array(
                        'controller' => 'trainings_credits_networks',
                        'action' => 'home',
                    )
                );
            }

            if ($this->request->is('get')) {
                $network['Network']['date_restarting_credit'] = Fecha::toFormatoVistaFecha($network['Network']['date_restarting_credit']);
                $this->request->data = $network;
            } else {
                $this->request->data['User']['id'] = $network_id;
                if ($this->Network->edit_network_restarting_date($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'network_id' => $network_id,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $active = array(
            ConstantsBooleans::NO => __t('General.No_active'),
            ConstantsBooleans::YES => __t('General.Active')
        );

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'trainings_trainers',
                'action' => 'home',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'active' => $active,
            'trainings_providers' => $this->TrainingProvider->searchList(),
        ));
    }

    /**
     * TrainingNetworkCredits Excel generation.
     */
    public function training_credits_network_excel()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $searcher = $this->request->query;

            $garagesCredits = $this->Garage->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.garage_id = Garage.id',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingAllowance',
                            'table' => 'trainings_allowances',
                            'type' => 'INNER',
                            'conditions' => array(
                                'TrainingAllowance.garage_network_id = GarageNetwork.id',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                                'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT),
                            ),
                        ),
                        array(
                            'alias' => 'TrainingCreditNetwork',
                            'table' => 'trainings_credits_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingCreditNetwork.garage_network_id = GarageNetwork.id',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingPlannedCourse',
                            'table' => 'trainings_planned_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingPlannedCourse.id = TrainingCreditNetwork.training_planned_course_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'TrainingCreditNetwork.id',
                        'TrainingAllowance.id',
                        'TrainingAllowance.start_date',
                        'TrainingAllowance.end_date',
                        'TrainingAllowance.is_actual',
                        'GarageNetwork.id',
                        'GarageNetwork.default_credit',
                        'GarageNetwork.credit',
                        'GarageNetwork.garage_id',
                        'Garage.id',
                        'Garage.name',
                        'Garage.business_name',
                        'Network.credit',
                    ),
                    'order' => 'Garage.name asc, TrainingAllowance.start_date desc',
                    'group' => 'TrainingAllowance.id',
                    'conditions' => array(
                        $this->Garage->conditions($searcher)
                    )
                )
            );

            $sumsGivenList = $this->TrainingAllowance->getAllGivenSumsList();
            $sumsSpentList = $this->TrainingAllowance->getAllSpentSumsList();

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'garages_credits' => $garagesCredits,
                'config' => $config,
                'sums_given_list' => $sumsGivenList,
                'sums_spent_list' => $sumsSpentList,
            ));

            $this->render('/TrainingsCreditsNetworks/Elements/export_excel_credits');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}
