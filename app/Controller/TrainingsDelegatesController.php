<?php

class TrainingsDelegatesController extends AppController
{
    public $uses = array(
        'TrainingDelegate',
        'TrainingTrainer',
        'TrainingCourse',
        'Garage',
        'Contact',
        'Position',
        'TrainingPlannedCourse',
        'GarageContactStaff',
        'TrainingCreditNetwork',
        'GarageNetwork',
        'ReasonDelegate',
        'ReasonDelegateCancelled',
        'Network',
        'TrainingProvider',
        'TrainingAllowance',
    );

    /**
     * TrainingDelegates home page.
     */
    public function home($training_planned_course_id)
    {
        $course = $this->TrainingPlannedCourse->findById($training_planned_course_id);
        if (
            $course &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $networks = $this->Network->getNetworksTraining($aagRegionId);
            $cancelled = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $trainingCourseId = $this->TrainingPlannedCourse->findTrainingCourseId($training_planned_course_id);

            if (!$course) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_COURSE));
                $this->redirect(
                    array(
                        'controller' => 'trainings_courses',
                        'action' => 'home',
                    )
                );
            }

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $trainingsDelegates = $this->custom_pagination(
                $this->TrainingDelegate->_query('home', $training_planned_course_id),
                $this->TrainingDelegate->conditions($searcher)
            );

            $availabilityStatus = true;
            if ($course['TrainingPlannedCourse']['availability'] == $this->TrainingDelegate->getNumberOfDelegatesAvailable($training_planned_course_id)) {
                $availabilityStatus = false;
            }

            $arrayGarageName = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['Garage_name']);
            }
            $arrayDelegateName = array();
            if (!empty($searcher['Delegate_name'])) {
                $arrayDelegateName = $this->Contact->getContactsNameByIdContact($searcher['Delegate_name']);
            }

            $this->set(array(
                'trainings_delegates' => $trainingsDelegates,
                'training_course_id' => $trainingCourseId['TrainingPlannedCourse']['training_course_id'],
                'course_list' => $this->TrainingCourse->searchList(),
                'employment_list' => $this->Position->search_list(),
                'training_planned_course_id' => $training_planned_course_id,
                'availability_status' => $availabilityStatus,
                'course' => $course,
                'cancelled' => $cancelled,
                'reasons_cancelled_delegates' => json_encode($this->ReasonDelegateCancelled->getListOfData()),
                'network_list' => $networks,
                'array_garage_name' => $arrayGarageName,
                'array_delegate_name' => $arrayDelegateName,
                'reason_cancelled_list' => $this->ReasonDelegateCancelled->search_list(),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TrainingDelegate
     */
    public function add($training_planned_course_id)
    {
        $plannedCourse = $this->TrainingPlannedCourse->getByPlannedCourseId($training_planned_course_id);
        if (
            $plannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $user = $this->Acceso->user();
            $contact_id = $user['contact_id'];
            $course = $this->TrainingCourse->getByCourseId($plannedCourse['TrainingPlannedCourse']['training_course_id']);
            if (
                !$this->request->is('get') &&
                isset($this->request->data['TrainingDelegate']['garage_id']) &&
                isset($this->request->data['TrainingDelegate']['delegate_id']) &&
                isset($this->request->data['TrainingDelegate']['network_id'])
            ) {
                $staff_id = $this->GarageContactStaff->getStaffData($this->request->data['TrainingDelegate']['garage_id'], $this->request->data['TrainingDelegate']['delegate_id']);
                $this->request->data['GarageNetwork'] = $this->GarageNetwork->findByGarageIdAndNetworkId($this->request->data['TrainingDelegate']['garage_id'], $this->request->data['TrainingDelegate']['network_id']);
                $this->request->data['TrainingPlannedCourse'] = $this->TrainingPlannedCourse->getByPlannedCourseId($this->request->data['training_planned_course_id']);
                $this->request->data['TrainingCourse'] = $this->TrainingCourse->getByCourseId($this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['training_course_id']);
                $this->request->data['Contact'] = $contact_id;
                if (!empty($staff_id)) {
                    if ($this->TrainingDelegate->add($this->request->data, $staff_id[0]['GarageContactStaff']['id'])) {
                        $this->request->data['training_delegate_id'] = $this->TrainingDelegate->getInsertID();

                        if (in_array($this->request->data['GarageNetwork']['GarageNetwork']['network_id'], array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE))) {
                            $trainingAllowances = $this->TrainingAllowance->getAllowanceFromGarageNetworkId($this->request->data['GarageNetwork']['GarageNetwork']['id']);
                            $isTimelined = 0;
                            $startDateAllowanceTmp = null;
                            $endDateAllowanceTmp = null;
                            $datePlannedCourseTmp = null;

                            foreach ($trainingAllowances as $trainingAllowance) {
                                $startDateAllowance = $trainingAllowance['TrainingAllowance']['start_date'];
                                $endDateAllowance = $trainingAllowance['TrainingAllowance']['end_date'];
                                $datePlannedCourse = $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from'];

                                if ($datePlannedCourse >= $startDateAllowance && $datePlannedCourse <= $endDateAllowance) {
                                    $isTimelined = 0;
                                    break;
                                } else {
                                    $isTimelined += 1;
                                    $startDateAllowanceTmp = $trainingAllowance['TrainingAllowance']['start_date'];
                                    $endDateAllowanceTmp = $trainingAllowance['TrainingAllowance']['end_date'];
                                    $datePlannedCourseTmp = $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from'];
                                }
                            }

                            if ($isTimelined != 0 && $startDateAllowanceTmp != null && $endDateAllowanceTmp != null && $datePlannedCourseTmp != null) {
                                $garageNetworkId = $this->request->data['GarageNetwork']['GarageNetwork']['id'];

                                $trainingAllowanceTmp = array();
                                $trainingAllowanceTmp['TrainingAllowance']['garage_network_id'] = $garageNetworkId;

                                // Setting start date
                                $day = date('d', strtotime($startDateAllowanceTmp));
                                $monthStart = date('m', strtotime($startDateAllowanceTmp));
                                $monthPlanned = date('m', strtotime($datePlannedCourseTmp));
                                $monthEnd = date('m', strtotime($endDateAllowanceTmp));

                                if (($monthStart > $monthPlanned && $monthEnd >= $monthPlanned) || ($monthPlanned < $monthStart && $monthPlanned < $monthEnd)) {
                                    $year = date('Y', strtotime($datePlannedCourseTmp)) - 1;
                                } else {
                                    $year = date('Y', strtotime($datePlannedCourseTmp));
                                }

                                $startDate = date('Y-m-d', strtotime($year . "-" . $monthStart . "-" . $day));
                                $trainingAllowanceTmp['TrainingAllowance']['start_date'] = $startDate;
                                $trainingAllowanceTmp['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($startDate . ' +1 year -1 day'));

                                $trainingAllowanceId = null;

                                // if allowance doesn't exists, it's created
                                $existingTrainingAllowance = $this->TrainingAllowance->findByGarageNetworkIdAndStartDate($garageNetworkId, $startDate);
                                if (!$existingTrainingAllowance) {
                                    $this->TrainingAllowance->add($trainingAllowanceTmp);
                                    $trainingAllowanceId = $this->TrainingAllowance->getInsertID();
                                } else {
                                    $trainingAllowanceId = $existingTrainingAllowance['TrainingAllowance']['id'];
                                }

                                if ($trainingAllowanceId) {
                                    $this->TrainingAllowance->fillMissingAllowances($trainingAllowanceId);
                                }
                            }
                        }

                        $trainingAllowance = $this->TrainingAllowance->checkAllowanceToDelegate($this->request->data['GarageNetwork']['GarageNetwork']['id'], $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from']);
                        if ($trainingAllowance) {
                            $this->request->data['TrainingNetworkCredit']['training_allowance_id'] = $trainingAllowance['TrainingAllowance']['id'];
                        }

                        if (!$this->TrainingCreditNetwork->addTrainingsCreditsNetworksAccept($this->request->data)) {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                        if ($this->request->data['add_credits_input'] == 'accept') {
                            if (!$this->GarageNetwork->editCreditGarageNetworkByIDSpent($this->request->data)) {
                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                            }
                        }
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'trainings_delegates',
                                'action' => 'edit',
                                $training_planned_course_id,
                                $this->TrainingDelegate->getLastInsertID()
                            )
                        );
                    } else {
                        $validationErrors = $this->TrainingDelegate->validationErrors;
                        if (isset($validationErrors['garage_contact_staff_id'])) {
                            $this->Session->setFlashError(__t('Validation.Garage_contact_staff_id_not_unique'));
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    }
                }
            }
            $this->setVarForm();
            $this->set(array(
                'position_list' => $this->Position->search_list(),
                'training_planned_course_id' => $training_planned_course_id,
                'reasons_delegates' => json_encode($this->ReasonDelegate->getListOfData()),
                'garages_subtractible' => json_encode($this->GarageNetwork->getCompleteListOnContactStaffSubtractible()),
                'course' => $course,
                'planned_course' => $plannedCourse,
                'provider' => $this->TrainingProvider->getByProviderId($course['TrainingCourse']['training_provider_id']),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit TrainingDelegate.
     */
    public function edit($training_planned_course_id, $training_delegate_id)
    {
        $plannedCourse = $this->TrainingPlannedCourse->getByPlannedCourseId($training_planned_course_id);
        $delegate = $this->TrainingDelegate->findById($training_delegate_id);
        if (
            $plannedCourse &&
            $delegate &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $user = $this->Acceso->user();
            $contact_id_user = $user['contact_id'];

            if (!$delegate) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_DELEGATE));
                $this->redirect(
                    array(
                        'controller' => 'trainings_planned_courses',
                        'action' => 'home',
                    )
                );
            }

            $arrayGarageName = array();

            if ($this->request->is('get')) {
                $this->request->data = $delegate;
                $garage_id = $this->GarageContactStaff->getStaffGarageIdByStaffId($this->request->data['TrainingDelegate']['garage_contact_staff_id']);
                $contact_id = $garage_id[0]['GarageContactStaff']['contact_id'];
                $network_id = $delegate['TrainingDelegate']['network_id'];
                $this->request->data['TrainingDelegate']['garage_id'] = $garage_id[0]['GarageContactStaff']['garage_id'];
                $plannedCourse = $this->TrainingPlannedCourse->getByPlannedCourseId($training_planned_course_id);
                $course = $this->TrainingCourse->getByCourseId($plannedCourse['TrainingPlannedCourse']['training_course_id']);
                if (!empty($this->request->data['TrainingDelegate']['garage_id'])) {
                    $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($this->request->data['TrainingDelegate']['garage_id']);
                }
            } else {
                if (!empty($this->request->data['TrainingDelegate']['garage_id'])) {
                    $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($this->request->data['TrainingDelegate']['garage_id']);
                }
                $staff_id = $this->GarageContactStaff->getStaffData($this->request->data['TrainingDelegate']['garage_id'], $this->request->data['TrainingDelegate']['delegate_id']);
                $this->request->data['GarageNetwork'] = $this->GarageNetwork->findByGarageIdAndNetworkId($this->request->data['TrainingDelegate']['garage_id'], $this->request->data['TrainingDelegate']['network_id']);
                $this->request->data['TrainingPlannedCourse'] = $this->TrainingPlannedCourse->getByPlannedCourseId($this->request->data['training_planned_course_id']);
                $this->request->data['TrainingCourse'] = $this->TrainingCourse->getByCourseId($this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['training_course_id']);
                if ($this->TrainingDelegate->edit($this->request->data, $staff_id[0]['GarageContactStaff']['id'])) {
                    if ($delegate['TrainingDelegate']['is_refund_eligible'] != $this->request->data['TrainingDelegate']['is_refund_eligible']) {
                        $trainingCredit = array();

                        $garage_network_tmp = $this->GarageNetwork->findByGarageIdAndNetworkId($this->request->data['TrainingDelegate']['garage_id'], $this->request->data['TrainingDelegate']['network_id']);
                        $planned_course_tmp = $this->TrainingPlannedCourse->getByPlannedCourseId($this->request->data['training_planned_course_id']);
                        $course_tmp = $this->TrainingCourse->getByCourseId($planned_course_tmp['TrainingPlannedCourse']['training_course_id']);

                        $trainingCredit['TrainingNetworkCredit']['garage_network_id'] = $garage_network_tmp['GarageNetwork']['id'];
                        $trainingCredit['TrainingNetworkCredit']['contact_id'] = $contact_id_user;
                        $trainingCredit['TrainingNetworkCredit']['training_planned_course_id'] = $this->request->data['training_planned_course_id'];
                        $trainingCredit['TrainingDelegate']['id'] = $this->request->data['TrainingDelegate']['id'];

                        $trainingAllowance = $this->TrainingAllowance->checkAllowanceToDelegate($this->request->data['GarageNetwork']['GarageNetwork']['id'], $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from']);
                        if ($trainingAllowance) {
                            $trainingCredit['TrainingNetworkCredit']['training_allowance_id'] = $trainingAllowance['TrainingAllowance']['id'];
                        }

                        $network_tmp = $this->Network->findById($garage_network_tmp['GarageNetwork']['network_id']);

                        if (!empty($network_tmp['Network']['date_restarting_credit'])) {
                            if ($this->request->data['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::NO) {
                                $trainingCredit['TrainingNetworkCredit']['credit_given'] = $course_tmp['TrainingCourse']['price_credit'];
                            } else {
                                $trainingCredit['TrainingNetworkCredit']['credit_spent'] = $course_tmp['TrainingCourse']['price_credit'];
                                $trainingCredit['TrainingNetworkCredit']['description'] = $course_tmp['TrainingCourse']['name'];
                            }
                            if ($this->TrainingCreditNetwork->addTrainingsCreditsNetworksDelegate($trainingCredit)) {
                                $this->GarageNetwork->editCreditGarageByDelegate($garage_network_tmp, $course_tmp, $trainingCredit);
                            };
                        } else {
                            if ($this->request->data['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::NO) {
                                $trainingCredit['TrainingNetworkCredit']['credit_given'] = 0;
                            } else {
                                $trainingCredit['TrainingNetworkCredit']['credit_spent'] = 0;
                                $trainingCredit['TrainingNetworkCredit']['description'] = $course_tmp['TrainingCourse']['name'];
                            }
                            $this->TrainingCreditNetwork->addTrainingsCreditsNetworksDelegate($trainingCredit);
                        }
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $plannedCourse = $this->TrainingPlannedCourse->getByPlannedCourseId($training_planned_course_id);
                    $course = $this->TrainingCourse->getByCourseId($plannedCourse['TrainingPlannedCourse']['training_course_id']);
                    $validationErrors = $this->TrainingDelegate->validationErrors;
                    if (isset($validationErrors['garage_contact_staff_id'])) {
                        $this->Session->setFlashError(__t('Validation.Garage_contact_staff_id_not_unique'));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            }
            $networkSelected = $this->Network->findListById($network_id);

            $this->setVarForm();
            $this->set(array(
                'position_list' => $this->Position->search_list(),
                'training_planned_course_id' => $training_planned_course_id,
                'contact_id' => isset($contact_id) ? $contact_id : null,
                'network_id' => isset($network_id) ? $network_id : null,
                'network_selected' => isset($networkSelected) ? $networkSelected : null,
                'course' => isset($course) ? $course : null,
                'planned_course' => isset($plannedCourse) ? $plannedCourse : null,
                'provider' => isset($course) ? $this->TrainingProvider->getByProviderId($course['TrainingCourse']['training_provider_id']) : null,
                'reasons' => $this->ReasonDelegate->search_list(),
                'array_garage_name' => $arrayGarageName,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $isRefundable = array(
            ConstantsBooleans::NO => __t('General.No'),
            ConstantsBooleans::YES => __t('General.Yes')
        );
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'trainings_planned_courses',
                'action' => 'home',
            ),
        );
        $this->set(array(
            'cancel_action' => $cancelAction,
            'is_refundable' => $isRefundable,
        ));
    }

    /**
     * AJAX get GarageNetwork.
     */
    public function ajax_select_network()
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
            $array_garages = $this->GarageNetwork->getNetworkNameByIdGarageAndTraining($this->request->data['element_id'], $this->request->data['isEdit']);
            return json_encode($array_garages);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX select delegate.
     */
    public function ajax_select_delegate()
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

            $array_garages = $this->GarageContactStaff->findAllByGarageId($this->request->data['element_id']);

            foreach ($array_garages as $array_garage) {
                $contact = $this->Contact->find('all', ['conditions' => ['Contact.id' => $array_garage['GarageContactStaff']['contact_id']]]);
                $array_garage_delegates[] = $contact[0];
            }
            return json_encode($array_garage_delegates);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete TrainingDelegate.
     */
    public function ajax_delete($training_delegate_id)
    {
        $this->verify_ajax($this->request);
        $delegate = $this->TrainingDelegate->findById($training_delegate_id);

        if (
            $delegate &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;

            $trainingDelegate = [];
            $user = $this->Acceso->user();
            $contact_id = $user['contact_id'];

            $garage_id = $this->GarageContactStaff->getStaffGarageIdByStaffId($delegate['TrainingDelegate']['garage_contact_staff_id']);
            $trainingDelegate['GarageNetwork'] = $this->GarageNetwork->findByGarageIdAndNetworkId($garage_id[0]['GarageContactStaff']['garage_id'], $delegate['TrainingDelegate']['network_id']);
            $trainingDelegate['TrainingPlannedCourse'] = $this->TrainingPlannedCourse->getByPlannedCourseId($delegate['TrainingDelegate']['training_planned_course_id']);
            $trainingDelegate['TrainingCourse'] = $this->TrainingCourse->getByCourseId($trainingDelegate['TrainingPlannedCourse']['TrainingPlannedCourse']['training_course_id']);
            $trainingDelegate['Contact'] = $contact_id;
            $trainingDelegate['training_delegate_id'] = $training_delegate_id;

            $trainingAllowance = $this->TrainingAllowance->checkAllowanceToDelegate($trainingDelegate['GarageNetwork']['GarageNetwork']['id'], $trainingDelegate['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from']);
            if ($trainingAllowance) {
                $trainingDelegate['TrainingNetworkCredit']['training_allowance_id'] = $trainingAllowance['TrainingAllowance']['id'];
            }

            if ($this->request->data['Credit_points'] && $this->request->data['Credit_points'] == 'true') {
                if ($delegate['TrainingDelegate']['is_refund_eligible'] && $delegate['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::YES) {
                    $trainingDelegate['add_credits_input'] = 'accept';
                } else {
                    $trainingDelegate['add_credits_input'] = 'not_accepted';
                }
                if (!$this->TrainingCreditNetwork->addTrainingsCreditsNetworksDelete($trainingDelegate)) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
                if ($trainingDelegate['add_credits_input'] == 'accept') {
                    $this->GarageNetwork->editCreditGarageNetworkReturnCredit($trainingDelegate);
                }
                $this->TrainingDelegate->cancelDelegate($delegate, $this->request->data['reason_cancelled_id']);
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                echo '1';
            } else {
                $trainingDelegate['add_credits_input'] = 'not_accepted';
                if (!$this->TrainingCreditNetwork->addTrainingsCreditsNetworksDelete($trainingDelegate)) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
                $this->TrainingDelegate->cancelDelegate($delegate, $this->request->data['reason_cancelled_id']);
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                echo '1';
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete all TrainingDelegates.
     */
    public function ajax_delete_all($training_planned_course_id)
    {
        $this->verify_ajax($this->request);
        $plannedCourse = $this->TrainingPlannedCourse->findById($training_planned_course_id);

        if (
            $plannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            $user = $this->Acceso->user();
            $contact_id = $user['contact_id'];

            $trainingsDelegates = $this->TrainingDelegate->getDelegatesFromPlannedCourseId($training_planned_course_id);

            foreach ($trainingsDelegates as $trainingDelegate) {
                $garage_id = $this->GarageContactStaff->getStaffGarageIdByStaffId($trainingDelegate['TrainingDelegate']['garage_contact_staff_id']);
                $trainingDelegate['GarageNetwork'] = $this->GarageNetwork->findByGarageIdAndNetworkId($garage_id[0]['GarageContactStaff']['garage_id'], $trainingDelegate['TrainingDelegate']['network_id']);
                $trainingDelegate['Contact'] = $contact_id;
                $trainingDelegate['TrainingPlannedCourse'] = $this->TrainingPlannedCourse->getByPlannedCourseId($trainingDelegate['TrainingDelegate']['training_planned_course_id']);

                $trainingAllowance = $this->TrainingAllowance->checkAllowanceToDelegate($trainingDelegate['GarageNetwork']['GarageNetwork']['id'], $trainingDelegate['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from']);
                if ($trainingAllowance) {
                    $trainingDelegate['TrainingNetworkCredit']['training_allowance_id'] = $trainingAllowance['TrainingAllowance']['id'];
                }

                if ($this->request->data['Credit_points'] && $this->request->data['Credit_points'] == 'true') {
                    if ($trainingDelegate['TrainingDelegate']['is_refund_eligible'] && $trainingDelegate['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::YES) {
                        $trainingDelegate['add_credits_input'] = 'accept';
                    } else {
                        $trainingDelegate['add_credits_input'] = 'not_accepted';
                    }
                } else {
                    $trainingDelegate['add_credits_input'] = 'not_accepted';
                }
                if ($trainingDelegate['add_credits_input'] == 'accept') {
                    $this->GarageNetwork->editCreditGarageNetworkReturnCreditTwo($trainingDelegate);
                }
                if ($this->TrainingCreditNetwork->addTrainingsCreditsNetworksDeleteFromPlannedCourse($trainingDelegate)) {
                    $this->TrainingDelegate->cancelDelegateGiven($trainingDelegate);
                }
                if ($this->request->data['delete'] && $this->request->data['delete'] == 'true') {
                    $this->TrainingDelegate->delete($trainingDelegate['TrainingDelegate']['id']);
                }
            }
            echo '1';
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * TrainingDelegates Excel generation.
     */
    public function training_delegates_excel($training_planned_course_id)
    {
        $plannedCourse = $this->TrainingPlannedCourse->findById($training_planned_course_id);
        if (
            $plannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $delegates = $this->TrainingDelegate->find(
                'all',
                array(
                    'conditions' => array(
                        $this->TrainingDelegate->conditions($searcher),
                        'TrainingDelegate.training_planned_course_id' => $training_planned_course_id
                    ),
                    'fields' => array(
                        'all',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'TrainingPlannedCourse',
                            'table' => 'trainings_planned_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingPlannedCourse.id = TrainingDelegate.training_planned_course_id',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingCourse',
                            'table' => 'trainings_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingCourse.id = TrainingPlannedCourse.training_course_id',

                            ),
                        ),
                        array(
                            'alias' => 'GarageContactStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactStaff.id = TrainingDelegate.garage_contact_staff_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactStaff.garage_id',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = TrainingDelegate.network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = GarageContactStaff.contact_id',
                            ),
                        ),
                        array(
                            'alias' => 'Position',
                            'table' => 'positions',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Position.id = Contact.position_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'TrainingPlannedCourse.*',
                        'TrainingDelegate.*',
                        'TrainingCourse.*',
                        'GarageContactStaff.*',
                        'Garage.*',
                        'Contact.*',
                        'Position.*',
                        'Network.*'
                    ),
                    'order' => 'Contact.first_name',
                )
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'delegates' => $delegates,
                'config' => $config,
                'reason_cancelled_list' => $this->ReasonDelegateCancelled->search_list(),
            ));

            $this->render('/TrainingsDelegates/Elements/export_excel_delegates');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get GarageNetwork.
     */
    public function ajax_garages_network()
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
            if (!empty($this->request->data['garage_id']) && !empty($this->request->data['network_id'])) {
                $garage_network = $this->GarageNetwork->findByGarageIdAndNetworkId($this->request->data['garage_id'], $this->request->data['network_id']);
                return $garage_network['GarageNetwork']['id'];
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change status.
     */
    public function ajax_toggle_status($delegate_id, $network_id)
    {
        $this->verify_ajax($this->request);
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $delegate_data = $this->TrainingDelegate->findById($delegate_id);
        $network = $this->Network->findByIdAndAagRegionId($network_id, $aagRegionId);

        if (
            $delegate_data &&
            $network &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;

            $substract_credits = $this->request->data['substract_credits'];

            // has to be string the 'true'
            if ($substract_credits == 'true') {
                $garage_contact_staff_data = $this->GarageContactStaff->findById($delegate_data['TrainingDelegate']['garage_contact_staff_id']);
                $this->request->data['add_credits_input'] = 'accept';
                $this->request->data['training_planned_course_id'] = $delegate_data['TrainingDelegate']['training_planned_course_id'];
                $this->request->data['GarageNetwork'] = $this->GarageNetwork->findByGarageIdAndNetworkId($garage_contact_staff_data['GarageContactStaff']['garage_id'], $network_id);
                $this->request->data['TrainingPlannedCourse'] = $this->TrainingPlannedCourse->getByPlannedCourseId($delegate_data['TrainingDelegate']['training_planned_course_id']);
                $this->request->data['TrainingCourse'] = $this->TrainingCourse->getByCourseId($this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['training_course_id']);
                $this->request->data['Contact'] = $garage_contact_staff_data['GarageContactStaff']['contact_id'];

                if (!empty($garage_contact_staff_data)) {
                    $this->request->data['training_delegate_id'] = $delegate_id;

                    if (in_array($this->request->data['GarageNetwork']['GarageNetwork']['network_id'], array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT))) {
                        $trainingAllowances = $this->TrainingAllowance->getAllowanceFromGarageNetworkId($this->request->data['GarageNetwork']['GarageNetwork']['id']);
                        $isTimelined = 0;
                        $startDateAllowanceTmp = null;
                        $endDateAllowanceTmp = null;
                        $datePlannedCourseTmp = null;

                        foreach ($trainingAllowances as $trainingAllowance) {
                            $startDateAllowance = $trainingAllowance['TrainingAllowance']['start_date'];
                            $endDateAllowance = $trainingAllowance['TrainingAllowance']['end_date'];
                            $datePlannedCourse = $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from'];
                            if ($datePlannedCourse >= $startDateAllowance && $datePlannedCourse <= $endDateAllowance) {
                                $isTimelined = 0;
                                break;
                            } else {
                                $isTimelined += 1;
                                $startDateAllowanceTmp = $trainingAllowance['TrainingAllowance']['start_date'];
                                $endDateAllowanceTmp = $trainingAllowance['TrainingAllowance']['end_date'];
                                $datePlannedCourseTmp = $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from'];
                            }
                        }

                        if ($isTimelined != 0 && $startDateAllowanceTmp != null && $endDateAllowanceTmp != null && $datePlannedCourseTmp != null) {
                            $garageNetworkId = $this->request->data['GarageNetwork']['GarageNetwork']['id'];
                            $trainingAllowanceTmp = array();
                            $trainingAllowanceTmp['TrainingAllowance']['garage_network_id'] = $garageNetworkId;

                            // Setting start date
                            $day = date('d', strtotime($startDateAllowanceTmp));
                            $monthStart = date('m', strtotime($startDateAllowanceTmp));
                            $monthPlanned = date('m', strtotime($datePlannedCourseTmp));
                            $monthEnd = date('m', strtotime($endDateAllowanceTmp));

                            if (($monthStart > $monthPlanned && $monthEnd >= $monthPlanned) || ($monthPlanned < $monthStart && $monthPlanned < $monthEnd)) {
                                $year = date('Y', strtotime($datePlannedCourseTmp)) - 1;
                            } else {
                                $year = date('Y', strtotime($datePlannedCourseTmp));
                            }

                            $startDate = date('Y-m-d', strtotime($year . "-" . $monthStart . "-" . $day));
                            $trainingAllowanceTmp['TrainingAllowance']['start_date'] = $startDate;
                            $trainingAllowanceTmp['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($startDate . ' +1 year -1 day'));

                            $trainingAllowanceId = null;

                            // if allowance doesn't exists, it's created
                            $existingTrainingAllowance = $this->TrainingAllowance->findByGarageNetworkIdAndStartDate($garageNetworkId, $startDate);
                            if (!$existingTrainingAllowance) {
                                $this->TrainingAllowance->add($trainingAllowanceTmp);
                                $trainingAllowanceId = $this->TrainingAllowance->getInsertID();
                            } else {
                                $trainingAllowanceId = $existingTrainingAllowance['TrainingAllowance']['id'];
                            }

                            if ($trainingAllowanceId) {
                                $this->TrainingAllowance->fillMissingAllowances($trainingAllowanceId);
                            }
                        }
                    }

                    $trainingAllowance = $this->TrainingAllowance->checkAllowanceToDelegate($this->request->data['GarageNetwork']['GarageNetwork']['id'], $this->request->data['TrainingPlannedCourse']['TrainingPlannedCourse']['date_from']);
                    if ($trainingAllowance) {
                        $this->request->data['TrainingNetworkCredit']['training_allowance_id'] = $trainingAllowance['TrainingAllowance']['id'];
                    }

                    if (in_array($network_id, array(NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT))) {
                        $substract_credits = 'false';
                        $this->request->data['add_credits_input'] = 'not_accept';
                    }

                    if (!$this->TrainingCreditNetwork->addTrainingsCreditsNetworksAccept($this->request->data)) {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        return false;
                    }
                    if ($this->request->data['add_credits_input'] == 'accept') {
                        if (!$this->GarageNetwork->editCreditGarageNetworkByIDSpent($this->request->data)) {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                            return false;
                        }
                    }
                    if (!$this->TrainingDelegate->toggle_status($delegate_data, $substract_credits)) {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        return false;
                    } else {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        return true;
                    }
                }
            } else {
                if (!$this->TrainingDelegate->toggle_status($delegate_data)) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    return false;
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    return true;
                }
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}
