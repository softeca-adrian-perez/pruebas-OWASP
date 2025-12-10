<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class TrainingsTrainersController extends AppController
{
    public $uses = array(
        'TrainingTrainer',
        'TrainingProvider',
    );

    /**
     * TrainingTrainers home page.
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

            $trainingsTrainers = $this->custom_pagination(
                $this->TrainingTrainer->_query('home'),
                $this->TrainingTrainer->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingTrainer',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'trainings_trainers' => $trainingsTrainers,
                'trainings_providers' => $this->TrainingProvider->searchList(),
                'training_trainer_list' => $this->TrainingTrainer->searchList(),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TrainingTrainer.
     */
    public function add()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            if (!$this->request->is('get')) {
                if ($this->TrainingTrainer->add($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'trainings_trainers',
                            'action' => 'edit',
                            $this->TrainingTrainer->getLastInsertID()
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
     * Edit TRainingTrainer.
     */
    public function edit($training_trainer_id)
    {
        $trainer = $this->TrainingTrainer->findById($training_trainer_id);
        if (
            $trainer &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {

            if ($this->request->is('get')) {
                $this->request->data = $trainer;
            } else {
                $this->request->data['User']['id'] = $training_trainer_id;
                if ($this->TrainingTrainer->edit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'training_trainer_id' => $training_trainer_id,
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
     * AJAX delete TrainingTrainer.
     */
    public function ajax_delete($training_trainer_id)
    {
        $this->verify_ajax($this->request);
        $trainer = $this->TrainingTrainer->findById($training_trainer_id, 'id');

        if (
            $trainer &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            return $this->TrainingTrainer->delete($training_trainer_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * TrainingTrainer Excel generation.
     */
    public function training_trainers_excel()
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

            $trainers = $this->TrainingTrainer->find(
                'all',
                array(
                    'conditions' => $this->TrainingTrainer->conditions($searcher),
                    'fields' => array(
                        'all',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'TrainingProvider',
                            'table' => 'trainings_providers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingProvider.id = TrainingTrainer.training_provider_id',
                            ),
                        )
                    ),
                    'fields' => array(
                        'TrainingTrainer.*',
                        'TrainingProvider.*'
                    ),
                    'order' => 'TrainingTrainer.name'
                )
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'trainers' => $trainers,
                'config' => $config,
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/TrainingsTrainers/Elements/export_excel_trainers');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}
