<?php
class TrainingsCreditsController extends AppController
{
    public $uses = array(
        'TrainingCredit',
        'Country',
    );

    /**
     * Training
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
            $trainingCreditExists = $this->TrainingCredit->checkBD();

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                $saveData = null;
                if ($trainingCreditExists != null) {
                    $saveData = $this->TrainingCredit->edit($this->request->data);
                } else {
                    $saveData = $this->TrainingCredit->add($this->request->data);
                }
                if ($saveData) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                ),
            );

            $this->set(
                array(
                    'cancel_action' => $cancelAction,
                    'training_credit_exists' => $trainingCreditExists,
                    'country' => $this->Country->findById(ConstantsCountries::UNITED_KINGDOM),
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
