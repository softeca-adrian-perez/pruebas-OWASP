<?php
class DistributorsLabelsController extends AppController
{
    public $uses = array(
        'DistributorLabel',
        'LabelType',
        'LogChange',
    );

    /**
     * Create DistributorLabel.
     */
    public function add($distributor_id)
    {
        $config = CakeSession::read('Auth.User.Config');

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $config[ConstantsConfig::LABEL]
        ) {
            $labelsTypes = $this->LabelType->find('list');
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_label',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $labelBd = $this->DistributorLabel->add_label($this->request->data, $distributor_id);
                if ($labelBd) {
                    unset($labelBd['DistributorLabel']['id']);
                    unset($labelBd['DistributorLabel']['distributor_id']);
                    $this->LogChange->get_params_create_log_add(
                        $labelBd['DistributorLabel'],
                        $this->DistributorLabel->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                    $this->redirect(
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_label',
                            $distributor_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'labels_types' => $labelsTypes
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorLabel.
     */
    public function edit($label_id, $distributor_id)
    {
        $config = CakeSession::read('Auth.User.Config');

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');
        $distributorLabel = $this->DistributorLabel->findById($label_id);

        if (
            $distributor && $distributorLabel &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $config[ConstantsConfig::LABEL]
        ) {
            $labelsTypes = $this->LabelType->find('list');
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_label',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $oldData = $distributorLabel;
                $labelBd = $this->DistributorLabel->edit_label($this->request->data, $distributor_id);
                if ($labelBd) {
                    if ($oldData != $labelBd) {
                        unset($oldData['DistributorService']['id']);
                        unset($oldData['DistributorService']['distributor_id']);
                        unset($labelBd['DistributorService']['id']);
                        unset($labelBd['DistributorService']['distributor_id']);
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['DistributorLabel'],
                            $labelBd['DistributorLabel'],
                            $this->DistributorLabel->table,
                            $this->Session->read('Auth'),
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_label',
                            $distributor_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $distributorLabel['DistributorLabel']['start_date'] = Fecha::toFormatoVistaFecha($distributorLabel['DistributorLabel']['start_date']);
                $distributorLabel['DistributorLabel']['end_date'] = Fecha::toFormatoVistaFecha($distributorLabel['DistributorLabel']['end_date']);
                $this->request->data = $distributorLabel;
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'labels_types' => $labelsTypes,
                'label' => $distributorLabel
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete DistributorLabel.
     */
    public function delete($distributor_label_id, $distributor_id)
    {
        $config = CakeSession::read('Auth.User.Config');

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');
        $distributorLabel = $this->DistributorLabel->findById($distributor_label_id);

        if (
            $distributor && $distributorLabel &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $config[ConstantsConfig::LABEL]
        ) {
            if ($this->DistributorLabel->delete($distributor_label_id)) {
                unset($distributorLabel['DistributorLabel']['id']);
                unset($distributorLabel['DistributorLabel']['distributor_id']);
                $this->LogChange->get_params_create_log_delete(
                    $distributorLabel['DistributorLabel'],
                    $this->DistributorLabel->table,
                    $this->Session->read('Auth'),
                    $distributor_id,
                    ConstantsLogType::DISTRIBUTOR
                );
            }

            $this->redirect(
                array(
                    'controller' => 'distributors',
                    'action' => 'add_label',
                    $distributor_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
