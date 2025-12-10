<?php
class DashboardController extends AppController
{
    public $uses = array(
        'Task',
        'TaskGarage',
        'Appointment',
        'Contact',
        'ContactContactList',
        'ContactRegion',
        'Garage',
        'GarageContactBdm',
        'Distributor',
        'Region',
        'User',
        'GarageSale',
        'SaleFamily',
        'SaleDePerRegion',
        'SaleDePerBDM',
        'SaleDeFlopFamilyAbsolute',
        'SaleDeFlopFamilyPercentage',
        'SaleDeFlopBdmAbsolute',
        'SaleDeFlopFamilyBdmAbsolute',
        'SaleDeFlopFamilyPerBdmPercentage',
        'SaleDeFlopCustomerPerBdmAbsolute',
        'SaleDeFlopCustomerPerBdmPercentage',
        'SaleDeFlopBdmPerRegionAbsolute',
    );

    /**
     * Dashboard home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $url = Router::url(array(
                'controller' => 'dashboard',
                'action' => 'home'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $user = CakeSession::read('Auth.User.id');
            $user_bd = $this->User->findById($user);
            $contact_id = $user_bd['User']['contact_id'];

            $assigned_tasks = $this->Task->getAssignedToMeOpenTask($user);
            $assigned_tasks_deadline_null = $this->Task->getAssignedToMeOpenTaskDeadlineNull($user);
            $assigned_open_tasks = $this->Task->countAssignedToMeOpenTask($user);

            //2-Task assigned to my group
            $assigned_group_tasks = $this->ContactContactList->getAssignedToMyGroupOpenTask($contact_id);
            $assigned_group_tasks_deadline_null = $this->ContactContactList->getAssignedToMyGroupOpenTaskDeadlineNull($contact_id);
            $assigned_group_open_tasks = $this->ContactContactList->countAssignedToMyGroupOpenTask($contact_id);
            $assigned_group_open_tasks = ($assigned_group_open_tasks != false) ? $assigned_group_open_tasks : 0;

            //Task created by me
            $created_tasks = $this->Task->getCreatedByMeOpenTask($user);
            $created_open_tasks = $this->Task->countCreatedByMeOpenTask($user);
            $created_open_tasks = ($created_open_tasks != false) ? $created_open_tasks : 0;

            $users = array();
            if (!empty($created_tasks) || !empty($assigned_tasks)) {
                $users = $this->User->listCompleteNameRegion($aagRegionId);
            }

            //3-Task assigned to my customers
            $assigned_customers_tasks_tmp = $this->GarageContactBdm->getAssignedToMyCustomersOpenTask($contact_id);
            $assigned_customers_tasks_deadline_null_tmp = $this->GarageContactBdm->getAssignedToMyCustomersOpenTaskDeadlineNull($contact_id);
            $assigned_customers_open_tasks = $this->GarageContactBdm->countAssignedToMyCustomersOpenTask($contact_id);
            $assigned_customers_open_tasks = ($assigned_customers_open_tasks != false) ? $assigned_customers_open_tasks : 0;

            //group the tasks 3 that belong to the same garage
            $assigned_customers_tasks = array();
            foreach ($assigned_customers_tasks_tmp as $task) {
                if (in_array($task['Task']['id'], array_keys($assigned_customers_tasks))) {
                    $assigned_customers_tasks[$task['Task']['id']]['Garage'][] = $task['Garage']['name'];
                } else {
                    $assigned_customers_tasks[$task['Task']['id']] = $task;
                }
            }
            $assigned_customers_tasks_deadline_null = array();
            foreach ($assigned_customers_tasks_deadline_null_tmp as $task) {
                if (in_array($task['Task']['id'], array_keys($assigned_customers_tasks))) {
                    $assigned_customers_tasks_deadline_null[$task['Task']['id']]['Garage'][] = $task['Garage']['name'];
                } else {
                    $assigned_customers_tasks_deadline_null[$task['Task']['id']] = $task;
                }
            }

            $visits = $this->Appointment->getNextVisits($user);
            $visits_requires = $this->Appointment->getNextVisitsRequires($user);
            $visits_without_feedback = $this->Appointment->getVisitsWithoutFeedbackAndStatusPending($user);

            $garages_last_visited = $this->Garage->getLastVisits();
            $distributors_last_visited = $this->Distributor->getLastVisits();

            foreach ($visits as $key => $visit) {
                if ($visit['Appointment']['visit_contact_id']) {
                    $visits[$key]['Contact'] = $this->Contact->getContactsNameByIdContact($visit['Appointment']['visit_contact_id']);
                }
            }

            foreach ($visits_requires as $key => $visit_require) {
                if ($visit_require['Appointment']['visit_contact_id']) {
                    $visits_requires[$key]['Contact'] = $this->Contact->getContactsNameByIdContact($visit_require['Appointment']['visit_contact_id']);
                }
            }

            foreach ($visits_without_feedback as $key => $visit_without_feedback) {
                if ($visit_without_feedback['Appointment']['visit_contact_id']) {
                    $visits_without_feedback[$key]['Contact'] = $this->Contact->getContactsNameByIdContact($visit_without_feedback['Appointment']['visit_contact_id']);
                }
            }

            $this->set(
                array(
                    'assigned_tasks' => $assigned_tasks,
                    'assigned_tasks_deadline_null' => $assigned_tasks_deadline_null,
                    'assigned_group_tasks' => $assigned_group_tasks,
                    'assigned_group_tasks_deadline_null' => $assigned_group_tasks_deadline_null,
                    'assigned_customers_tasks' => $assigned_customers_tasks,
                    'assigned_customers_tasks_deadline_null' => $assigned_customers_tasks_deadline_null,
                    'created_tasks' => $created_tasks,
                    'visits' => $visits,
                    'visits_requires' => $visits_requires,
                    'visits_without_feedback' => $visits_without_feedback,
                    'user' => $user,
                    'users' => $users,
                    'user_bd' => $user_bd,
                    'assigned_open_tasks' => $assigned_open_tasks,
                    'assigned_customers_open_tasks' => $assigned_customers_open_tasks,
                    'assigned_group_open_tasks' => $assigned_group_open_tasks,
                    'created_open_tasks' => $created_open_tasks,
                    'garages_last_visited' => $garages_last_visited,
                    'distributors_last_visited' => $distributors_last_visited,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get customer sales.
     */
    public function ajax_customer_sales()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $data_post = $this->request->data;
            if (!isset($data_post['month'])) {
                $data_post['month'] = date('n');
            }
            if (!isset($data_post['year'])) {
                $data_post['year'] = date('Y');
            }
            $expect_increase = ConstantsIncrease::DEFAULT_INCREASE;
            $current_year = $data_post['year'];
            $previous_year = $current_year - 1;
            $current_month = $data_post['month'];
            if ($current_month == 1) {
                $previous_month = 12;
            } else {
                $previous_month = $current_month - 1;
            }

            $this->GarageSale->virtualFields['IAM_month_previous_year'] = 'SUM(case when GarageSale.year = ' . $previous_year . ' then GarageSaleFamily.sales_IAM else 0 end) * ' . $expect_increase;
            $this->GarageSale->virtualFields['IAM_month_current_year'] = 'SUM(case when GarageSale.year = ' . $current_year . ' then GarageSaleFamily.sales_IAM else 0 end)';
            $this->GarageSale->virtualFields['OE_month_previous_year'] = 'SUM(case when GarageSale.year = ' . $previous_year . ' then GarageSaleFamily.sales_OE else 0 end) * ' . $expect_increase;
            $this->GarageSale->virtualFields['OE_month_current_year'] = 'SUM(case when GarageSale.year = ' . $current_year . ' then GarageSaleFamily.sales_OE else 0 end)';
            if ($current_month == 0) {
                $conditions = array(
                    'OR' => array(
                        array(
                            'GarageSale.year' => $previous_year,
                        ),
                        array(
                            'GarageSale.year' => $current_year,
                        )
                    )
                );
            } else {
                $conditions = array(
                    'OR' => array(
                        array(
                            'GarageSale.year' => $previous_year,
                            'GarageSale.month' => $previous_month,
                        ),
                        array(
                            'GarageSale.year' => $current_year,
                            'GarageSale.month' => $previous_month,
                        )
                    )
                );
            }
            if (isset($data_post['search']) && !empty($data_post['search'])) {
                $conditions['Garage.name LIKE']  = '%' . $data_post['search'] . '%';
            }

            $tmp = $this->GarageSale->_query('CustomerSales');
            $tmp['group'] = array(
                'Garage.id',
            );
            $tmp['fields'] = array(
                'Garage.id',
                'Garage.name',
                'IAM_month_previous_year',
                'IAM_month_current_year',
                'OE_month_previous_year',
                'OE_month_current_year',
            );
            $garage_customer_sales = $this->custom_pagination(
                $tmp,
                $conditions,
                5,
                'GarageSale'
            );

            $this->set(
                array(
                    'garage_customer_sales' => $garage_customer_sales,
                    'months' => $this->months(),
                    'selected_month' => $data_post['month'],
                    'selected_year' => $data_post['year']
                )
            );

            $this->layout = false;
            $this->render('../Dashboard/Elements/ajax_customer_sales');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function months()
    {
        $months = array(
            __t('CRM.See_all'),
            __t('General.January'),
            __t('General.February'),
            __t('General.March'),
            __t('General.April'),
            __t('General.May'),
            __t('General.June'),
            __t('General.July'),
            __t('General.August'),
            __t('General.September'),
            __t('General.October'),
            __t('General.November'),
            __t('General.December'),
        );

        return $months;
    }
}
