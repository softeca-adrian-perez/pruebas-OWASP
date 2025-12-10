<?php

class AppSchema extends CakeSchema
{

    public function before($event = array())
    {
        return true;
    }

    public function after($event = array())
    {
        if (isset($event['create']) && $event['create'] == 'users_statistics') { /* The last of this file */
            $this->_crearForeignKeys($this->_getForeignKeys());
            $this->_editarFormatFields($this->_getAlterFormatFields());
        }
    }

    private function _getForeignKeys()
    {
        return array(
            'emails_daily' => array(
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'users' => array(
                'role_id' => array(
                    'reference_table' => 'roles',
                ),
                'language_id' => array(
                    'reference_table' => 'languages',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                )
            ),
            'users_recover_passwords' => array(
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'positions' => array(
                'role_id' => array(
                    'reference_table' => 'roles',
                ),
            ),
            'permissions' => array(
                'grouping_permission_id' => array(
                    'reference_table' => 'groupings_permissions',
                ),
                'position_config_type_id' => array(
                    'reference_table' => 'positions_config_types',
                ),
            ),
            'groups_permissions_permissions' => array(
                'group_permission_id' => array(
                    'reference_table' => 'groups_permissions',
                ),
                'permission_id' => array(
                    'reference_table' => 'permissions',
                ),
            ),
            'groups_permissions_users' => array(
                'group_permission_id' => array(
                    'reference_table' => 'groups_permissions',
                ),
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'groups_permissions_roles' => array(
                'group_permission_id' => array(
                    'reference_table' => 'groups_permissions',
                ),
                'role_id' => array(
                    'reference_table' => 'roles',
                ),
            ),
            'permissions_roles' => array(
                'permission_id' => array(
                    'reference_table' => 'permissions',
                ),
                'role_id' => array(
                    'reference_table' => 'roles',
                ),
            ),
            'permissions_users' => array(
                'permission_id' => array(
                    'reference_table' => 'permissions',
                ),
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'users_permissions_exception' => array(
                'permission_id' => array(
                    'reference_table' => 'permissions',
                ),
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'trading_groups_networks' => array(
                'network_id' => array(
                    'reference_table' => 'networks',
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups',
                ),
            ),
            'provinces' => array(
                'country_id' => array(
                    'reference_table' => 'countries',
                ),
            ),
            'countries' => array(
                'aag_region_id' => array(
                    'reference_table' => 'aag_regions'
                ),
            ),
            'garages' => array(
                'province_id' => array(
                    'reference_table' => 'provinces',
                ),
                'user_id' => array(
                    'reference_table' => 'users',
                ),
                'sales_area_id' => array(
                    'reference_table' => 'regions',
                ),
                'turnover_id' => array(
                    'reference_table' => 'turnovers',
                ),
                'insurance_agreement_id' => array(
                    'reference_table' => 'insurance_agreements',
                ),
            ),
            'garages_courtesy_car_types' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'courtesy_car_type_id' => array(
                    'reference_table' => 'courtesy_car_types',
                ),
            ),
            'garages_networks' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'network_id' => array(
                    'reference_table' => 'networks',
                ),
                // 'network_contract_type_id' => array(
                //     'reference_table' => 'networks_contract_types',
                // ),
                'supplier_id' => array(
                    'reference_table' => 'suppliers',
                ),
                'reason_leaving_id' => array(
                    'reference_table' => 'leaving_reason_types',
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups',
                ),
            ),
            'garages_services' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'service_id' => array(
                    'reference_table' => 'services',
                ),
            ),
            'garages_vehicle_types' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'vehicle_type_id' => array(
                    'reference_table' => 'vehicle_types',
                ),
            ),
            'garages_vehicles' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'vehicle_id' => array(
                    'reference_table' => 'vehicles',
                ),
            ),
            'garages_files' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
            ),
            'garages_images' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
            ),
            'distributors_images' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
            ),
            'garages_software' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'software_id' => array(
                    'reference_table' => 'software',
                ),
                'software_type_id' => array(
                    'reference_table' => 'software_types',
                ),
                'supplier_id' => array(
                    'reference_table' => 'suppliers',
                ),
                'software_manufacture_id' => array(
                    'reference_table' => 'software_manufactures',
                ),
                'billing_schedule_id' => array(
                    'reference_table' => 'billings_schedules'
                ),
            ),
            'garages_comments' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'distributors' => array(
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups',
                ),
                // 'distributor_id' => array(
                //     'reference_table' => 'distributors',
                // ),
                'province_id' => array(
                    'reference_table' => 'provinces',
                ),
                // 'association_id' => array(
                //     'reference_table' => 'associations',
                // ),
                'user_id' => array(
                    'reference_table' => 'users',
                ),
                'sales_area_id' => array(
                    'reference_table' => 'regions',
                ),
                'distributor_type_id' => array(
                    'reference_table' => 'distributors_types',
                ),
                'association_type_id' => array(
                    'reference_table' => 'associations_types',
                ),
            ),
            'distributors_distributors_activities' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'distributor_activity_id' => array(
                    'reference_table' => 'distributors_activities_primary',
                ),
            ),
            'distributors_distributors_networks' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'network_id' => array(
                    'reference_table' => 'distributors_networks',
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups',
                ),
                'reason_leaving_id' => array(
                    'reference_table' => 'leaving_reason_types',
                ),
            ),
            'distributors_comments' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'distributors_contacts_bdm' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'distributors_contacts_staff' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'distributors_contacts_general_branch_manager' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'distributors_software' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'software_id' => array(
                    'reference_table' => 'software'
                ),
                'software_type_id' => array(
                    'reference_table' => 'software_types'
                ),
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                ),
                'software_manufacture_id' => array(
                    'reference_table' => 'software_manufactures'
                ),
            ),
            'garages_websites' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'website_id' => array(
                    'reference_table' => 'websites',
                ),
            ),
            'garages_distributors' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
            ),
            'garages_contacts_lists' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'contact_list_id' => array(
                    'reference_table' => 'contacts_lists',
                ),
            ),
            'garages_contacts_bdm' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'garages_contacts_staff' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'garages_contacts_general_branch_manager' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts',
                ),
            ),
            'contacts' => array(
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
                'title_id' => array(
                    'reference_table' => 'contacts_titles'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'logistic_center_id' => array(
                    'reference_table' => 'logistic_centers'
                ),
                'aag_region_id' => array(
                    'reference_table' => 'aag_regions'
                ),
            ),
            'logs_fields' => array(
                'table_id' => array(
                    'reference_table' => 'logs_tables'
                )
            ),
            'logs_changes' => array(
                'table_id' => array(
                    'reference_table' => 'logs_tables'
                ),
                'field_id' => array(
                    'reference_table' => 'logs_fields'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'group_permission_id' => array(
                    'reference_table' => 'groups_permissions'
                ),
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
            ),
            'appointments_contacts' => array(
                'appointment_id' => array(
                    'reference_table' => 'appointments'
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
            ),
            'contacts_contacts_lists' => array(
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
                'contact_list_id' => array(
                    'reference_table' => 'contacts_lists'
                ),
            ),
            'appointments_contacts_lists' => array(
                'appointment_id' => array(
                    'reference_table' => 'appointments'
                ),
                'contact_list_id' => array(
                    'reference_table' => 'contacts_lists'
                ),
            ),
            'appointments_topics' => array(
                'appointment_id' => array(
                    'reference_table' => 'appointments'
                ),
                'topic_id' => array(
                    'reference_table' => 'debrief_topics'
                )
            ),
            'appointments' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'appointment_feeling_id' => array(
                    'reference_table' => 'appointments_feelings'
                ),
                'appointment_status_id' => array(
                    'reference_table' => 'appointments_status'
                ),
                'appointment_type_id' => array(
                    'reference_table' => 'appointments_types'
                ),
                'feedback_user_modification' => array(
                    'reference_table' => 'users'
                ),
                'user_creation_id' => array(
                    'reference_table' => 'users'
                ),
                'user_assigned_id' => array(
                    'reference_table' => 'users'
                ),
                'visit_contact_id' => array(
                    'reference_table' => 'contacts'
                )
            ),
            'tasks' => array(
                'appointment_id' => array(
                    'reference_table' => 'appointments'
                ),
                'user_assigned_id' => array(
                    'reference_table' => 'users'
                ),
                'user_creation_id' => array(
                    'reference_table' => 'users'
                ),
                'task_status_id' => array(
                    'reference_table' => 'tasks_status'
                ),
            ),
            'tasks_contacts_lists' => array(
                'task_id' => array(
                    'reference_table' => 'tasks'
                ),
                'contact_list_id' => array(
                    'reference_table' => 'contacts_lists'
                ),
            ),
            'tasks_users' => array(
                'user_id' => array(
                    'reference_table' => 'users'
                ),
                'task_id' => array(
                    'reference_table' => 'tasks'
                ),
            ),
            'tasks_files' => array(
                'task_id' => array(
                    'reference_table' => 'tasks'
                ),
            ),
            'alerts' => array(
                'alert_type_id' => array(
                    'reference_table' => 'alerts_types'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'appointments_files' => array(
                'appointment_id' => array(
                    'reference_table' => 'appointments'
                ),
            ),
            'appointments_comments' => array(
                'appointment_id' => array(
                    'reference_table' => 'appointments',
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                )
            ),
            'users_images' => array(
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'users_reassignments' => array(
                'user_id_origin' => array(
                    'reference_table' => 'users',
                ),
                'user_id_destination' => array(
                    'reference_table' => 'users',
                ),
            ),
            'users_preferences' => array(
                'user_id' => array(
                    'reference_table' => 'users',
                ),
            ),
            'garages_routes' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'route_id' => array(
                    'reference_table' => 'routes'
                )
            ),
            'distributors_routes' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors',
                ),
                'route_id' => array(
                    'reference_table' => 'routes'
                )
            ),
            'routes' => array(
                'user_creation_id' => array(
                    'reference_table' => 'users'
                ),
                'user_assigned_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'postcode_provinces' => array(
                'province_id' => array(
                    'reference_table' => 'provinces'
                ),
            ),
            'tutorials' => array(
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'communications' => array(
                'communication_section_id' => array(
                    'reference_table' => 'communications_sections'
                ),
                'section_subsection_id' => array(
                    'reference_table' => 'sections_subsections'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'sections_subsections' => array(
                'communication_section_id' => array(
                    'reference_table' => 'communications_sections'
                ),
            ),
            'communications_files' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
            ),
            'communications_networks' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'communications_trading_groups' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
            ),
            'contacts_lists' => array(
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'garages_distributors_shortcuts' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'garages_specialist_makes' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'vehicle_id' => array(
                    'reference_table' => 'vehicles'
                ),
            ),
            'shortcuts' => array(
                'shortcut_type_id' => array(
                    'reference_table' => 'shortcuts_types'
                ),
            ),
            'shortcuts_networks' => array(
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'shortcuts_trading_groups' => array(
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
            ),
            'shortcuts_roles' => array(
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'role_id' => array(
                    'reference_table' => 'roles'
                )
            ),
            'tutorials_roles' => array(
                'tutorial_id' => array(
                    'reference_table' => 'tutorials'
                ),
                'role_id' => array(
                    'reference_table' => 'roles'
                )
            ),
            'suppliers_files' => array(
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                ),
                'supplier_category_id' => array(
                    'reference_table' => 'suppliers_categories'
                )
            ),
            'suppliers_images' => array(
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                )
            ),
            'suppliers_networks' => array(
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'suppliers_trading_groups' => array(
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
            ),
            'brands' => array(
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                )
            ),
            'brands_images' => array(
                'brand_id' => array(
                    'reference_table' => 'brands'
                )
            ),
            'products' => array(
                'brand_id' => array(
                    'reference_table' => 'brands'
                )
            ),
            'products_images' => array(
                'product_id' => array(
                    'reference_table' => 'products'
                )
            ),
            'debrief_tasks' => array(
                'contact_list_id' => array(
                    'reference_table' => 'contacts'
                ),
                'user_assigned_id' => array(
                    'reference_table' => 'contacts'
                ),
            ),
            'debrief_tasks_garages' => array(
                'debrief_task_id' => array(
                    'reference_table' => 'debrief_tasks'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
            ),
            'debrief_tasks_distributors' => array(
                'debrief_task_id' => array(
                    'reference_table' => 'debrief_tasks'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
            ),
            'searches_distributors' => array(
                'search_id' => array(
                    'reference_table' => 'users_searches'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
            ),
            'searches_networks' => array(
                'search_id' => array(
                    'reference_table' => 'users_searches'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'searches_contacts' => array(
                'search_id' => array(
                    'reference_table' => 'users_searches'
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
            ),
            'users_searches' => array(
                'user_id' => array(
                    'reference_table' => 'users'
                ),
                'route_id' => array(
                    'reference_table' => 'routes'
                ),
                'network_status_id' => array(
                    'reference_table' => 'networks_statuses'
                ),
            ),
            'tasks_garages' => array(
                'task_id' => array(
                    'reference_table' => 'tasks'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
            ),
            'tasks_distributors' => array(
                'task_id' => array(
                    'reference_table' => 'tasks'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
            ),
            'contacts_regions' => array(
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
                'region_id' => array(
                    'reference_table' => 'regions'
                ),
            ),
            'garages_equipments' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'equipment_id' => array(
                    'reference_table' => 'equipments'
                ),
                'equipment_type_id' => array(
                    'reference_table' => 'equipments_types'
                ),
                'supplier_id' => array(
                    'reference_table' => 'suppliers'
                ),
                'brand_id' => array(
                    'reference_table' => 'brands'
                ),
                'billing_schedule_id' => array(
                    'reference_table' => 'billings_schedules'
                ),
            ),
            'garages_brands' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'brand_id' => array(
                    'reference_table' => 'brands'
                ),
            ),
            'garages_employees' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'employee_type_id' => array(
                    'reference_table' => 'employee_types'
                ),
            ),
            'garages_campaign' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'garage_campaign_id' => array(
                    'reference_table' => 'campaign_entries'
                ),
            ),
            'distributors_labels' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'label_type_id' => array(
                    'reference_table' => 'labels_types'
                ),
            ),
            'garages_customers_activities' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'customer_activity_id' => array(
                    'reference_table' => 'customers_activities'
                ),
            ),
            'networks_contacts_bdm' => array(
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
            ),
            'distributors_networks_contacts_bdm' => array(
                'distributor_network_id' => array(
                    'reference_table' => 'distributors_networks'
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
            ),
            'distributors_services' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'service_type_id' => array(
                    'reference_table' => 'services_types'
                ),
            ),
            'distributors_contracts' => array(
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
                'leaving_reason_id' => array(
                    'reference_table' => 'leaving_reason_types'
                )
            ),
            'distributors_customer_activities' => array(
                'customer_activity_id' => array(
                    'reference_table' => 'customers_activities'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
            ),
            // 'distributors_customer_activities_workshops' => array(
            //     'distributor_customer_activity_id' => array(
            //         'reference_table' => 'distributors_customer_activities'
            //     ),
            //     'workshop_activity_id' => array(
            //         'reference_table' => 'workshop_activities'
            //     ),
            // ),
            'config' => array(
                'section_id' => array(
                    'reference_table' => 'config_sections'
                ),
            ),
            'positions_config' => array(
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
                'position_config_type_id' => array(
                    'reference_table' => 'positions_config_types'
                ),
                'group_permission_id' => array(
                    'reference_table' => 'groups_permissions'
                ),
            ),
            'positions_config_trading_groups' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
            ),
            'positions_config_networks' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
            ),
            'positions_config_regions' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
            ),
            'positions_config_bdms' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'communications_customers_activities' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
                'customer_activity_id' => array(
                    'reference_table' => 'customers_activities'
                ),
            ),
            'communications_distributors_networks' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
                'distributor_network_id' => array(
                    'reference_table' => 'distributors_networks'
                ),
            ),
            'communications_positions' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
            ),
            // 'communications_sections_customers_activities' => array(
            //     'communication_section_id' => array(
            //         'reference_table' => 'communications_sections'
            //     ),
            //     'customer_activity_id' => array(
            //         'reference_table' => 'customers_activities'
            //     ),
            // ),
            // 'communications_sections_distributors_networks' => array(
            //     'communication_section_id' => array(
            //         'reference_table' => 'communications_sections'
            //     ),
            //     'distributor_network_id' => array(
            //         'reference_table' => 'distributors_networks'
            //     ),
            // ),
            'communications_sections_networks' => array(
                'communication_section_id' => array(
                    'reference_table' => 'communications_sections'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'communications_sections_positions' => array(
                'communication_section_id' => array(
                    'reference_table' => 'communications_sections'
                ),
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
            ),
            'communications_users' => array(
                'communication_id' => array(
                    'reference_table' => 'communications'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'garages_workshop_activities' => array(
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'workshop_activity_id' => array(
                    'reference_table' => 'workshop_activities'
                ),
            ),
            'groups_permissions' => array(
                'position_config_type_id' => array(
                    'reference_table' => 'positions_config_types'
                ),
            ),
            'messages' => array(
                'user_id' => array(
                    'reference_table' => 'users'
                ),
            ),
            'messages_distributors' => array(
                'message_id' => array(
                    'reference_table' => 'messages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
            ),
            'messages_files' => array(
                'message_id' => array(
                    'reference_table' => 'messages'
                ),
            ),
            'messages_garages' => array(
                'message_id' => array(
                    'reference_table' => 'messages'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
            ),
            'positions_config_aag_members' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
            ),
            'positions_config_profiles' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
            ),
            'positions_config_suppliers_categories' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
                'supplier_category_id' => array(
                    'reference_table' => 'suppliers_categories'
                ),
            ),
            'requested_changes' => array(
                'table_id' => array(
                    'reference_table' => 'logs_tables'
                ),
                'field_id' => array(
                    'reference_table' => 'logs_fields'
                ),
                'user_id' => array(
                    'reference_table' => 'users'
                ),
                'garage_id' => array(
                    'reference_table' => 'garages'
                ),
                'distributor_id' => array(
                    'reference_table' => 'distributors'
                ),
            ),
            'requested_changes_images' => array(
                'requested_change_id' => array(
                    'reference_table' => 'requested_changes'
                ),
            ),
            'roles_positions_config_types' => array(
                'role_id' => array(
                    'reference_table' => 'roles'
                ),
                'position_config_type_id' => array(
                    'reference_table' => 'positions_config_types'
                ),
            ),
            'sections_subsections_networks' => array(
                'section_subsection_id' => array(
                    'reference_table' => 'sections_subsections'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'sections_subsections_positions' => array(
                'section_subsection_id' => array(
                    'reference_table' => 'sections_subsections'
                ),
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
            ),
            'sections_subsections_trading_groups' => array(
                'section_subsection_id' => array(
                    'reference_table' => 'sections_subsections'
                ),
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
            ),
            'shortcuts_customers_activities' => array(
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'customer_activity_id' => array(
                    'reference_table' => 'customers_activities'
                ),
            ),
            'shortcuts_distributors_networks' => array(
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'distributor_network_id' => array(
                    'reference_table' => 'distributors_networks'
                ),
            ),
            'shortcuts_positions' => array(
                'shortcut_id' => array(
                    'reference_table' => 'shortcuts'
                ),
                'position_id' => array(
                    'reference_table' => 'positions'
                ),
            ),
            'tasks_codes' => array(
                'task_id' => array(
                    'reference_table' => 'tasks'
                ),
            ),
            'trading_groups_distributors_networks' => array(
                'trading_group_id' => array(
                    'reference_table' => 'trading_groups'
                ),
                'network_id' => array(
                    'reference_table' => 'distributors_networks'
                ),
            ),
            'positions_config_customer_activities' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
            ),
            'positions_config_distributor_networks' => array(
                'position_config_id' => array(
                    'reference_table' => 'positions_config'
                ),
            ),
            'conferences' => array(
                'venue_id' => array(
                    'reference_table' => 'venues'
                ),
            ),
            'conferences_delegates' => array(
                'conferences_id' => array(
                    'reference_table' => 'conferences'
                ),
                'delegate_id' => array(
                    'reference_table' => 'contacts'
                ),
                'distributors_id' => array(
                    'reference_table' => 'distributors'
                ),
                'garages_id' => array(
                    'reference_table' => 'garages'
                ),
                'room_type_id' => array(
                    'reference_table' => 'room_type'
                ),
                'guest_separate_room' => array(
                    'reference_table' => 'room_type'
                ),
                'stand_size_id' => array(
                    'reference_table' => 'stand_size'
                ),
                'suppliers_id' => array(
                    'reference_table' => 'suppliers'
                ),
            ),
            'room' => array(
                'conferences_delegates_id' => array(
                    'reference_table' => 'conferences_delegates'
                ),
            ),
            'trade_show' => array(
                'conferences_delegates_id' => array(
                    'reference_table' => 'conferences_delegates'
                ),
            ),
            'dinner' => array(
                'conferences_delegates_id' => array(
                    'reference_table' => 'conferences_delegates'
                ),
            ),
            'trainings_trainers' => array(
                'training_provider_id' => array(
                    'reference_table' => 'trainings_providers'
                ),
            ),
            'trainings_courses' => array(
                'course_type_id' => array(
                    'reference_table' => 'courses_types'
                ),
                'training_provider_id' => array(
                    'reference_table' => 'trainings_providers'
                ),
            ),
            'trainings_planned_courses' => array(
                'training_course_id' => array(
                    'reference_table' => 'trainings_courses'
                ),
                'training_trainer_id' => array(
                    'reference_table' => 'trainings_trainers'
                ),
                'venue_id' => array(
                    'reference_table' => 'venues'
                ),
            ),
            'trainings_delegates' => array(
                'training_planned_course_id' => array(
                    'reference_table' => 'trainings_planned_courses'
                ),
                'garage_contact_staff_id' => array(
                    'reference_table' => 'garages_contacts_staff'
                ),
                'reason_delegate_id' => array(
                    'reference_table' => 'reasons_delegates'
                ),
                'network_id' => array(
                    'reference_table' => 'networks'
                ),
            ),
            'trainings_credits_networks' => array(
                'garage_network_id' => array(
                    'reference_table' => 'garages_networks'
                ),
                'contact_id' => array(
                    'reference_table' => 'contacts'
                ),
                'training_planned_course_id' => array(
                    'reference_table' => 'trainings_planned_courses'
                ),
            ),
            'garages_values_adds' => array(
                'garage_id' => array(
                    'reference_table' => 'garages',
                ),
                'value_add_id' => array(
                    'reference_table' => 'values_adds',
                ),
                'billing_schedule_id' => array(
                    'reference_table' => 'billings_schedules'
                ),
            ),
        );
    }

    private function _getAlterFormatFields()
    {
        return array(/*'tabla' => array(
                'campo' => array(
                    'format' => 'DECIMAL(12, 2)',
                    'null' => true,
                    'after' => 'campo_anterior',
                ),
            ),*/);
    }

    private function _crearForeignKeys($foreign_keys)
    {
        foreach ($foreign_keys as $tabla => $foreign_key) {
            $this->_crearForeignKey($tabla, $foreign_key);
        }

        // we create like this because it's too long.
        $db = ConnectionManager::getDataSource($this->connection);
        // FK between: distributors_customer_activities_workshops && distributors_customer_activities
        $db->fetchAll(
            'ALTER TABLE `distributors_customer_activities_workshops` ADD CONSTRAINT `FK_activities_workshops_customer_activities` FOREIGN KEY (`distributor_customer_activity_id`) REFERENCES `distributors_customer_activities` (`id`);'
        );
        // FK between: distributors_customer_activities_workshops && workshop_activities
        $db->fetchAll(
            'ALTER TABLE `distributors_customer_activities_workshops` ADD CONSTRAINT `FK_activities_workshops_workshop_activities` FOREIGN KEY (`workshop_activity_id`) REFERENCES `workshop_activities` (`id`);'
        );
    }

    private function _crearForeignKey($tabla, $foreign_key)
    {
        $db = ConnectionManager::getDataSource($this->connection);
        foreach ($foreign_key as $field => $options) {
            $db->fetchAll(
                'ALTER TABLE ' . $tabla .
                    ' ADD CONSTRAINT FK_' . (isset($options['alias']) ? $options['alias'] : $tabla . '_' . $field) . ' FOREIGN KEY (' . $field . ')
                    REFERENCES ' . $options['reference_table'] . ' (id);'
            );
        }
    }

    private function _editarFormatFields($fields)
    {
        foreach ($fields as $tabla => $field) {
            $this->_editarFormatField($tabla, $field);
        }
    }

    private function _editarFormatField($tabla, $field)
    {
        $db = ConnectionManager::getDataSource($this->connection);
        foreach ($field as $field => $options) {
            $db->fetchAll(
                'ALTER TABLE ' . $tabla .
                    ' CHANGE COLUMN ' . $field .
                    ' ' . $field .
                    ' ' . $options['format'] .
                    ' ' . ((!isset($options['null']) || $options['null']) ? 'NULL' : 'NOT NULL') .
                    ' AFTER ' . $options['after']
            );
        }
    }

    public $emails = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'from_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'from' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'to' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'cc' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'bcc' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'subject' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sent' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'retries' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10),
        'notification_error' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'last_error_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'view_vars' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'template' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'attachments' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $emails_daily = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'from' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'to' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'cc' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'bcc' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'subject' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sent' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'action' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'view_vars' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'attachments' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $config_sections = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Config_sections', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $config = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'section_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Config', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tooltip_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tooltip_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tooltip_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tooltip_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Config', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $groups_permissions_permissions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'group_permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_groups_permissions_permissions_groups_permissions' => array('column' => 'group_permission_id', 'unique' => 0),
            'FK_groups_permissions_permissions_permissions' => array('column' => 'permission_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $groups_permissions_roles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'group_permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_groups_permissions_roles_groups_permissions' => array('column' => 'group_permission_id', 'unique' => 0),
            'FK_groups_permissions_roles_roles' => array('column' => 'role_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $groups_permissions_users = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'group_permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_groups_permissions_users_groups_permissions' => array('column' => 'group_permission_id', 'unique' => 0),
            'FK_groups_permissions_users_users' => array('column' => 'user_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $permissions_roles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_permissions_roles_permissions' => array('column' => 'permission_id', 'unique' => 0),
            'FK_permissions_roles_roles' => array('column' => 'role_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $permissions_users = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'exclude_permission' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_permissions_users_permissions' => array('column' => 'permission_id', 'unique' => 0),
            'FK_permissions_users_users' => array('column' => 'user_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_permissions_exception = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'exclude' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_permissions_users_permissions' => array('column' => 'permission_id', 'unique' => 0),
            'FK_permissions_users_users' => array('column' => 'user_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Positions_config_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Distributors_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'position_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'position_config_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'group_permission_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'all_networks' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_regions' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_trading_groups' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_distributor_networks' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_aag_members' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_customer_activities' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_profiles' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'all_suppliers_categories' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_aag_members = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'aag_member' => array('type' => 'integer', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_customer_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_distributor_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_profiles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'profile_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );





    public $users_master_key = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
        'master_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'modification_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_recover_passwords = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'new_user' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $logs_changes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'table_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'field_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255, 'key' => 'index'),
        'group_permission_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'position_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'old_value' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'new_value' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $logs_fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'table_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Logs_fields', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $logs_tables = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Logs_tables', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $permissions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Permissions', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'grouping_permission_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'position_config_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'FK_permissions_groupings_permissions' => array('column' => 'grouping_permission_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $groupings_permissions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Groupings_permissions', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $groups_permissions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'position_config_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Groups_permissions', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $turnovers = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Turnovers', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $courtesy_car_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Courtesy_car_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );


    public $distributors_customer_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'type' => array('type' => 'boolean', 'null' => true, 'default' => 0, 'comment' => '0-Distributor / 1-Workshop'),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'modification_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $workshop_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Workshop_activities', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_customer_activities_workshops = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'distributor_customer_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'workshop_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'activity_details' => array('type' => 'string', 'null' => true, 'default' => true, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_distributors_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'join_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'left_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_figures = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_no' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'figures' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_figures_details = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_no' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'figures_details' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_kpis = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_no' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'kpis' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_contacts_bdm = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_comments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_contacts_staff = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_contacts_general_branch_manager = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_visit_frequencies = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_comments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_figures = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_no' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'figures' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_contacts_lists = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_list_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_figures_details = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_no' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'figures_details' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_kpis = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'customer_no' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'kpis' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );


    public $garages_contacts_bdm = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_contacts_staff = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_contacts_general_branch_manager = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_distributors = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'principal' => array('type' => 'boolean', 'null' => true, 'default' => false),
        'order' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_software = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'software_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'software_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'supplier_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'software_manufacture_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'modification_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'version' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'password' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $contacts_contacts_lists = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_list_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_contacts_lists = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_list_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_contacts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tasks_contacts_lists = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'contact_list_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'task_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tasks_users = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'task_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tasks_files = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'task_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tasks_codes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'task_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'office_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    );

    public $tasks_garages = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'task_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'completed' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'completed_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
    );

    public $tasks_distributors = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'task_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'completed' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'completed_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
    );

    public $tasks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'body' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'task_status_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'mandatory' => array('type' => 'boolean', 'null' => true, 'default' => ConstantsBooleans::NO),
        'resolve_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'limit_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'user_assigned_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_creation_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'reason' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tasks_status = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Tasks_status', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_files = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_comments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_codes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'office_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    );

    public $appointments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'appointment_feeling_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'appointment_status_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'appointment_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'visit_contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'visit_contact_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'date' => array('type' => 'date', 'null' => false, 'default' => null),
        'reminder' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'latitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'longitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'end_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'start_time' => array('type' => 'time', 'null' => true, 'default' => null),
        'end_time' => array('type' => 'time', 'null' => true, 'default' => null),
        'description' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'mtd' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'qtd' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ytd' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'feedback' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'feedback_user_modification' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'feedback_date_creation' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'requires_follow_up' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'user_assigned_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_creation_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'date' => array('column' => 'date', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_routes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'route_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'order' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'start_time' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'end_time' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    );

    public $distributors_routes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'route_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'order' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'start_time' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'end_time' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),

    );

    public $routes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'user_creation_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_assigned_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Shortcuts_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'single' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::NO),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_distributors_shortcuts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'network_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'parameter_value_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'parameter_value_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'fav' => array('type' => 'boolean', 'null' => true, 'default' => ConstantsBooleans::NO),
        'creation_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_roles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_positions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'position_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts_customers_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'shortcut_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $shortcuts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tooltip' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'shortcut_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'start_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::YES),
        'is_sso' => array('type' => 'boolean', 'null' => true, 'default' => ConstantsBooleans::NO),
        'parameter_name_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'parameter_name_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'fixed' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::YES),
        'code' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'without_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_distributor_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_activity' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_yes' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_no' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $sections_subsections = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Sections_subsections', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'without_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_distributor_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_activity' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_yes' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_no' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_files = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'communication_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'communication_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'communication_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'communication_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_positions = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'position_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_customers_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'subtitle' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'section_subsection_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::YES),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'without_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_distributor_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_activity' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_yes' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_no' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'is_popup' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'start_date_popup' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date_popup' => array('type' => 'date', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $messages_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => true, 'default' => 'Bd.Messages_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $messages = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'subject' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'date_sent' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'creation_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $messages_garages = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'message_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'date_read' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $messages_distributors = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'message_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'date_read' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $messages_files = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'message_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );


    public $communications_sections = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => true, 'default' => 'Bd.Communications_sections', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'without_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_distributor_networks' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'without_activity' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_yes' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'aag_member_no' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'scrolling' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'visual' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $services_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_services = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'service_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'modification_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_contracts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'start_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'leaving_reason' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'leaving_reason_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'modification_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $associations_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Associations_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'account_number' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'abbreviation' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'client_type' => array('type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'head_office' => array('type' => 'boolean', 'null' => true, 'default' => false),
        'subsidiary' => array('type' => 'boolean', 'null' => true, 'default' => false),
        'aag_member' => array('type' => 'boolean', 'null' => true, 'default' => false),
        'distributor_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
        'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'trading_as' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'fax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'web' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address3' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address4' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'latitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'longitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'town' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'province_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
        'postcode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sales_area_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
        'monday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'last_visit' => array('type' => 'date', 'null' => true, 'default' => null),
        // 'reg_number' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of UK'),
        // 'rebate_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        // 'association_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'association_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        // 'currency' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        // 'MAMID' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of UK'),
        'VAT_number' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'detax_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of France'),
        'siret' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of France'),
        'credit_watch' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'trading_group_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index', 'comment' => 'Parent Account'),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'modification_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_activities_primary = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Distributors_activities_primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $trading_groups_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $trading_groups_distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image address of the network icon'),
        'web' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'If 1, Trading Group is Independent, if 0 is subsidiary'),
        'is_cv' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '0 is LV, 1 is CV , Null are both'),
        'primary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'primary_font_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'primary_background_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_font_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_background_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tertiary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_active' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'menu_color' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'menu_background_color' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_exito' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_fallo' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_informacion' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_disabled' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'independent' => array('type' => 'boolean', 'null' => true, 'default' => true),
        'creation_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $associations = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'trading_group_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'supplier_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'network_contract_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'contract_sent_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'contract_received_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'contract_start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        // 'date_on_hold' => array('type' => 'date', 'null' => true, 'default' => null),
        // 'reason_on_hold' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'contract_end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'reason_leaving_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        // 'leaving_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'creation_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'modification_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'last' => array('type' => 'boolean', 'null' => true, 'default' => true),
        'dd_active' => array('type' => 'boolean', 'null' => false, 'default' => false),
        'annex_detail_id' => array('type' => 'integer', 'null' => true, 'default' => true, 'length' => 10),
        'credit' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '11', 'unsigned' => false),
        'default_credit' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '11', 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'contract_start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'contract_end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'reason_leaving_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'modification_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'last' => array('type' => 'boolean', 'null' => true, 'default' => true),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $networks_contract_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Networks_contract_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $networks_contacts_bdm = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_networks_contacts_bdm = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image address of the network icon'),
        'image_pin' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image of the network pin'),
        'image_cluster' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image of the network cluster'),
        'web' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'network_type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8',),
        'primary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'primary_font_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'primary_background_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_font_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_background_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_active' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tertiary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'menu_color' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'menu_background_color' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_exito' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_fallo' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_informacion' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_disabled' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'internal' => array('type' => 'boolean', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'ref_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'credit' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'date_restarting_credit' => array('type' => 'date', 'null' => false, 'default' => null),
        'training' => array('type' => 'tinyinteger', 'null' => true, 'default' => 0, 'length' => 1),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'image' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image address of the network icon'),
        'image_pin' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image of the network pin'),
        'image_cluster' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image of the network cluster'),
        'web' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'network_type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8',),
        'primary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'primary_font_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'primary_background_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_font_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'secondary_background_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_active' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tertiary_color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'menu_color' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'menu_background_color' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_active' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_exito' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_fallo' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_informacion' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color_disabled' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $networks_statuses = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_statuses = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_services = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'service_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $services = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Services', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'international_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image address of the service icon'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_vehicle_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'vehicle_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $vehicle_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'international_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Vehicle_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image address of the service icon'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_vehicles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'vehicle_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_specialist_makes = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'vehicle_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $vehicles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Vehicles', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );


    public $garages_files = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'principal' => array('type' => 'boolean', 'null' => true, 'default' => false, 'comment' => '1 for the main facade'),
        'web' => array('type' => 'boolean', 'null' => true, 'default' => false, 'comment' => '1 is seen in the frame or web'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'principal' => array('type' => 'boolean', 'null' => true, 'default' => false, 'comment' => '1 for the main facade'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_software = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'software_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'software_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'supplier_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'software_manufacture_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'version' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'password' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'billing_schedule_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'amount' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '11,2', 'key' => 'index'),
        'member_pay' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11, 'key' => 'index'),
        'garage_pay' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11, 'key' => 'index'),
        'billed_by_aag' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'number_subscription' => array('type' => 'integer', 'null' => true, 'default' => 0, 'length' => 10, 'key' => 'index'),
        'online_ordering' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $software_manufactures = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Sotware_manufactures', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $software_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Software_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $software = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Software', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_equipments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'equipment_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'equipment_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'supplier_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'brand_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'billing_schedule_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'amount' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '11,2', 'key' => 'index'),
        'member_pay' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11, 'key' => 'index'),
        'garage_pay' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11, 'key' => 'index'),
        'billed_by_aag' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $equipments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Equipments', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $equipments_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Equipments_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_brands = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'brand_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $insurance_agreements = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_employees = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'employee_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'number' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $employee_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Employee_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_campaign = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'index'),
        'garage_campaign_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'index'),
        'number' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $campaign_entries = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Campaign_entries', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $order_products = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Order_products', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_product = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'order_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'index'),
        'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'index'),
        'quantity' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $labels_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $distributors_labels = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'label_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'modification_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $customers_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Customers_activities', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_customers_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'order' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $leaving_reason_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Leaving_reason_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $hold_reason_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Hold_reason_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $logistic_centers = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Logistics_centers', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_websites = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'website_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tagline' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $websites = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'g_number_id' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ref_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'business_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'client_type' => array('type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'VAT_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of France'),
        'siret' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of France'),
        'insurance_agreement_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
        'status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'repairmaintenance' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'affiliation_assembly' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'documents_legal' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'diesel_liability' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'turnover_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
        'flat_rate' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'comment' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address3' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address4' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'latitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'longitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'town' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'province_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'index'),
        'postcode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'mobile' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'phone_international' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'fax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'web' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'service_24h_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'monday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'tuesday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'wednesday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'thursday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'friday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'saturday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_open_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_closed_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_open_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sunday_closed_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'visit_frequency' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'visit_monday' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'visit_tuesday' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'visit_wednesday' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'visit_thursday' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'visit_friday' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'last_visit' => array('type' => 'date', 'null' => true, 'default' => null),
        'ramps' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'MOT_bays' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Number of MOT Bays'),
        'spend_this_month' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'spend_last_month' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'spend_12_month' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'spend_projected' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'foundation_year' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Years running'),
        'lead_source' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'marketing_email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'interests' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'modification_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'repairmaintenance' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'current_charge' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'member_pays' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'garage_pays' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'fleet_work_direction' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'fleet_mot' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'fleet_labour_rate' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'long_life_oil_price_b2b' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'standard_oil_price_b2b' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'collection_delivery_b2b' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'retail_mot' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'retail_labour_rate' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'long_life_oil_price_b2c' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'standard_oil_price_b2c' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'collection_delivery_b2c' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'ev_charge_points' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10, 'unsigned' => false),
        'kwh_charging_retail_price' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'kwh_charging_fleet_price' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'ev_ppe_audited_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
        'courtesy_car' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
    );

    public $garages_courtesy_car_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'courtesy_car_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_workshop_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'workshop_activity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );


    public $appointments_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Appointments_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'is_event' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '0 is visits, 1 is event'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_status = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Appointments_status', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'is_event' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '0 is visits, 1 is event'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_feelings = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Appointments_feelings', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'color' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'icon' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $alerts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'alert_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'rm_alert_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'body' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'read' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_reassignments = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'user_id_origin' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'user_id_destination' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'garage_contact_bdm_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_contact_bdm_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'contact_contact_list_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'task_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'appointment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'route_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'date_from' => array('type' => 'date', 'null' => true, 'default' => null),
        'date_to' => array('type' => 'date', 'null' => true, 'default' => null),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'permanent' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_preferences = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'preference_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'value' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $searches_distributors = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'search_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $searches_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'search_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $searches_contacts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'search_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_searches = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'false' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'user_id' => array('type' => 'integer', 'null' => true, 'false' => null, 'length' => 10, 'unsigned' => false),
        'route_id' => array('type' => 'integer', 'null' => true, 'false' => null, 'length' => 10, 'unsigned' => false),
        'network_status_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'is_garages' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'city' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'location' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'lat' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'lng' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'distance' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false),
        'last_visit' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'my_customers' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'last_use' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $contacts_lists = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'global' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'color' => array('type' => 'string', 'null' => false, 'default' => '#3668a1', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tutorials_roles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'tutorial_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $tutorials = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'title' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Image address of the service icon'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'order' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'surname' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'username' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 255, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_type' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'distributor_type' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'repairmaintenance' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::ACTIVE),
        'retries' => array('type' => 'integer', 'null' => true, 'default' => ConstantsBooleans::NO),
        'last_login' => array('type' => 'datetime', 'null' => true),
        'set_login' => array('type' => 'datetime', 'null' => true),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'username' => array('column' => 'username', 'unique' => 1),
            'role_id' => array('column' => 'role_id', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $languages = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'code' => array('type' => 'string', 'null' => false, 'length' => 2, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Code to identify the country'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Languages', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $contacts = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'title_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'position_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'logistic_center_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'mobile_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'identification_number' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index', 'comment' => 'if contact have a parent'),
        'creation_date' => array('type' => 'date', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $contacts_titles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Positions', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $roles = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Roles', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'role' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $roles_positions_config_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'role_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'position_config_type_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $alerts_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Alerts_types', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $postcode_provinces = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'postcode' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'province_code' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'province_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $provinces = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'province_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 6, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'country_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $countries = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'country_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'country_code_2' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'aag_region_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'image' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 255, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'currency' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'symbol' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $suppliers = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'code' => array('type' => 'string', 'null' => true, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'web' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url_video' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'url_channel' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'active' => array('type' => 'boolean', 'null' => false),
        'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'fax' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'town' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'postcode' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'address2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'VAT_number' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of France'),
        'siret' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Only for the installation of France'),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'edition_date' => array('type' => 'datetime', 'null' => false),
        'publication_date' => array('type' => 'date', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $suppliers_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'supplier_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'file' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $suppliers_files = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'supplier_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'supplier_category_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'file' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'active' => array('type' => 'boolean', 'null' => true, 'default' => ConstantsBooleans::NO),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $suppliers_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'supplier_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $suppliers_trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'supplier_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $suppliers_categories = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Suppliers_categories', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'uses' => array('type' => 'integer', 'null' => false, 'length' => 10, 'default' => 0),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $brands = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'supplier_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'active' => array('type' => 'boolean', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $brands_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'brand_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'file' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $products = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'brand_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'active' => array('type' => 'boolean', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $products_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'product_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'file' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $debrief_tasks_garages = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'debrief_task_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $debrief_tasks_distributors = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'debrief_task_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $debrief_tasks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'contact_list_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'user_assigned_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'title_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'title_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'title_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'title_lc' => array('type' => 'string', 'null' => true, 'default' => 'Bd.Debrief_tasks', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Debrief_tasks', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'specific_user' => array('type' => 'integer', 'null' => false, 'length' => 10, 'default' => 0),
        'uses' => array('type' => 'integer', 'null' => false, 'length' => 10, 'default' => 0),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $debrief_topics = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => true, 'default' => 'Bd.Debrief_topics', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'uses' => array('type' => 'integer', 'null' => false, 'length' => 10, 'default' => 0),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $appointments_topics = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'appointment_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'topic_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $regions = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_regions = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'region_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_bdms = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'user_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_sections_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_sections_distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'distributor_network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_sections_trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_sections_positions = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'position_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_sections_customers_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'communication_section_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $sections_subsections_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'section_subsection_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $sections_subsections_distributors_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'section_subsection_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'distributor_network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $sections_subsections_trading_groups = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'section_subsection_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $sections_subsections_positions = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'section_subsection_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'position_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $sections_subsections_customers_activities = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'section_subsection_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'customer_activity_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $positions_config_suppliers_categories = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'position_config_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'supplier_category_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $requested_changes = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'table_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'field_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'user_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'old_value' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'new_value' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'section' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'sent' => array('type' => 'boolean', 'null' => false, 'default' => 0),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $requested_changes_images = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
        'requested_change_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'source_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $communications_users = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'communication_id' => array('type' => 'integer', 'null' => true, 'length' => 10, 'key' => 'index'),
        'date_read' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $contacts_regions = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'contact_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'region_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $users_statistics = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'user_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'trading_group_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'network_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'distributor_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'section_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'section_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'article_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'),
        'article_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'device' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'ip' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $agreements = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'nombre' => array('type' => 'string', 'null' => false, 'length' => 50),
        'codigo' => array('type' => 'string', 'null' => false, 'length' => 50),
        'fecha_creacion' => array('type' => 'datetime'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $garages_agreements = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'default' => null, 'length' => 10),
        'parent_acct' => array('type' => 'string', 'default' => null, 'length' => 50),
        'fleet_agreement' => array('type' => 'string', 'default' => null, 'length' => 50),
        'fleet_reference' => array('type' => 'string', 'default' => null, 'length' => 50),
        'agreement_code' => array('type' => 'string', 'default' => null, 'length' => 50),
        'garage_ref' => array('type' => 'string', 'default' => null, 'length' => 50),
        'creation_date' => array('type' => 'datetime', 'null' => false),
        'contract_sent_date' => array('type' => 'date', 'default' => false),
        'contract_received_date' => array('type' => 'date', 'default' => false),
        'contract_start_date' => array('type' => 'date', 'default' => false),
        'contract_end_date' => array('type' => 'date', 'default' => false),
        'date_on_hold' => array('type' => 'date', 'default' => false),
        'reason_on_hold' => array('type' => 'string', 'default' => false, 'length' => 255),
        'reason_leaving' => array('type' => 'string', 'default' => false, 'length' => 255),
        'leaving_date' => array('type' => 'date', 'default' => false),
        'status' => array('type' => 'string', 'default' => false, 'length' => 255),
        'reason_leaving_id' => array('type' => 'integer', 'default' => false, 'length' => 10),
        'reason_hold_id' => array('type' => 'integer', 'default' => false, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $lists = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'length' => 50),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $annex_details = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => false, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => false, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Annex_details'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $postcodes = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'length' => 255),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $garages_b2b_postcodes = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'postcode_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $garages_b2c_postcodes = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'postcode_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $facilities = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.Facilities'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $garages_facilities = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'facility_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $value_add_suppliers = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.Value_add_suppliers'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $value_add_supplier_type = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.Value_add_supplier_type'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $garages_value_add_supplier = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'value_add_supplier_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'value_add_supplier_type_id' => array('type' => 'integer', 'null' => false, 'length' => 10),
        'to_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'from_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $venues = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'address_1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'address_2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'latitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'longitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '30,20', 'unsigned' => false),
        'town' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'postcode' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'telephone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'active' => array('type' => 'boolean', 'null' => false, 'default' => ConstantsBooleans::YES),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $conferences = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'start_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'duration' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'venue_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'status' => array('type' => 'integer', 'null' => true, 'default' => ConstantsBooleans::NO, 'length' => 1),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trade_show = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'conferences_delegates_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'weeks_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $dinner = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'conferences_delegates_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'weeks_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $room = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'conferences_delegates_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'weeks_id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $conferences_delegates = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'conferences_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'booking_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'garages_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'distributors_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'suppliers_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'delegate_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'contact' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'room_type_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'nights' => array('type' => 'integer', 'null' => true, 'default' => ConstantsBooleans::NO, 'length' => 11),
        'vegetarian' => array('type' => 'integer', 'null' => true, 'default' => ConstantsBooleans::NO, 'length' => 1),
        'diet_requirements' => array('type' => 'text', 'null' => true, 'default' => null),
        'guest_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'guest_separate_room' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'stand_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'stand_size_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
        'stand_power_required' => array('type' => 'integer', 'null' => true, 'default' => ConstantsBooleans::NO, 'length' => 1),
        'bespoke_stand_details' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'website_entry_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $room_type = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.RoomType'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $stand_size = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.StandSize'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $week = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.Week'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_credits = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'pound' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'longitude' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '10,2', 'unsigned' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_providers = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_trainers = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'training_provider_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $courses_types = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.CoursesTypes'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_courses = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'course_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'training_provider_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'duration' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'price_credit' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'cost_training' => array('type' => 'integer', 'null' => false, 'default' => 0, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'description' => array('type' => 'string', 'default' => null, 'null' => true),
        'part_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'invoice_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_planned_courses = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'training_course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'training_trainer_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'venue_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'date_from' => array('type' => 'date', 'null' => true, 'default' => null),
        'date_to' => array('type' => 'date', 'null' => true, 'default' => null),
        'starting_time' => array('type' => 'time', 'null' => true, 'default' => null),
        'duration' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'availability' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10),
        'status' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10),
        'new_imported_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'full' => array('type' => 'tinyinteger', 'null' => true, 'default' => null, 'length' => 1),
        'guid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
        'invoice_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'note' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_delegates = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'training_planned_course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'garage_contact_staff_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'is_refund_eligible' => array('type' => 'boolean', 'null' => true, 'default' => null),
        'order_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'invoice_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'cancelled' => array('type' => 'tinyinteger', 'null' => true, 'default' => 0, 'length' => 1),
        'refund_credits' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'manual_credits_calculation' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'paid_separately' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $trainings_credits_networks = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'garage_network_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'credit_spent' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'credit_given' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'credit' => array('type' => 'integer', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'creation_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
        'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'training_planned_course_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );

    public $values_adds = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_fr' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_de' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'name_lc' => array('type' => 'string', 'null' => false, 'default' => 'Bd.Campaign_entries', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $garages_values_adds = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'garage_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'value_add_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
        'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
        'version' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'billing_schedule_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'key' => 'index'),
        'amount' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '11', 'key' => 'index'),
        'member_pay' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11, 'key' => 'index'),
        'garage_pay' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 11, 'key' => 'index'),
        'billed_by_aag' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'number_subscription' => array('type' => 'integer', 'null' => true, 'default' => 0, 'length' => 10, 'key' => 'index'),
        'online_ordering' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $reasons_delegates = array(
        'id' => array('type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
        'name_en' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_fr' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_de' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 255),
        'name_lc' => array('type' => 'string', 'null' => false, 'length' => 255, 'default' => 'Bd.ReasonsDelegates'),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
    );
}
