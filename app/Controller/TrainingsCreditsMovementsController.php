<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class TrainingsCreditsMovementsController extends AppController
{
    public $uses = array(
        'TrainingCreditMovement',
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
        'ReasonAllowance',
        'TrainingAllowance',
        'TrainingPlannedCourse',
        'TrainingDelegate'
    );

    /**
     * TrainingCreditMovements home page.
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

            $cancelled = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

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

            $garagesNetwork = $this->custom_pagination(
                $this->TrainingCreditMovement->queryMovement('home'),
                $this->TrainingCreditMovement->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingCreditMovement',
                null,
                'PaginatorOrderCustom'
            );

            $arrayGarageName = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['Garage_name']);
            }

            $arrayDelegateName = array();
            if (!empty($searcher['Delegate_name'])) {
                $arrayDelegateName = $this->Contact->getContactsNameByIdContact($searcher['Delegate_name']);
            }

            $arrayAllowances = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayAllowances = $this->TrainingAllowance->getListAllowanceFromGarageId($searcher['Garage_name']);
            } else {
                $arrayAllowances = $this->TrainingAllowance->searchList();
            }

            $this->set(array(
                'garages_networks' => $garagesNetwork,
                'network_list' => $networks,
                'price_credits' => ($this->TrainingCredit->checkBD() != null) ? $this->TrainingCredit->checkBD() : null,
                'user' => $user['Role']['id'],
                'contact_id' => $contactId,
                'reasons_allowance_list' => $this->ReasonAllowance->search_list(),
                'cancelled' => $cancelled,
                'array_garage_name' => $arrayGarageName,
                'array_delegate_name' => $arrayDelegateName,
                'trainings_allowances' => $arrayAllowances,
                'is_actual_checked' => $is_actual_checked,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * TrainingCreditMovements Excel generation.
     */
    public function training_credits_movements_excel()
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
            $searcher = $this->request->query;

            $creditsMovements = $this->TrainingCreditMovement->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'GarageNetwork.id = TrainingCreditMovement.garage_network_id',
                            ),
                            'fields' => array(
                                'GarageNetwork.id',
                                'GarageNetwork.credit',
                                'GarageNetwork.garage_id',
                                'GarageNetwork.network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                                'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                            ),
                            'fields' => array(
                                'Garage.id',
                                'Garage.name'
                            ),
                        ),
                        array(
                            'alias' => 'TrainingDelegate',
                            'table' => 'trainings_delegates',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingDelegate.id = TrainingCreditMovement.training_delegate_id',
                            ),
                            'fields' => array(
                                'TrainingDelegate.id',
                                'TrainingDelegate.garage_contact_staff_id',
                                'TrainingDelegate.cancelled',
                                'TrainingDelegate.order_number',
                            ),
                        ),
                        array(
                            'alias' => 'GarageContactStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactStaff.id = TrainingDelegate.garage_contact_staff_id',
                            ),
                            'fields' => array(
                                'GarageContactStaff.id',
                                'GarageContactStaff.contact_id',
                            ),
                        ),
                        array(
                            'alias' => 'ContactDelegate',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'ContactDelegate.id = GarageContactStaff.contact_id',
                                'ContactDelegate.aag_region_id' => ConstantsAAGRegionId::UK,
                            ),
                            'fields' => array(
                                'ContactDelegate.id',
                                'ContactDelegate.first_name',
                                'ContactDelegate.last_name',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingPlannedCourse',
                            'table' => 'trainings_planned_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingPlannedCourse.id = TrainingCreditMovement.training_planned_course_id',
                            ),
                            'fields' => array(
                                'TrainingPlannedCourse.id',
                                'TrainingPlannedCourse.date_from',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingAllowance',
                            'table' => 'trainings_allowances',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingAllowance.id = TrainingCreditMovement.training_allowance_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'TrainingCreditMovement.*',
                        'GarageNetwork.credit',
                        'GarageNetwork.network_id',
                        'Garage.id',
                        'Garage.name',
                        'ContactDelegate.id',
                        'ContactDelegate.first_name',
                        'ContactDelegate.last_name',
                        'TrainingPlannedCourse.date_from',
                        'TrainingDelegate.cancelled',
                        'TrainingDelegate.order_number',
                        'TrainingAllowance.is_actual'
                    ),
                    'order' => 'TrainingCreditMovement.creation_date DESC',
                    'group' => 'TrainingCreditMovement.id',
                    'conditions' => $this->TrainingCreditMovement->conditions($searcher)
                )
            );

            $networks = $this->Network->getNetworksTraining($aagRegionId);

            $sumsGivenList = $this->TrainingAllowance->getAllGivenSumsList();
            $sumsSpentList = $this->TrainingAllowance->getAllSpentSumsList();

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'credits_movements' => $creditsMovements,
                'config' => $config,
                'reasons_allowance_list' => $this->ReasonAllowance->search_list(),
                'sums_given_list' => $sumsGivenList,
                'sums_spent_list' => $sumsSpentList,
                'network_list' => $networks,
            ));

            $this->render('/TrainingsCreditsMovements/Elements/export_excel_credits_movements');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get TrainingAllowance options by garage ID.
     */
    public function ajax_options_allowance()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->autoRender = false;
            $arrayAllowances = $this->TrainingAllowance->getAllAllowanceFromGarageId($this->request->data['garage_id']);
            return json_encode($arrayAllowances);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get TrainingAllowance options.
     */
    public function ajax_options_allowance_all()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->autoRender = false;
            $arrayAllowances = $this->TrainingAllowance->getData();
            return json_encode($arrayAllowances);
        } else {
            throw new UnauthorizedException();
        }
    }
}
