<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class TrainingsProvidersController extends AppController
{
    public $uses = array(
        'TrainingProvider',
        'TrainingTrainer',
        'TrainingPlannedCourse',
    );

    /**
     * TrainingProviders home page.
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
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $trainingsProviders = $this->custom_pagination(
                $this->TrainingProvider->_query('home'),
                $this->TrainingProvider->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingProvider',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'trainings_providers' => $trainingsProviders,
                'training_provider_list' => $this->TrainingProvider->searchList(),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TrainingProvider.
     */
    public function add()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            if (!$this->request->is('get')) {
                if ($this->TrainingProvider->add($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'trainings_providers',
                            'action' => 'edit',
                            $this->TrainingProvider->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }
            $this->setVarForm();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit TrainingProvider.
     */
    public function edit($training_provider_id)
    {
        $provider = $this->TrainingProvider->findById($training_provider_id);
        if (
            $provider &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {

            if ($this->request->is('get')) {
                $this->request->data = $provider;
            } else {
                $this->request->data['User']['id'] = $training_provider_id;
                if ($this->TrainingProvider->edit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'training_provider_id' => $training_provider_id,
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
                'controller' => 'trainings_providers',
                'action' => 'home',
            ),
        );
        $this->set(array(
            'cancel_action' => $cancelAction,
            'active' => $active,
        ));
    }

    /**
     * AJAX delete TrainingProvider.
     */
    public function ajax_delete($training_provider_id)
    {
        $this->verify_ajax($this->request);
        $provider = $this->TrainingProvider->findById($training_provider_id, 'id');

        if (
            $provider &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            if ($this->TrainingTrainer->findAllByTrainingProviderId($training_provider_id)) {
                return 'assoc';
            } else {
                return $this->TrainingProvider->delete($training_provider_id);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete TrainingTrainer and TrainingProviders.
     */
    public function ajax_delete_assoc($training_provider_id)
    {
        $this->verify_ajax($this->request);
        $provider = $this->TrainingProvider->findById($training_provider_id, 'id');

        if (
            $provider &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            foreach ($this->TrainingTrainer->findAllByTrainingProviderId($training_provider_id) as $training_trainer) {
                $this->TrainingTrainer->delete($training_trainer['TrainingTrainer']['id']);
            }
            return $this->TrainingProvider->delete($training_provider_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * TrainingProviders Excel generation.
     */
    public function training_providers_excel()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->TrainingProvider->conditions($searcher);
            $options = array(
                'conditions' => $conditions,
            );
            $providers = $this->TrainingProvider->find('all', $options);

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'providers' => $providers,
                'config' => $config,
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/TrainingsProviders/Elements/export_excel_providers');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}
