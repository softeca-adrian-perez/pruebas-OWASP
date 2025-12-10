<?php

class AlertsController extends AppController
{
    /**
     * Alerts home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_ALERT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::ALERTS)
            )
        ) {
            $types = $this->Alert->AlertType->search_list();

            $read_types = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $searcher = $this->request->query;

            $this->request->data['Search'] = $searcher;
            $conditions = $this->Alert->conditions($searcher);
            $conditions[] = array('user_id' => CakeSession::read('Auth.User.id'));

            $alerts = $this->custom_pagination(
                $this->Alert->_query('search'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Alert',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'alerts' => $alerts,
                'types' => $types,
                'read_types' => $read_types
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX mark alert as read.
     */
    public function ajax_check_alert($alert_id)
    {
        $this->verify_ajax($this->request);

        $alertBd = $this->Alert->findByIdAndUserId($alert_id, CakeSession::read('Auth.User.id'));
        if (
            $alertBd &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_ALERT) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::ALERTS)
                )
            )
        ) {
            $checked = $alertBd ? true : false;

            $alert = array(
                'Alert' => array(
                    'id' => $alert_id,
                    'read' => $checked
                )
            );
            if ($checked) {
                $this->Alert->edit_alert($alert);
            } else {
                echo __t('Alert.Cannot_checked');
            }
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX mark alert as unread.
     */
    public function ajax_uncheck_alert($alert_id)
    {
        $this->verify_ajax($this->request);

        $alertBd = $this->Alert->findByIdAndUserId($alert_id, CakeSession::read('Auth.User.id'));
        if (
            $alertBd &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_ALERT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::ALERTS)
            )
        ) {
            $checked = $alertBd ? true : false;

            $alert = array(
                'Alert' => array(
                    'id' => $alert_id,
                    'read' => !$checked
                )
            );
            if ($checked) {
                $this->Alert->edit_alert($alert);
            } else {
                echo __t('Alert.Cannot_checked');
            }
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}
