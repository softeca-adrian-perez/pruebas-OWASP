<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class CommunicationsController extends AppController
{
    public $uses = array(
        'Communication',
        'CommunicationSection',
        'CommunicationNetwork',
        'CommunicationDistributorNetwork',
        'CommunicationTradingGroup',
        'CommunicationFile',
        'CommunicationPosition',
        'CommunicationCustomerActivity',
        'CommunicationSectionNetwork',
        'CommunicationSectionDistributorNetwork',
        'CommunicationSectionTradingGroup',
        'CommunicationSectionCustomerActivity',
        'CommunicationSectionPosition',
        'SectionSubsectionNetwork',
        'SectionSubsectionDistributorNetwork',
        'SectionSubsectionTradingGroup',
        'SectionSubsectionPosition',
        'SectionSubsectionCustomerActivity',
        'CustomerActivity',
        'Distributor',
        'GarageNetwork',
        'SectionSubsection',
        'Position',
        'Network',
        'DistributorNetwork',
        'TradingGroup',
        'GarageCustomerActivity',
        'DistributorCustomerActivity',
        'DistributorDistributorNetwork',
        'UserStatistic',
    );

    /**
     * Maintenance communications home page.
     */
    public function maintenance_communications()
    {
        $roleId = CakeSession::read('Auth.User.role_id');

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

            $active = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $popUp = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $search = $this->request->query;
            $searchRegion = array(
                'Communication.aag_region_id' => $aagRegionId
            );
            $this->request->data['Search'] = $search;
            $conditions = $this->Communication->conditions($search);
            $communications = $this->custom_pagination(
                $this->Communication->_query('search_maintenance'),
                array_merge($conditions, $searchRegion),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Communication',
                null,
                'PaginatorOrderCustom'
            );

            $communicationsSection = $this->CommunicationSection->search_list_region($aagRegionId);
            $communicationsSubsection = $this->SectionSubsection->search_list_region($aagRegionId);

            $this->setVarNetworkList();
            $distributorsNetworksList = $this->DistributorNetwork->getListRegion($aagRegionId);
            $tradingGroupList = $this->TradingGroup->getIndependent($aagRegionId);
            $activitiesList = $this->CustomerActivity->search_list();

            $this->set(array(
                'distributors_networks_list' => $distributorsNetworksList,
                'trading_group_list' => $tradingGroupList,
                'activities_list' => $activitiesList,
                'communications' => $communications,
                'communications_section' => $communicationsSection,
                'communications_subsection' => $communicationsSubsection,
                'active' => $active,
                'pop_up' => $popUp,
                'aag_region_id' => $aagRegionId,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Maintenance communications sections home page.
     */
    public function maintenance_communications_sections()
    {
        $roleId = CakeSession::read('Auth.User.role_id');

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $search = $this->request->query;
            $searchRegion = array(
                'aag_region_id' => $aagRegionId
            );
            $this->request->data['Search'] = $search;

            $conditions = $this->CommunicationSection->conditions($search);
            $communicationsSection = $this->custom_pagination(
                $this->CommunicationSection->_query('search_maintenance_sections'),
                array_merge($conditions, $searchRegion),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'CommunicationSection',
                null,
                'PaginatorOrderCustom'
            );

            $scrolling = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $visual = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $this->set(array(
                'communications_section' => $communicationsSection,
                'scrolling' => $scrolling,
                'visual' => $visual,
                'aag_region_id' => $aagRegionId,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Maintenance communications section subsections home page.
     */
    public function maintenance_section_subsection()
    {
        $roleId = CakeSession::read('Auth.User.role_id');

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $search = $this->request->query;
            $searchRegion = array(
                'aag_region_id' => $aagRegionId
            );
            $this->request->data['Search'] = $search;

            $sections = $this->CommunicationSection->search_list_region($aagRegionId);
            $conditions = $this->SectionSubsection->conditions($search);
            $sectionSubsections = $this->custom_pagination(
                $this->SectionSubsection->_query('search_maintenance_subsections'),
                array_merge($conditions, $searchRegion),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'SectionSubsection',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'section_subsections' => $sectionSubsections,
                'sections' => $sections,
                'aag_region_id' => $aagRegionId,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Home section page.
     */
    public function home_section($section_id, $subsection_id = null, $communication_id = null, $from_alert = ConstantsBooleans::NO)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $roleId = CakeSession::read('Auth.User.role_id');
        $section = $this->CommunicationSection->findByIdAndAagRegionId($section_id, $aagRegionId);

        if (
            $section &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)
                )
            )
        ) {
            if ($subsection_id == null && $communication_id == null) {
                $this->registerDataSection($section_id);
            } elseif ($communication_id != null) {
                $this->registerDataArticle($communication_id);
            }

            //If article(communication) does not exist or subsection doesn't exit
            if (
                ($communication_id != null && empty($this->Communication->findByIdAndAagRegionId($communication_id, $aagRegionId))) ||
                ($subsection_id != null && empty($this->SectionSubsection->findByIdAndAagRegionId($subsection_id, $aagRegionId)))
            ) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_ALERT));
                $this->redirect(
                    array(
                        'controller' => 'alerts',
                        'action' => 'home',
                    )
                );
            } else {
                if (CakeSession::read('Auth.User.garage_id')) {
                    $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
                    $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));
                    if ($networks) {
                        if ($activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'));
                        }
                    } else {
                        if ($activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, null, null, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, null, null, null, CakeSession::read('Auth.User'));
                        }
                    }
                } elseif (CakeSession::read('Auth.User.distributor_id')) {
                    $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
                    $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));

                    if ($networks) {
                        if ($activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, null, $networks, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, null, $networks, null, CakeSession::read('Auth.User'));
                        }
                    } else {
                        if ($activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, null, null, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $subsections = $this->SectionSubsection->getSubsections($section_id, null, null, null, CakeSession::read('Auth.User'));
                        }
                    }
                } else {
                    $subsections = $this->SectionSubsection->getSubsectionsBySectionAndAagRegionId($section_id, $aagRegionId);
                }

                $communicationBySubsection = null;
                if (!$communication_id && $subsection_id) {
                    if (CakeSession::read('Auth.User.garage_id')) {
                        $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
                        $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));
                        if ($networks) {
                            if ($activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'));
                            } elseif (!$activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, CakeSession::read('Auth.User.current_network'), null, null,  CakeSession::read('Auth.User'));
                            }
                        } else {
                            if ($activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, null, null, $activities,  CakeSession::read('Auth.User'));
                            } elseif (!$activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, null, null, null,  CakeSession::read('Auth.User'));
                            }
                        }
                    } elseif (CakeSession::read('Auth.User.distributor_id')) {
                        $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
                        $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));

                        if ($networks) {
                            if ($activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, null, $networks, $activities,  CakeSession::read('Auth.User'));
                            } elseif (!$activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, null, $networks, null,  CakeSession::read('Auth.User'));
                            }
                        } else {
                            if ($activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, null, null, $activities,  CakeSession::read('Auth.User'));
                            } elseif (!$activities) {
                                $communicationBySubsection = $this->Communication->getCommunicationsBySectionAndSubsections($section_id, $subsection_id, null, null, null,  CakeSession::read('Auth.User'));
                            }
                        }
                    } else {
                        $communicationBySubsection = $this->Communication->getAllBySectionSubsectionId($subsection_id);
                    }

                    if (count($communicationBySubsection) == 1) {
                        $communication_id = $communicationBySubsection[0]['Communication']['id'];
                    }
                }

                $lastCommunicationsBySubsection = null;
                if (!$communication_id && !$subsection_id) {
                    foreach ($subsections as $subsection) {
                        $lastCommunicationsBySubsection[$subsection['SectionSubsection']['id']]['Section'] = $subsection;
                        $lastCommunicationsBySubsection[$subsection['SectionSubsection']['id']]['Communication'] = $this->Communication->getLastCommunicationBySection($section_id, $subsection['SectionSubsection']['id']);
                    }
                }

                if ($communication_id) {
                    $this->Communication->CommunicationUser->add($this->Acceso->user('id'), $communication_id);
                    if ($from_alert && $roleId == ConstantsRoles::GARAGE) {
                        $this->CommunicationNetwork = ClassRegistry::init('CommunicationNetwork');
                        $communicationsNetworks = $this->CommunicationNetwork->getCommunicationsNetworksByCommunicationId($communication_id);
                        $garageNetworks = $this->GarageNetwork->findActiveNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
                        if (!in_array(CakeSession::read('Auth.User.current_network'), array_keys($communicationsNetworks))) {
                            $intersect = array_values(array_intersect(array_keys($communicationsNetworks), $garageNetworks));
                            CakeSession::write('Auth.User.current_network', $intersect[0]); // T001 SECURITY - It is not changed
                        }
                    }
                }

                $sectionsList = $this->SectionSubsection->search_list_region($aagRegionId);
                $this->setVarDate();
                $this->set(array(
                    'active_page' => ConstantsActiveHomePage::PAGE_2,
                    'section' => $section,
                    'section_id' => $section_id,
                    'subsection_id' => $subsection_id,
                    'subsections' => $subsections,
                    'sections_list' => $sectionsList,
                    'communication' => $communication_id ? $this->Communication->findById($communication_id) : null,
                    'communications_subsection' => $communicationBySubsection,
                    'communication_files' => $communication_id ? $this->CommunicationFile->findAllByCommunicationId($communication_id) : null,
                    'last_communications_by_subsection' => $lastCommunicationsBySubsection,
                ));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarDate()
    {
        $date = getdate();
        $dayWeek = array(
            '1' => __t('Garage.Monday'),
            '2' => __t('Garage.Tuesday'),
            '3' => __t('Garage.Wednesday'),
            '4' => __t('Garage.Thursday'),
            '5' => __t('Garage.Friday'),
            '6' => __t('Garage.Saturday'),
            '7' => __t('Garage.Sunday'),
        );
        $date = $date['mday'] . '/' . $date['mon'] . '/' . $date['year'];

        $this->set(array(
            'date' => $date,
            'day_week' => $dayWeek[date('N')],
        ));
    }

    /**
     * Create Communication.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $communicationSections = $this->CommunicationSection->search_list_with_subsections_region($aagRegionId);
            $subsectionsNetworks = array();
            $sectionsSubsections = isset($this->request->data['Communication']['communication_section_id']) ? $this->SectionSubsection->getSubsectionByCommunicationSection($this->request->data['Communication']['communication_section_id']) : array();

            if (!$this->request->is('get')) {
                $this->request->data['Communication']['aag_region_id'] = $aagRegionId;
                $errorSize = false;
                $errorType = false;
                $checkFile = false;
                if (!empty($this->request->data['Communication']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['Communication']['image'], $this->request->data['Communication']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot(
                            $this->request->data['Communication']['new_image'],
                            $this->request->data['Communication']['image'],
                            ConstantsFileType::IMAGE,
                            ConstantsPath::DIR_COMMUNICATIONS_IMAGE
                        );
                        $this->request->data['Communication']['image'] = $image;
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                }
                $this->request->data['Communication']['end_date'] = Fecha::toFormatoBd($this->request->data['Communication']['end_date']);
                $this->request->data['Communication']['start_date'] = Fecha::toFormatoBd($this->request->data['Communication']['start_date']);
                $this->request->data['Communication']['end_date_popup'] = Fecha::toFormatoBd($this->request->data['Communication']['end_date_popup']);
                $this->request->data['Communication']['start_date_popup'] = Fecha::toFormatoBd($this->request->data['Communication']['start_date_popup']);
                $communicationBd = $this->Communication->add($this->request->data);

                if ($communicationBd) {
                    if (isset($this->request->data['Communication']['files']) && !empty($this->request->data['Communication']['files'])) {
                        foreach ($this->request->data['Communication']['files'] as $file) {
                            if ($file['error'] == ConstantsBooleans::NO) {
                                $checkFile = FileManager::check_file($file);
                                if ($checkFile == ConstantsFileErrorTypes::OK) {
                                    if (!$this->CommunicationFile->saveFile($file, $communicationBd['Communication']['id'], ConstantsFileType::FILE)) {
                                        $errorType = true;
                                    }
                                } else {
                                    break;
                                }
                            } elseif ($file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                $errorSize = true;
                            }
                        }
                    }
                    if (!$checkFile || $checkFile == ConstantsFileErrorTypes::OK) {
                        if ($errorType || !$image) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } elseif (!$errorSize) {
                            foreach ($this->request->data['Network'] as $network) {
                                if ($network != 0) {
                                    $add = $this->CommunicationNetwork->add($communicationBd['Communication']['id'], $network);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            if (isset($this->request->data['DistributorNetwork'])) {
                                foreach ($this->request->data['DistributorNetwork'] as $distributorNetwork) {
                                    if ($distributorNetwork != 0) {
                                        $add = $this->CommunicationDistributorNetwork->add($communicationBd['Communication']['id'], $distributorNetwork);
                                        if (!$add) {
                                            return false;
                                        }
                                    }
                                }
                            }

                            foreach ($this->request->data['TradingGroup'] as $tradingGroup) {
                                if ($tradingGroup != 0) {
                                    $add = $this->CommunicationTradingGroup->add($communicationBd['Communication']['id'], $tradingGroup);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationPosition->add($communicationBd['Communication']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationPosition->add($communicationBd['Communication']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['Activity'] as $activity_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationCustomerActivity->add($communicationBd['Communication']['id'], $activity_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect(
                                array(
                                    'controller' => 'communications',
                                    'action' => 'edit',
                                    $this->Communication->getLastInsertID()
                                )
                            );
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                        }
                    } elseif ($checkFile == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                    }
                } else {
                    $this->request->data['Communication']['end_date'] = Fecha::toFormatoVista($this->request->data['Communication']['end_date']);
                    $this->request->data['Communication']['start_date'] = Fecha::toFormatoVista($this->request->data['Communication']['start_date']);
                    $this->request->data['Communication']['end_date_popup'] = Fecha::toFormatoVista($this->request->data['Communication']['end_date_popup']);
                    $this->request->data['Communication']['start_date_popup'] = Fecha::toFormatoVista($this->request->data['Communication']['start_date_popup']);

                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->setVarNetworks();
            $this->setVarDistributorsNetworks($aagRegionId);
            $this->setVarCustomersActivities();
            $this->setVarTradingGroups();
            $this->setVarCancel1();
            $this->set(array(
                'communication_sections' => $communicationSections,
                'sections_subsections' => $sectionsSubsections,
                'subsections_networks' => $subsectionsNetworks,
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
                'subsection' => array(),
                'subsections_distributors_networks' => array(),
                'subsections_trading_groups' => array(),
                'subsections_positions' => array(),
                'subsections_customers_activities' => array()
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Communication.
     */
    public function edit($communication_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $communication = $this->Communication->getCommunicationByIdAndAagRegionId($communication_id, $aagRegionId);

        if (
            $communication &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'communications',
                    'action' => 'maintenance_communications',
                    $communication_id
                ),
            );
            $communicationSections = $this->CommunicationSection->search_list_with_subsections_region($aagRegionId);
            $oldImage = $communication['Communication']['image'];
            $sectionsSubsections = $this->SectionSubsection->getSubsectionByCommunicationSection($communication['Communication']['communication_section_id']);

            $communicationFiles = $this->CommunicationFile->findAllByCommunicationId($communication_id);

            if (!$this->request->is('get')) {
                $errorSize = false;
                $image = true;
                $errorType = false;
                $checkFile = false;

                if ($this->request->data['Communication']['new_image'] == 'false') {
                    $image = false;
                } elseif (!empty($this->request->data['Communication']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['Communication']['image'], $this->request->data['Communication']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot(
                            $this->request->data['Communication']['new_image'],
                            $this->request->data['Communication']['image'],
                            ConstantsFileType::IMAGE,
                            ConstantsPath::DIR_COMMUNICATIONS_IMAGE
                        );
                        FileManager::delete_file(WWW_ROOT, ConstantsPath::DIR_COMMUNICATIONS_IMAGE . DS . $oldImage);
                        $this->request->data['Communication']['image'] = $image;
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                }

                if ($image) {
                    $this->request->data['Communication']['end_date'] = Fecha::toFormatoBd($this->request->data['Communication']['end_date']);
                    $this->request->data['Communication']['start_date'] = Fecha::toFormatoBd($this->request->data['Communication']['start_date']);
                    $this->request->data['Communication']['end_date_popup'] = Fecha::toFormatoBd($this->request->data['Communication']['end_date_popup']);
                    $this->request->data['Communication']['start_date_popup'] = Fecha::toFormatoBd($this->request->data['Communication']['start_date_popup']);
                    $communicationBd = $this->Communication->edit($this->request->data);

                    if ($communicationBd) {
                        unset($this->request->data['Communication']['file'][0]);
                        foreach ($this->request->data['Communication']['files'] as $file) {
                            if ($file['error'] == ConstantsBooleans::NO) {
                                $checkFile = FileManager::check_file($file);
                                if ($checkFile == ConstantsFileErrorTypes::OK) {
                                    if (!$this->CommunicationFile->saveFile($file, $communicationBd['Communication']['id'], ConstantsFileType::FILE)) {
                                        $errorType = true;
                                    }
                                } else {
                                    break;
                                }
                            } elseif ($file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                $errorSize = true;
                            }
                        }

                        // Delete networks
                        $communicationNetworks = $this->CommunicationNetwork->findAllByCommunicationId($communicationBd['Communication']['id']);
                        if ($communicationNetworks) {
                            foreach ($communicationNetworks as $communication_tmp) {
                                if (!$this->CommunicationNetwork->delete($communication_tmp['CommunicationNetwork']['id'])) {
                                    return false;
                                }
                            }
                        }
                        // Delete distributors_networks
                        $this->CommunicationDistributorNetwork->deleteAll(array('communication_id' => $communicationBd['Communication']['id']));

                        // Delete trading_groups
                        $communication_trading_groups = $this->CommunicationTradingGroup->findAllByCommunicationId($communicationBd['Communication']['id']);
                        if ($communication_trading_groups) {
                            foreach ($communication_trading_groups as $communication_tmp) {
                                if (!$this->CommunicationTradingGroup->delete($communication_tmp['CommunicationTradingGroup']['id'])) {
                                    return false;
                                }
                            }
                        }
                        // Delete Positions
                        $this->CommunicationPosition->deleteAll(array('communication_id' => $communicationBd['Communication']['id']));

                        // Delete Activities
                        $this->CommunicationCustomerActivity->deleteAll(array('communication_id' => $communicationBd['Communication']['id']));

                        foreach ($this->request->data['Network'] as $network) {
                            if ($network != 0) {
                                $add = $this->CommunicationNetwork->add($communicationBd['Communication']['id'], $network);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        if (isset($this->request->data['DistributorNetwork'])) {
                            foreach ($this->request->data['DistributorNetwork'] as $distributorNetwork) {
                                if ($distributorNetwork != 0) {
                                    $add = $this->CommunicationDistributorNetwork->add($communicationBd['Communication']['id'], $distributorNetwork);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }
                        }

                        foreach ($this->request->data['TradingGroup'] as $tradingGroup) {
                            if ($tradingGroup != 0) {
                                $add = $this->CommunicationTradingGroup->add($communicationBd['Communication']['id'], $tradingGroup);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                            if ($value != 0) {
                                $add = $this->CommunicationPosition->add($communicationBd['Communication']['id'], $position_id);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                            if ($value != 0) {
                                $add = $this->CommunicationPosition->add($communicationBd['Communication']['id'], $position_id);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['Activity'] as $activity_id => $value) {
                            if ($value != 0) {
                                $add = $this->CommunicationCustomerActivity->add($communicationBd['Communication']['id'], $activity_id);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        if (!$checkFile || $checkFile == ConstantsFileErrorTypes::OK) {
                            if ($errorSize) {
                                $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                            } elseif ($errorType) {
                                $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                            } else {
                                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                $this->redirect($this->request->here);
                            }
                        } elseif ($checkFile == ConstantsFileErrorTypes::SIZE_ERROR) {
                            $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                        }
                    }
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            } else {
                $this->request->data = $communication;
                $this->request->data['Communication']['end_date'] = Fecha::toFormatoVista($this->request->data['Communication']['end_date']);
                $this->request->data['Communication']['start_date'] = Fecha::toFormatoVista($this->request->data['Communication']['start_date']);
                $this->request->data['Communication']['end_date_popup'] = Fecha::toFormatoVista($this->request->data['Communication']['end_date_popup']);
                $this->request->data['Communication']['start_date_popup'] = Fecha::toFormatoVista($this->request->data['Communication']['start_date_popup']);
            }

            $subsection = $this->SectionSubsection->findById($communication['Communication']['section_subsection_id']);
            $subsectionsNetworks = $this->SectionSubsectionNetwork->getSectionsSubsectionsNetworksBySectionSubsectionId($communication['Communication']['section_subsection_id']);
            $subsectionsDistributorsNetworks = $this->SectionSubsectionDistributorNetwork->getSectionsSubsectionsDistributorsNetworksBySectionSubsectionId($communication['Communication']['section_subsection_id']);
            $subsectionsTradingGroups = $this->SectionSubsectionTradingGroup->getTGBySectionSubsectionId($communication['Communication']['section_subsection_id']);
            $subsectionsCustomersActivities = $this->SectionSubsectionCustomerActivity->getListActivitiesBySectionSubsectionId($communication['Communication']['section_subsection_id']);
            $subsectionsPositions = $this->SectionSubsectionPosition->getListPositionsBySectionSubsectionId($communication['Communication']['section_subsection_id']);

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->setVarNetworkList();
            $this->setVarNetworks();
            $this->setVarDistributorsNetworks($aagRegionId);
            $this->setVarCustomersActivities();
            $this->setVarCommunicationsNetworks($communication_id);
            $this->setVarCommunicationsDistributorsNetworks($communication_id);
            $this->setVarCommunicationsTradingGroups($communication_id);
            $this->setVarCommunicationsPositions($communication_id);
            $this->setVarCommunicationsActivities($communication_id);
            $this->setVarTradingGroups();
            $this->setVarCancel1();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'communication' => $communication,
                'sections_subsections' => $sectionsSubsections,
                'subsection' => $subsection,
                'communication_sections' => $communicationSections,
                'communication_files' => $communicationFiles,
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
                'subsections_networks' => $subsectionsNetworks,
                'subsections_distributors_networks' => $subsectionsDistributorsNetworks,
                'subsections_trading_groups' => $subsectionsTradingGroups,
                'subsections_customers_activities' => $subsectionsCustomersActivities,
                'subsections_positions' => $subsectionsPositions,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create CommunicationSection.
     */
    public function add_section()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

            if (!$this->request->is('get')) {
                $this->request->data['CommunicationSection']['aag_region_id'] = $aagRegionId;
                $errorType = false;
                $checkImage = false;
                if (!empty($this->request->data['CommunicationSection']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['CommunicationSection']['image'], $this->request->data['CommunicationSection']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot($this->request->data['CommunicationSection']['new_image'], $this->request->data['CommunicationSection']['image'], ConstantsFileType::IMAGE, ConstantsPath::DIR_COMMUNICATIONS_IMAGE);
                        $this->request->data['CommunicationSection']['image'] = $image;
                        if (!$image) {
                            $errorType = true;
                        }
                    }
                }

                if (!$errorType) {
                    if ($checkImage == ConstantsFileErrorTypes::OK || !$checkImage) {
                        $sectionBd = $this->CommunicationSection->add($this->request->data);
                        if ($sectionBd) {

                            foreach ($this->request->data['Network'] as $network) {
                                if ($network != 0) {
                                    $add = $this->CommunicationSectionNetwork->add($sectionBd['CommunicationSection']['id'], $network);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            if (isset($this->request->data['DistributorNetwork'])) {
                                foreach ($this->request->data['DistributorNetwork'] as $distributorNetwork) {
                                    if ($distributorNetwork != 0) {
                                        $add = $this->CommunicationSectionDistributorNetwork->add($sectionBd['CommunicationSection']['id'], $distributorNetwork);
                                        if (!$add) {
                                            return false;
                                        }
                                    }
                                }
                            }

                            foreach ($this->request->data['TradingGroup'] as $tradingGroup) {
                                if ($tradingGroup != 0) {
                                    $add = $this->CommunicationSectionTradingGroup->add($sectionBd['CommunicationSection']['id'], $tradingGroup);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationSectionPosition->add($sectionBd['CommunicationSection']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationSectionPosition->add($sectionBd['CommunicationSection']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['Activity'] as $activity_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationSectionCustomerActivity->add($sectionBd['CommunicationSection']['id'], $activity_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect(
                                array(
                                    'controller' => 'communications',
                                    'action' => 'edit_section',
                                    $this->CommunicationSection->getLastInsertID()
                                )
                            );
                        }
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            }
            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->setVarNetworks();
            $this->setVarDistributorsNetworks($aagRegionId);
            $this->setVarCustomersActivities();
            $this->setVarTradingGroups();
            $this->setVarCancel2();
            $this->set(array(
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit CommunicationSection.
     */
    public function edit_section($communication_section_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $communicationSection = $this->CommunicationSection->findByIdAndAagRegionId($communication_section_id, $aagRegionId);

        if (
            $communicationSection &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $oldImage = $communicationSection['CommunicationSection']['image'];

            if (!$this->request->is('get')) {
                $errorType = false;
                if ($this->request->data['CommunicationSection']['new_image'] == 'false') {
                    $errorType = true;
                } elseif (!empty($this->request->data['CommunicationSection']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['CommunicationSection']['image'], $this->request->data['CommunicationSection']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot($this->request->data['CommunicationSection']['new_image'], $this->request->data['CommunicationSection']['image'], ConstantsFileType::IMAGE, ConstantsPath::DIR_COMMUNICATIONS_IMAGE);
                        $this->request->data['CommunicationSection']['image'] = $image;
                        if (!$image) {
                            $errorType = true;
                        } else {
                            FileManager::delete_file(WWW_ROOT, ConstantsPath::DIR_COMMUNICATIONS_IMAGE . DS . $oldImage);
                        }
                    }
                }
                if (!$errorType) {
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $communicationSectionBd = $this->CommunicationSection->edit($this->request->data, $communicationSection);
                        if ($communicationSectionBd) {
                            // Delete networks
                            $communicationSectionsNetworks = $this->CommunicationSectionNetwork->findAllByCommunicationSectionId($communicationSectionBd['CommunicationSection']['id']);
                            if ($communicationSectionsNetworks) {
                                foreach ($communicationSectionsNetworks as $communication) {
                                    if (!$this->CommunicationSectionNetwork->delete($communication['CommunicationSectionNetwork']['id'])) {
                                        return false;
                                    }
                                }
                            }

                            // Delete distributors_networks
                            $this->CommunicationSectionDistributorNetwork->deleteAll(array('communication_section_id' =>  $communicationSectionBd['CommunicationSection']['id']));

                            //Delete trading_groups
                            $communicationSectionTradingGroups = $this->CommunicationSectionTradingGroup->findAllByCommunicationSectionId($communicationSectionBd['CommunicationSection']['id']);
                            if ($communicationSectionTradingGroups) {
                                foreach ($communicationSectionTradingGroups as $communication) {
                                    if (!$this->CommunicationSectionTradingGroup->delete($communication['CommunicationSectionTradingGroup']['id'])) {
                                        return false;
                                    }
                                }
                            }

                            // Delete Positions
                            $this->CommunicationSectionPosition->deleteAll(array('communication_section_id' =>  $communicationSectionBd['CommunicationSection']['id']));

                            // Delete Activities
                            $this->CommunicationSectionCustomerActivity->deleteAll(array('communication_section_id' =>  $communicationSectionBd['CommunicationSection']['id']));

                            foreach ($this->request->data['Network'] as $network) {
                                if ($network != 0) {
                                    $add = $this->CommunicationSectionNetwork->add($communicationSectionBd['CommunicationSection']['id'], $network);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            if (isset($this->request->data['DistributorNetwork'])) {
                                foreach ($this->request->data['DistributorNetwork'] as $distributorNetwork) {
                                    if ($distributorNetwork != 0) {
                                        $add = $this->CommunicationSectionDistributorNetwork->add($communicationSectionBd['CommunicationSection']['id'], $distributorNetwork);
                                        if (!$add) {
                                            return false;
                                        }
                                    }
                                }
                            }

                            foreach ($this->request->data['TradingGroup'] as $tradingGroup) {
                                if ($tradingGroup != 0) {
                                    $add = $this->CommunicationSectionTradingGroup->add($communicationSectionBd['CommunicationSection']['id'], $tradingGroup);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationSectionPosition->add($communicationSectionBd['CommunicationSection']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationSectionPosition->add($communicationSectionBd['CommunicationSection']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['Activity'] as $activity_id => $value) {
                                if ($value != 0) {
                                    $add = $this->CommunicationSectionCustomerActivity->add($communicationSectionBd['CommunicationSection']['id'], $activity_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect($this->request->here);
                        }
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            } else {
                $this->request->data = $communicationSection;
            }

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->setVarNetworks();
            $this->setVarDistributorsNetworks($aagRegionId);
            $this->setVarCustomersActivities();
            $this->setVarTradingGroups();
            $this->setVarCommunicationsSectionsNetworks($communication_section_id);
            $this->setVarCommunicationsSectionsDistributorsNetworks($communication_section_id);
            $this->setVarCommunicationsSectionsTradingGroups($communication_section_id);
            $this->setVarCommunicationsSectionsPositions($communication_section_id);
            $this->setVarCommunicationsSectionsActivities($communication_section_id);
            $this->setVarCancel2();
            $this->set(array(
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
                'communication_section' => $communicationSection,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create communication SectionSubsection.
     */
    public function add_subsection()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

            if (!$this->request->is('get')) {
                $this->request->data['SectionSubsection']['aag_region_id'] = $aagRegionId;
                $errorType = false;
                $checkImage = false;
                if (!empty($this->request->data['SectionSubsection']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['SectionSubsection']['image'], $this->request->data['SectionSubsection']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot($this->request->data['SectionSubsection']['new_image'], $this->request->data['SectionSubsection']['image'], ConstantsFileType::IMAGE, ConstantsPath::DIR_COMMUNICATIONS_IMAGE);
                        $this->request->data['SectionSubsection']['image'] = $image;
                        if (!$image) {
                            $errorType = true;
                        }
                    }
                }

                if (!$errorType) {
                    if ($checkImage == ConstantsFileErrorTypes::OK || !$checkImage) {
                        $subsection = $this->SectionSubsection->add($this->request->data);
                        if ($subsection) {
                            foreach ($this->request->data['Network'] as $network) {
                                if ($network != 0) {
                                    $add = $this->SectionSubsectionNetwork->add($subsection['SectionSubsection']['id'], $network);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            if (isset($this->request->data['DistributorNetwork'])) {
                                foreach ($this->request->data['DistributorNetwork'] as $distributorNetwork) {
                                    if ($distributorNetwork != 0) {
                                        $add = $this->SectionSubsectionDistributorNetwork->add($subsection['SectionSubsection']['id'], $distributorNetwork);
                                        if (!$add) {
                                            return false;
                                        }
                                    }
                                }
                            }

                            foreach ($this->request->data['TradingGroup'] as $tradingGroup) {
                                if ($tradingGroup != 0) {
                                    $add = $this->SectionSubsectionTradingGroup->add($subsection['SectionSubsection']['id'], $tradingGroup);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->SectionSubsectionPosition->add($subsection['SectionSubsection']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    $add = $this->SectionSubsectionPosition->add($subsection['SectionSubsection']['id'], $position_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['Activity'] as $activity_id => $value) {
                                if ($value != 0) {
                                    $add = $this->SectionSubsectionCustomerActivity->add($subsection['SectionSubsection']['id'], $activity_id);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }

                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect(
                                array(
                                    'controller' => 'communications',
                                    'action' => 'edit_subsection',
                                    $this->SectionSubsection->getLastInsertID()
                                )
                            );
                        }
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            }

            $this->setVarCancel3();

            $sections = $this->CommunicationSection->search_list_region($aagRegionId);

            $this->set(
                array(
                    'sections' => $sections,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit communication SectionSubsection.
     */
    public function edit_subsection($section_subsection_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $sectionSubsection = $this->SectionSubsection->findByIdAndAagRegionId($section_subsection_id, $aagRegionId);

        if (
            $sectionSubsection &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $oldImage = $sectionSubsection['SectionSubsection']['image'];
            $sections = $this->CommunicationSection->search_list_region($aagRegionId);
            $section = $this->CommunicationSection->findById($sectionSubsection['SectionSubsection']['communication_section_id']);
            if (!$this->request->is('get')) {
                $error_file = false;
                if ($this->request->data['SectionSubsection']['new_image'] == 'false') {
                    $error_file = true;
                } elseif (!empty($this->request->data['SectionSubsection']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['SectionSubsection']['image'], $this->request->data['SectionSubsection']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot($this->request->data['SectionSubsection']['new_image'], $this->request->data['SectionSubsection']['image'], ConstantsFileType::IMAGE, ConstantsPath::DIR_COMMUNICATIONS_IMAGE);
                        $this->request->data['SectionSubsection']['image'] = $image;
                        if (!$image) {
                            $error_file = true;
                        } else {
                            $error_file = !FileManager::delete_file(WWW_ROOT, ConstantsPath::DIR_COMMUNICATIONS_IMAGE . DS . $oldImage);
                        }
                    }
                }

                $sectionSubsectionBd = $this->SectionSubsection->edit($this->request->data, $sectionSubsection);

                if ($sectionSubsectionBd && !$error_file) {
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        // Delete networks
                        $sectionsSubsectionsNetworks = $this->SectionSubsectionNetwork->findAllBySectionSubsectionId($sectionSubsectionBd['SectionSubsection']['id']);
                        if ($sectionsSubsectionsNetworks) {
                            foreach ($sectionsSubsectionsNetworks as $sections_subsections_network) {
                                if (!$this->SectionSubsectionNetwork->delete($sections_subsections_network['SectionSubsectionNetwork']['id'])) {
                                    return false;
                                }
                            }
                        }

                        // Delete distributors_networks
                        $this->SectionSubsectionDistributorNetwork->deleteAll(array('section_subsection_id' =>  $sectionSubsectionBd['SectionSubsection']['id']));

                        //Delete trading_groups
                        $sectionSubsectionTradingGroups = $this->SectionSubsectionTradingGroup->findAllBySectionSubsectionId($sectionSubsectionBd['SectionSubsection']['id']);
                        if ($sectionSubsectionTradingGroups) {
                            foreach ($sectionSubsectionTradingGroups as $sectionSubsectionTradingGroup) {
                                if (!$this->SectionSubsectionTradingGroup->delete($sectionSubsectionTradingGroup['SectionSubsectionTradingGroup']['id'])) {
                                    return false;
                                }
                            }
                        }

                        // Delete Positions
                        $this->SectionSubsectionPosition->deleteAll(array('section_subsection_id' =>  $sectionSubsectionBd['SectionSubsection']['id']));

                        // Delete Activities
                        $this->SectionSubsectionCustomerActivity->deleteAll(array('section_subsection_id' =>  $sectionSubsectionBd['SectionSubsection']['id']));

                        foreach ($this->request->data['Network'] as $network) {
                            if ($network != 0) {
                                $add = $this->SectionSubsectionNetwork->add($sectionSubsectionBd['SectionSubsection']['id'], $network);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        if (isset($this->request->data['DistributorNetwork'])) {
                            foreach ($this->request->data['DistributorNetwork'] as $distributorNetwork) {
                                if ($distributorNetwork != 0) {
                                    $add = $this->SectionSubsectionDistributorNetwork->add($sectionSubsectionBd['SectionSubsection']['id'], $distributorNetwork);
                                    if (!$add) {
                                        return false;
                                    }
                                }
                            }
                        }

                        foreach ($this->request->data['TradingGroup'] as $tradingGroup) {
                            if ($tradingGroup != 0) {
                                $add = $this->SectionSubsectionTradingGroup->add($sectionSubsectionBd['SectionSubsection']['id'], $tradingGroup);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                            if ($value != 0) {
                                $add = $this->SectionSubsectionPosition->add($sectionSubsectionBd['SectionSubsection']['id'], $position_id);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                            if ($value != 0) {
                                $add = $this->SectionSubsectionPosition->add($sectionSubsectionBd['SectionSubsection']['id'], $position_id);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['Activity'] as $activity_id => $value) {
                            if ($value != 0) {
                                $add = $this->SectionSubsectionCustomerActivity->add($sectionSubsectionBd['SectionSubsection']['id'], $activity_id);
                                if (!$add) {
                                    return false;
                                }
                            }
                        }

                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } elseif ($error_file) {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            } else {
                $this->request->data = $sectionSubsection;
            }

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);
            $sections_positions = $this->CommunicationSectionPosition->getListPositionsByCommunicationSectionId($section['CommunicationSection']['id']);

            $this->setVarNetworks();
            $sectionsNetworks = $this->CommunicationSectionNetwork->getCommunicationsSectionsNetworksByCommunicationId($section['CommunicationSection']['id']);
            $this->setVarDistributorsNetworks($aagRegionId);
            $sectionsDistributorsNetworks = $this->CommunicationSectionDistributorNetwork->getCommunicationsSectionsDistributorsNetworksByCommunicationId($section['CommunicationSection']['id']);
            $this->setVarCustomersActivities();
            $sectionsCustomersActivities = $this->CommunicationSectionCustomerActivity->getListActivitiesByCommunicationSectionId($section['CommunicationSection']['id']);
            $this->setVarTradingGroups();
            $sectionsTradingGroups = $this->CommunicationSectionTradingGroup->getTGByCommunicationSectionId($section['CommunicationSection']['id']);

            $this->setVarSectionsSubsectionsNetworks($section_subsection_id);
            $this->setVarSectionsSubsectionsDistributorsNetworks($section_subsection_id);
            $this->setVarSectionsSubsectionsTradingGroups($section_subsection_id);
            $this->setVarSectionsSubsectionsPositions($section_subsection_id);
            $this->setVarSectionsSubsectionsActivities($section_subsection_id);
            $this->setVarCancel3();
            $this->set(array(
                'sections_networks' => $sectionsNetworks,
                'sections_distributors_networks' => $sectionsDistributorsNetworks,
                'sections_trading_groups' => $sectionsTradingGroups,
                'sections_customers_activities' => $sectionsCustomersActivities,
                'sections_positions' => $sections_positions,
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
                'sections' => $sections,
                'section_subsection' => $sectionSubsection,
                'section' => $section,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete CommunicationSection.
     */
    public function ajax_delete_section()
    {
        $this->verify_ajax($this->request);

        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $sectionId = $this->request->data['section_id'];
        $section = $this->CommunicationSection->findByIdAndAagRegionId($sectionId, $aagRegionId);

        if (
            $section &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            //First: Check if there's a communication with this section.
            $communication = $this->Communication->findByCommunicationSectionId($sectionId);

            if (empty($communication)) {
                //Second: Delete Subsections:
                $subsections = $this->SectionSubsection->find(
                    'list',
                    array('conditions' => array('communication_section_id' => $sectionId))
                );

                if (!empty($subsections)) {
                    foreach ($subsections as $subsection_id) {
                        //Delete relationships with other tables
                        $this->deleteRelationshipsSubcategory($subsection_id);
                        $subsection_bd = $this->SectionSubsection->findById($subsection_id);
                        $bd = $this->SectionSubsection->delete($subsection_id);

                        // if SectionSubsection has an image, it was deleted
                        if ($bd && $subsection_bd['SectionSubsection']['image'] != '') {
                            FileManager::delete_file(WWW_ROOT, FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $subsection_bd['SectionSubsection']['image']);
                        }
                    }
                }

                //Third: Delete Section
                $this->deleteRelationshipsCategory($sectionId);
                $bd = $this->CommunicationSection->delete($sectionId);
                // if CommunicationSection has an image, it was deleted
                if ($bd && $section['CommunicationSection']['image'] != '') {
                    FileManager::delete_file(WWW_ROOT, FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $section['CommunicationSection']['image']);
                }
            } else {
                echo 'error';
            }

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function deleteRelationshipsCategory($sectionId)
    {
        //CommunicationSectionCustomerActivity
        $communicationSectionCustomerActivities = $this->CommunicationSectionCustomerActivity->findAllByCommunicationSectionId($sectionId);
        if (!empty($communicationSectionCustomerActivities)) {
            foreach ($communicationSectionCustomerActivities as $communicationSectionCustomerActivity) {
                $this->CommunicationSectionCustomerActivity->delete($communicationSectionCustomerActivity['CommunicationSectionCustomerActivity']['id']);
            }
        }

        //CommunicationSectionDistributorNetwork
        $communicationSectionDistributorNetworks = $this->CommunicationSectionDistributorNetwork->findAllByCommunicationSectionId($sectionId);
        if (!empty($communicationSectionDistributorNetworks)) {
            foreach ($communicationSectionDistributorNetworks as $communicationSectionDistributorNetwork) {
                $this->CommunicationSectionDistributorNetwork->delete($communicationSectionDistributorNetwork['CommunicationSectionDistributorNetwork']['id']);
            }
        }

        //CommunicationSectionNetwork
        $communicationSectionNetworks = $this->CommunicationSectionNetwork->findAllByCommunicationSectionId($sectionId);
        if (!empty($communicationSectionNetworks)) {
            foreach ($communicationSectionNetworks as $communicationSectionNetwork) {
                $this->CommunicationSectionNetwork->delete($communicationSectionNetwork['CommunicationSectionNetwork']['id']);
            }
        }

        //CommunicationSectionTradingGroup
        $communicationSectionTradingGroups = $this->CommunicationSectionTradingGroup->findAllByCommunicationSectionId($sectionId);
        if (!empty($communicationSectionTradingGroups)) {
            foreach ($communicationSectionTradingGroups as $communicationSectionTradingGroup) {
                $this->CommunicationSectionTradingGroup->delete($communicationSectionTradingGroup['CommunicationSectionTradingGroup']['id']);
            }
        }

        //CommunicationSectionPosition
        $communicationSectionPositions = $this->CommunicationSectionPosition->findAllByCommunicationSectionId($sectionId);
        if (!empty($communicationSectionPositions)) {
            foreach ($communicationSectionPositions as $communicationSectionPosition) {
                $this->CommunicationSectionPosition->delete($communicationSectionPosition['CommunicationSectionPosition']['id']);
            }
        }

        return true;
    }

    /**
     * AJAX delete SectionSubsection.
     */
    public function ajax_delete_subsection()
    {
        $this->verify_ajax($this->request);

        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $subsectionId = $this->request->data['subsection_id'];
        $subsection = $this->SectionSubsection->findByIdAndAagRegionId($subsectionId, $aagRegionId);

        if (
            $subsection &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            //First: Check if there's a communication with this subsection.
            $communication = $this->Communication->findBySectionSubsectionId($subsectionId);

            if (empty($communication)) {
                //Delete relationships with other tables
                $this->deleteRelationshipsSubcategory($subsectionId);

                //Second: Delete Subsections:
                $bd = $this->SectionSubsection->delete($subsectionId);

                // if SectionSubsection has an image, it was deleted
                if ($bd && $subsection['SectionSubsection']['image'] != '') {
                    FileManager::delete_file(WWW_ROOT, FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $subsection['SectionSubsection']['image']);
                }
            } else {
                echo 'error';
            }

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function deleteRelationshipsSubcategory($subsectionId)
    {
        //SectionSubsectionCustomerActivity
        $sectionSubsectionCustomerActivities = $this->SectionSubsectionCustomerActivity->findAllBySectionSubsectionId($subsectionId);
        if (!empty($sectionSubsectionCustomerActivities)) {
            foreach ($sectionSubsectionCustomerActivities as $sectionSubsectionCustomerActivity) {
                $this->SectionSubsectionCustomerActivity->delete($sectionSubsectionCustomerActivity['SectionSubsectionCustomerActivity']['id']);
            }
        }

        //SectionSubsectionDistributorNetwork
        $sectionSubsectionDistributorNetworks = $this->SectionSubsectionDistributorNetwork->findAllBySectionSubsectionId($subsectionId);
        if (!empty($sectionSubsectionDistributorNetworks)) {
            foreach ($sectionSubsectionDistributorNetworks as $sectionSubsectionDistributorNetwork) {
                $this->SectionSubsectionDistributorNetwork->delete($sectionSubsectionDistributorNetwork['SectionSubsectionDistributorNetwork']['id']);
            }
        }

        //SectionSubsectionNetwork
        $sectionSubsectionNetworks = $this->SectionSubsectionNetwork->findAllBySectionSubsectionId($subsectionId);
        if (!empty($sectionSubsectionNetworks)) {
            foreach ($sectionSubsectionNetworks as $sectionSubsectionNetwork) {
                $this->SectionSubsectionNetwork->delete($sectionSubsectionNetwork['SectionSubsectionNetwork']['id']);
            }
        }

        //SectionSubsectionTradingGroup
        $sectionSubsectionTradingGroups = $this->SectionSubsectionTradingGroup->findAllBySectionSubsectionId($subsectionId);
        if (!empty($sectionSubsectionTradingGroups)) {
            foreach ($sectionSubsectionTradingGroups as $sectionSubsectionTradingGroup) {
                $this->SectionSubsectionTradingGroup->delete($sectionSubsectionTradingGroup['SectionSubsectionTradingGroup']['id']);
            }
        }

        //SectionSubsectionPosition
        $sectionSubsectionPositions = $this->SectionSubsectionPosition->findAllBySectionSubsectionId($subsectionId);
        if (!empty($sectionSubsectionPositions)) {
            foreach ($sectionSubsectionPositions as $sectionSubsectionPosition) {
                $this->SectionSubsectionPosition->delete($sectionSubsectionPosition['SectionSubsectionPosition']['id']);
            }
        }

        return true;
    }

    /**
     * AJAX delete Communication.
     */
    public function ajax_delete_communication()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $communicationId = $this->request->data['communication_id'];

            //First: Check if there's a communication with this id.
            $communication = $this->Communication->findByIdAndAagRegionId($communicationId, $aagRegionId);

            if (!empty($communication)) {
                //FIRST:
                //CommunicationNetwork
                $communicationsNetworks = $this->CommunicationNetwork->findAllByCommunicationId($communicationId);
                if (!empty($communicationsNetworks)) {
                    foreach ($communicationsNetworks as $communication_network) {
                        $this->CommunicationNetwork->delete($communication_network['CommunicationNetwork']['id']);
                    }
                }

                //CommunicationDistributorNetworks
                $communicationsDistributorNetworks = $this->CommunicationDistributorNetwork->findAllByCommunicationId($communicationId);
                if (!empty($communicationsDistributorNetworks)) {
                    foreach ($communicationsDistributorNetworks as $communication_distributor_network) {
                        $this->CommunicationDistributorNetwork->delete($communication_distributor_network['CommunicationDistributorNetwork']['id']);
                    }
                }

                //CommunicationTradingGroup
                $communicationsTradingGroups = $this->CommunicationTradingGroup->findAllByCommunicationId($communicationId);
                if (!empty($communicationsTradingGroups)) {
                    foreach ($communicationsTradingGroups as $communication_tg) {
                        $this->CommunicationTradingGroup->delete($communication_tg['CommunicationTradingGroup']['id']);
                    }
                }

                //Imagen
                if ($communication['Communication']['image'] != '') {
                    FileManager::delete_file(WWW_ROOT, FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication['Communication']['image']);
                }

                //CommunicationFile
                $communicationsFiles = $this->CommunicationFile->findAllByCommunicationId($communicationId);
                if (!empty($communicationsFiles)) {
                    foreach ($communicationsFiles as $communication_file) {
                        if (FileManager::delete_file(WWW_ROOT, substr(ConstantsPath::DIR_COMMUNICATIONS_FILES, 3) . DS . $communication_file['CommunicationFile']['file'])) {
                            $this->CommunicationFile->delete($communication_file['CommunicationFile']['id']);
                        }
                    }
                }

                //CommunicationCustomerActivity
                $communicationsCustomerActivitites = $this->CommunicationCustomerActivity->findAllByCommunicationId($communicationId);
                if (!empty($communicationsCustomerActivitites)) {
                    foreach ($communicationsCustomerActivitites as $communication_customer_activitity) {
                        $this->CommunicationCustomerActivity->delete($communication_customer_activitity['CommunicationCustomerActivity']['id']);
                    }
                }

                //CommunicationPosition
                $communicationsPositions = $this->CommunicationPosition->findAllByCommunicationId($communicationId);
                if (!empty($communicationsPositions)) {
                    foreach ($communicationsPositions as $communication_position) {
                        $this->CommunicationPosition->delete($communication_position['CommunicationPosition']['id']);
                    }
                }

                //SECOND: Delete communication
                $this->Communication->delete($communicationId);
            } else {
                echo 'error';
            }

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarNetworkList()
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $networksList = $this->Network->find(
            'list',
            array(
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId
                ),
            )
        );

        $this->set(array(
            'networks_list' => $networksList
        ));
    }

    private function setVarNetworks()
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $networks = $this->Network->find(
            'all',
            array(
                'fields' => array(
                    'Network.id',
                    'Network.name',
                    'Network.image',
                ),
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId
                ),
            )
        );

        $this->set(array(
            'networks' => $networks
        ));
    }

    private function setVarDistributorsNetworks($aagRegionId)
    {
        $distributorsNetworks = $this->DistributorNetwork->find(
            'all',
            array(
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                    'DistributorNetwork.image',
                ),
                'conditions' => array(
                    'DistributorNetwork.aag_region_id' => $aagRegionId
                ),
            )
        );

        $this->set(array(
            'distributors_networks' => $distributorsNetworks
        ));
    }

    private function setVarCustomersActivities()
    {
        $customersActivities = $this->CustomerActivity->search_list();

        $this->set(array(
            'customers_activities' => $customersActivities
        ));
    }

    private function getRolePositions($roleId)
    {
        $positions = $this->Position->find(
            'all',
            array(
                'conditions' => array(
                    'role_id' => $roleId
                ),
                'fields' => array(
                    'Position.id',
                    'Position.name' . __s(),
                )
            )
        );

        return $positions;
    }

    private function setVarTradingGroups()
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $tradingGroups = $this->TradingGroup->find(
            'all',
            array(
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'TradingGroup.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'TradingGroup.id',
                    'TradingGroup.name',
                    'TradingGroup.image',
                )
            )
        );

        $this->set(array(
            'trading_groups' => $tradingGroups
        ));
    }

    private function setVarCommunicationsNetworks($communicationId)
    {
        $communicationsNetworks = $this->CommunicationNetwork->getCommunicationsNetworksByCommunicationId($communicationId);

        $this->set(array(
            'communications_networks' => $communicationsNetworks
        ));
    }

    private function setVarCommunicationsSectionsNetworks($communicationSectionId)
    {
        $communicationsSectionsNetworks = $this->CommunicationSectionNetwork->getCommunicationsSectionsNetworksByCommunicationId($communicationSectionId);

        $this->set(array(
            'communications_sections_networks' => $communicationsSectionsNetworks
        ));
    }

    private function setVarSectionsSubsectionsNetworks($sectionSubsectionId)
    {
        $sectionsSubsectionsNetworks = $this->SectionSubsectionNetwork->getSectionsSubsectionsNetworksBySectionSubsectionId($sectionSubsectionId);

        $this->set(array(
            'sections_subsections_networks' => $sectionsSubsectionsNetworks
        ));
    }

    private function setVarCommunicationsDistributorsNetworks($communicationId)
    {
        $communicationsDistributorsNetworks = $this->CommunicationDistributorNetwork->getCommunicationsDistributorsNetworksByCommunicationId($communicationId);

        $this->set(array(
            'communications_distributors_networks' => $communicationsDistributorsNetworks
        ));
    }

    private function setVarCommunicationsSectionsDistributorsNetworks($communicationSectionId)
    {
        $communicationsSectionsDistributorsNetworks = $this->CommunicationSectionDistributorNetwork->getCommunicationsSectionsDistributorsNetworksByCommunicationId($communicationSectionId);

        $this->set(array(
            'communications_sections_distributors_networks' => $communicationsSectionsDistributorsNetworks
        ));
    }

    private function setVarSectionsSubsectionsDistributorsNetworks($sectionSubsectionId)
    {
        $sectionsSubsectionsDistributorsNetworks = $this->SectionSubsectionDistributorNetwork->getSectionsSubsectionsDistributorsNetworksBySectionSubsectionId($sectionSubsectionId);

        $this->set(array(
            'sections_subsections_distributors_networks' => $sectionsSubsectionsDistributorsNetworks
        ));
    }

    private function setVarCommunicationsTradingGroups($communicationId)
    {
        $communicationsTradingGroups = $this->CommunicationTradingGroup->getTGByCommunicationId($communicationId);

        $this->set(array(
            'communications_trading_groups' => $communicationsTradingGroups
        ));
    }

    private function setVarCommunicationsSectionsTradingGroups($communicationSectionId)
    {
        $communicationsSectionsTradingGroups = $this->CommunicationSectionTradingGroup->getTGByCommunicationSectionId($communicationSectionId);

        $this->set(array(
            'communications_sections_trading_groups' => $communicationsSectionsTradingGroups
        ));
    }

    private function setVarSectionsSubsectionsTradingGroups($sectionSubsectionId)
    {
        $sectionsSubsectionsTradingGroups = $this->SectionSubsectionTradingGroup->getTGBySectionSubsectionId($sectionSubsectionId);

        $this->set(array(
            'sections_subsections_trading_groups' => $sectionsSubsectionsTradingGroups
        ));
    }

    private function setVarCommunicationsPositions($communicationId)
    {
        $communicationsPositions = $this->CommunicationPosition->getPositionsByCommunicationId($communicationId);

        $this->set(array(
            'communications_positions' => $communicationsPositions
        ));
    }

    private function setVarCommunicationsSectionsPositions($communicationSectionId)
    {
        $communicationsSectionsPositions = $this->CommunicationSectionPosition->getPositionsByCommunicationSectionId($communicationSectionId);

        $this->set(array(
            'communications_sections_positions' => $communicationsSectionsPositions
        ));
    }

    private function setVarSectionsSubsectionsPositions($sectionSubsectionId)
    {
        $sectionsSubsectionsPositions = $this->SectionSubsectionPosition->getPositionsBySectionSubsectionId($sectionSubsectionId);

        $this->set(array(
            'sections_subsections_positions' => $sectionsSubsectionsPositions
        ));
    }

    private function setVarCommunicationsActivities($communicationId)
    {
        $communicationsActivities = $this->CommunicationCustomerActivity->getActivitiesByCommunicationId($communicationId);
        $this->set(array(
            'communications_activities' => $communicationsActivities
        ));
    }

    private function setVarCommunicationsSectionsActivities($communicationSectionId)
    {
        $communicationsSectionsActivities = $this->CommunicationSectionCustomerActivity->getActivitiesByCommunicationSectionId($communicationSectionId);
        $this->set(array(
            'communications_sections_activities' => $communicationsSectionsActivities
        ));
    }

    private function setVarSectionsSubsectionsActivities($sectionSubsectionId)
    {
        $sectionsSubsectionsActivities = $this->SectionSubsectionCustomerActivity->getActivitiesBySectionSubsectionId($sectionSubsectionId);
        $this->set(array(
            'sections_subsections_activities' => $sectionsSubsectionsActivities
        ));
    }

    private function setVarCancel1()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'communications',
                'action' => 'maintenance_communications',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }

    private function setVarCancel2()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'communications',
                'action' => 'maintenance_communications_sections',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }

    private function setVarCancel3()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'communications',
                'action' => 'maintenance_section_subsection',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }

    /**
     * AJAX load communication SectionSubsections.
     */
    public function ajax_load_subsections()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $sectionId = $this->request->data['section_id'];
            $sectionsSubsections = $this->SectionSubsection->getSubsectionByCommunicationSection($sectionId);
            $this->set(array(
                'sections_subsections' => $sectionsSubsections,
            ));

            $this->layout = null;
            $this->render('../Communications/Elements/ajax_load_subsections');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get CommunicactionSection list.
     */
    public function ajax_section_list($communication_section_id, $subsection_id = null)
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $title = !empty($this->request->query['title']) ? $this->request->query['title'] : '';

            if (!empty($this->request->query['section_subsection_id'])) {
                $subsection_id = $this->request->query['section_subsection_id'];
            }

            if ($subsection_id == null) {
                $communications = $this->getCommunicationsSectionSubsectionNullByUser($communication_section_id, $title);
            } else {
                $communications = $this->getCommunicationsSectionSubsectionNotNullByUser($communication_section_id, $subsection_id, $title);
            }

            foreach ($communications as $key => $communication) {
                $tmp = $this->CommunicationFile->findAllByCommunicationId($communication['Communication']['id']);
                if (!empty($tmp)) {
                    $communications[$key]['Communication']['CommunicationFiles'] = $tmp;
                }
            }

            $sectionsSubsections = $this->SectionSubsection->getSubsectionByCommunicationSection($communication_section_id);

            $this->set(array(
                'communications' => $communications,
                'sections_subsections' => $sectionsSubsections,
            ));
            $this->layout = null;
            $this->render('../Communications/Elements/section_list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CommunicationSection view.
     */
    public function section($section_id, $subsection_id = null)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $section = $this->CommunicationSection->findByIdAndAagRegionId($section_id, $aagRegionId);

        if (
            $section &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)
                )
            )
        ) {
            $title = !empty($this->request->query['title']) ? $this->request->query['title'] : '';

            $subsectionsTmp = array_unique(Hash::extract($this->CommunicationSection->getSubsectionsActive($section_id), '{n}.Communication.section_subsection_id'));
            $subsections = $this->SectionSubsection->findAllById($subsectionsTmp);

            if ($subsection_id == null) {
                $communications = $this->getCommunicationsSectionSubsectionNullByUser($section_id, $title);
            } else {
                $communications = $this->getCommunicationsSectionSubsectionNotNullByUser($section_id, $subsection_id, $title);
            }

            foreach ($communications as $key => $communication) {
                $tmp = $this->CommunicationFile->findAllByCommunicationId($communication['Communication']['id']);
                if (!empty($tmp)) {
                    $communications[$key]['Communication']['CommunicationFiles'] = $tmp;
                }
            }
            $sectionsSubsections = $this->SectionSubsection->search_list_region($aagRegionId);

            $this->set(array(
                'section' => $section,
                'title' => $title,
                'subsections' => $subsections,
                'communications' => $communications,
                'sections_subsections' => $sectionsSubsections,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Article searcher.
     */
    public function communication_searcher()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)
            )
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

            $conditions = array();
            if (isset($this->request->query['search']) && $this->request->query['search'] != '') {
                $param = $this->request->query['search'];
                $conditions = array(
                    'OR' => array(
                        'Communication.title LIKE' => '%' . $param . '%',
                        'Communication.subtitle LIKE' => '%' . $param . '%',
                        'Communication.body LIKE' => '%' . $param . '%',
                    )
                );
                $this->request->data['Search'] = $this->request->query;
            }

            $communications = $this->custom_pagination(
                $this->Communication->getCommunicationsSearch(CakeSession::read('Auth.User')),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            $communicationsSection = $this->CommunicationSection->search_list_region($aagRegionId);
            $communicationsSubsection = $this->SectionSubsection->search_list_region($aagRegionId);

            $this->set(array(
                'communications' => $communications,
                'communications_section' => $communicationsSection,
                'communications_subsection' => $communicationsSubsection,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX modal communication.
     */
    public function ajax_modal_communication($communication_id)
    {
        $this->verify_ajax($this->request);

        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $communication = $this->Communication->getCommunicationByIdAndAagRegionId($communication_id, $aagRegionId);

        if (
            $communication &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                )
            )
        ) {
            $communicationFiles = $this->CommunicationFile->getListByCommunicationId($communication_id);
            $communicationSections = $this->CommunicationSection->search_list_region($aagRegionId);
            $sectionsSubsections = $this->SectionSubsection->search_list_region($aagRegionId);

            $this->set(
                array(
                    'communication' => $communication,
                    'communication_files' => $communicationFiles,
                    'communication_sections' => $communicationSections,
                    'sections_subsections' => $sectionsSubsections,
                )
            );

            $this->layout = false;
            $this->render('../Communications/Elements/modal_communication');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function getCommunicationsSectionSubsectionNullByUser($sectionId, $title)
    {
        if (CakeSession::read('Auth.User.garage_id')) {
            $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
            $communications = $this->custom_pagination(
                $this->Communication->_query('getCommunicationsByNetworksAndSection'),
                $this->Communication->_conditionsCommunicationsByNetworksAndSection($networks, $sectionId, $title),
                ConstantsPagination::SIZE_ADVANCED_SEARCH
            );
        } elseif (CakeSession::read('Auth.User.distributor_id')) {
            $distributor = $this->Distributor->findByIdAndAagRegionId(CakeSession::read('Auth.User.distributor_id'), CakeSession::read('Auth.User.aag_region_id'));
            $communications = $this->custom_pagination(
                $this->Communication->_query('getCommunicationsByTradingGroupAndSection'),
                $this->Communication->_conditionsCommunicationsByTradingGroupAndSection($distributor['Distributor']['trading_group_id'], $sectionId, $title),
                ConstantsPagination::SIZE_ADVANCED_SEARCH
            );
        } else {
            $networks = $this->Network->getList();
            $communications = $this->custom_pagination(
                $this->Communication->_query('getCommunicationsByNetworksAndSection'),
                $this->Communication->_conditionsCommunicationsByNetworksAndSection($networks, $sectionId, $title),
                ConstantsPagination::SIZE_ADVANCED_SEARCH
            );
        }

        return $communications;
    }

    private function getCommunicationsSectionSubsectionNotNullByUser($sectionId, $subsectionId, $title)
    {
        if (CakeSession::read('Auth.User.garage_id')) {
            $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
            $communications = $this->custom_pagination(
                $this->Communication->_query('getCommunicationsByNetworksAndSection'),
                $this->Communication->_conditionsCommunicationsByNetworksAndSectionAndSubsection($networks, $sectionId, $subsectionId, $title),
                ConstantsPagination::SIZE_ADVANCED_SEARCH
            );
        } elseif (CakeSession::read('Auth.User.distributor_id')) {
            $distributor = $this->Distributor->findByIdAndAagRegionId(CakeSession::read('Auth.User.distributor_id'), CakeSession::read('Auth.User.aag_region_id'));
            $communications = $this->custom_pagination(
                $this->Communication->_query('getCommunicationsByTradingGroupAndSection'),
                $this->Communication->_conditionsCommunicationsByTradingGroupAndSectionAndSubsection($distributor['Distributor']['trading_group_id'], $sectionId, $subsectionId, $title),
                ConstantsPagination::SIZE_ADVANCED_SEARCH
            );
        } else {
            $networks = $this->Network->getList();
            $communications = $this->custom_pagination(
                $this->Communication->_query('getCommunicationsByNetworksAndSection'),
                $this->Communication->_conditionsCommunicationsByNetworksAndSectionAndSubsection($networks, $sectionId, $subsectionId, $title),
                ConstantsPagination::SIZE_ADVANCED_SEARCH
            );
        }

        return $communications;
    }

    /**
     * AJAX load communicaction SectionSubsection filters.
     */
    public function ajax_load_subsection_filters()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $section_id = $this->request->data['section_id'];
            $section = $this->CommunicationSection->findById($section_id);

            $sections = $this->CommunicationSection->search_list_region($aagRegionId);

            $this->setVarNetworks();
            $sectionsNetworks = $this->CommunicationSectionNetwork->getCommunicationsSectionsNetworksByCommunicationId($section_id);

            $this->setVarDistributorsNetworks($aagRegionId);
            $sectionsDistributorsNetworks = $this->CommunicationSectionDistributorNetwork->getCommunicationsSectionsDistributorsNetworksByCommunicationId($section_id);

            $this->setVarTradingGroups();
            $sectionsTradingGroups = $this->CommunicationSectionTradingGroup->getTGByCommunicationSectionId($section_id);

            $this->setVarCustomersActivities();
            $sectionsCustomersActivities = $this->CommunicationSectionCustomerActivity->getListActivitiesByCommunicationSectionId($section_id);

            $sections_positions = $this->CommunicationSectionPosition->getListPositionsByCommunicationSectionId($section_id);
            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->set(
                array(
                    'sections_networks' => $sectionsNetworks,
                    'sections_distributors_networks' => $sectionsDistributorsNetworks,
                    'sections_trading_groups' => $sectionsTradingGroups,
                    'sections_customers_activities' => $sectionsCustomersActivities,
                    'sections_positions' => $sections_positions,
                    'garage_positions' => $garagePositions,
                    'distributor_positions' => $distributorPositions,
                    'section' => $section,
                    'sections' => $sections,
                )
            );

            $this->layout = null;
            $this->render('../Communications/Elements/ajax_load_subsection_filters');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load Communication filters.
     */
    public function ajax_load_communication_filters()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $subsection_id = $this->request->data['subsection_id'];

            $subsection = $this->SectionSubsection->findById($subsection_id);

            $this->setVarNetworks();
            $subsectionsNetworks = $this->SectionSubsectionNetwork->getSectionsSubsectionsNetworksBySectionSubsectionId($subsection_id);

            $this->setVarDistributorsNetworks($aagRegionId);
            $subsectionsDistributorsNetworks = $this->SectionSubsectionDistributorNetwork->getSectionsSubsectionsDistributorsNetworksBySectionSubsectionId($subsection_id);

            $this->setVarTradingGroups();
            $subsectionsTradingGroups = $this->SectionSubsectionTradingGroup->getTGBySectionSubsectionId($subsection_id);

            $this->setVarCustomersActivities();
            $subsectionsCustomersActivities = $this->SectionSubsectionCustomerActivity->getListActivitiesBySectionSubsectionId($subsection_id);

            $subsectionsPositions = $this->SectionSubsectionPosition->getListPositionsBySectionSubsectionId($subsection_id);
            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->set(
                array(
                    'subsections_networks' => $subsectionsNetworks,
                    'subsections_distributors_networks' => $subsectionsDistributorsNetworks,
                    'subsections_trading_groups' => $subsectionsTradingGroups,
                    'subsections_customers_activities' => $subsectionsCustomersActivities,
                    'subsections_positions' => $subsectionsPositions,
                    'garage_positions' => $garagePositions,
                    'distributor_positions' => $distributorPositions,
                    'subsection' => $subsection,
                )
            );

            $this->layout = null;
            $this->render('../Communications/Elements/ajax_load_communication_filters');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function registerDataSection($section_id)
    {
        if (!CakeSession::read('Auth.User.master_key')) {
            $section = $this->CommunicationSection->findById($section_id);

            $stats = array(
                'user_id' => CakeSession::read('Auth.User.id'),
                'user_name' => CakeSession::read('Auth.User.full_name'),
                'trading_group_id' => !is_null(CakeSession::read('Auth.User.trading_group')) ? CakeSession::read('Auth.User.trading_group') : null,
                'network_id' => !is_null(CakeSession::read('Auth.User.trading_group')) ? null : CakeSession::read('Auth.User.current_network'),
                'garage_id' => CakeSession::read('Auth.User.garage_id'),
                'distributor_id' => CakeSession::read('Auth.User.distributor_id'),
                'section_id' => $section_id,
                'section_name' => $section['CommunicationSection']['name' . __s()],
                'article_id' => null,
                'article_name' => null,
                'date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'device' => CakeSession::read('Auth.User.device'),
                'ip' => $_SERVER['REMOTE_ADDR']
            );

            $this->UserStatistic->create();
            $this->UserStatistic->save($stats);
        }
    }

    private function registerDataArticle($article_id)
    {
        if (!CakeSession::read('Auth.User.master_key')) {
            $article = $this->Communication->findById($article_id);

            $stats = array(
                'user_id' => CakeSession::read('Auth.User.id'),
                'user_name' => CakeSession::read('Auth.User.full_name'),
                'trading_group_id' => !is_null(CakeSession::read('Auth.User.trading_group')) ? CakeSession::read('Auth.User.trading_group') : null,
                'network_id' => !is_null(CakeSession::read('Auth.User.trading_group')) ? null : CakeSession::read('Auth.User.current_network'),
                'garage_id' => CakeSession::read('Auth.User.garage_id'),
                'distributor_id' => CakeSession::read('Auth.User.distributor_id'),
                'section_id' => null,
                'section_name' => null,
                'article_id' => $article_id,
                'article_name' => $article['Communication']['title'],
                'date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'device' => CakeSession::read('Auth.User.device'),
                'ip' => $_SERVER['REMOTE_ADDR']
            );

            $this->UserStatistic->create();
            $this->UserStatistic->save($stats);
        }
    }
}
